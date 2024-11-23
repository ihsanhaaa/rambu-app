<?php

namespace App\Http\Controllers;

use App\Models\Foto;
use App\Models\Kecamatan;
use App\Models\Lokasi;
use App\Models\Rambu;
use App\Models\Status;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class RambuController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $rambus = Rambu::with('statusRambuTerbaru', 'kecamatan')->get();

        return view('rambu.index', compact('rambus'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'file' => 'required|file|mimes:json',
        ]);

        $file = $request->file('file');
        $data = json_decode(file_get_contents($file), true);

        foreach ($data['features'] as $feature) {
            $properties = $feature['properties'] ?? [];

            if (isset($properties['description'])) {
                // Memparsing `description` untuk mengekstrak informasi
                $description = $properties['description'];
                // dd($description);
                $parsedData = $this->parseDescription($description);

                // Simpan data utama ke tabel marka_jalans
                $rambu = Rambu::create([
                    'nama_rambu' => $parsedData['nama_rambu'] ?? 'Tidak Ada Nama',
                    'id_rambu' => $parsedData['id_rambu'] ?? null,
                    'kategori_rambu' => $parsedData['kategori_rambu'] ?? null,
                    'jenis_rambu' => $parsedData['jenis_rambu'] ?? null,
                    'harga' => $parsedData['harga'] ?? null,
                ]);

                // Simpan data geojson ke tabel lokasis yang terhubung dengan marka_jalan_id
                Lokasi::create([
                    'rambu_id' => $rambu->id,
                    'geojson' => json_encode($feature['geometry']),
                ]);

                Status::create([
                    'rambu_id' => $rambu->id,
                    'tgl_temuan' => Carbon::now(),
                    'label_status' => $parsedData['status'] ?? null,
                    'deskripsi' => $parsedData['deskripsi'] ?? null,
                ]);

                if ($parsedData['jenis_rambu'] == 'Bersuar') {
                    $rambu->tiang()->create([
                        'bentuk_tiang' => $parsedData['bentuk_tiang'] ?? null,
                        'tinggi_tiang' => $parsedData['tinggi_tiang'] ?? null,
                        'material_tiang' => $parsedData['material_tiang'] ?? null,
                        'alat_tambahan' => $parsedData['alat_tambahan'] ?? null,
                    ]);

                    $rambu->lensa()->create(['posisi_lensa' => $parsedData['posisi_lensa']]);

                } elseif ($parsedData['jenis_rambu'] == 'Tidak Bersuar') {
                    $rambu->tiang()->create([
                        'bentuk_tiang' => $parsedData['bentuk_tiang'] ?? null,
                        'tinggi_tiang' => $parsedData['tinggi_tiang'] ?? null,
                        'material_tiang' => $parsedData['material_tiang'] ?? null,
                        'alat_tambahan' => $parsedData['alat_tambahan'] ?? null,
                    ]);
                    $rambu->daun()->create([
                        'material_daun' => $parsedData['material_daun'] ?? null,
                        'ukuran_daun' => $parsedData['ukuran_daun'] ?? null,
                    ]);
                }
            }
        }

        return redirect()->back()->with('success', 'Data Rambu berhasil diimport');
    }

    private function parseDescription($description)
    {
        $parsedData = [];

        // Menggunakan regex untuk menangkap data dari setiap bagian description
        preg_match('/Nama Rambu:\s*([^\n]*)/', $description, $matches);
        $parsedData['nama_rambu'] = $matches[1] ?? null;
        
        preg_match('/Kategori Rambu:\s*([^\n]*)/', $description, $matches);
        $parsedData['kategori_rambu'] = $matches[1] ?? null;

        preg_match('/Deskripsi Status:\s*([^\n]*)/', $description, $matches);
        $parsedData['deskripsi'] = $matches[1] ?? null;

        preg_match('/Tgl Temuan:\s*([^\n]*)/', $description, $matches);
        $parsedData['tgl_temuan'] = isset($matches[1]) ? date('Y-m-d H:i:s', strtotime($matches[1])) : null;

        preg_match('/Status:\s*([^\n]*)/', $description, $matches);
        $parsedData['status'] = $matches[1] ?? null;

        preg_match('/Id Rambu:\s*([^\n]*)/', $description, $matches);
        $parsedData['id_rambu'] = $matches[1] ?? null;

        preg_match('/Jenis Rambu:\s*([^\n]*)/', $description, $matches);
        $parsedData['jenis_rambu'] = $matches[1] ?? null;

        preg_match('/Harga:\s*([^\n]*)/', $description, $matches);
        $parsedData['harga'] = $matches[1] ?? null;

        // tiang
        preg_match('/Bentuk Tiang:\s*([^\n]*)/', $description, $matches);
        $parsedData['bentuk_tiang'] = $matches[1] ?? null;
        
        preg_match('/Tinggi Tiang:\s*([^\n]*)/', $description, $matches);
        $parsedData['tinggi_tiang'] = $matches[1] ?? null;
        
        preg_match('/Sumber Daya:\s*([^\n]*)/', $description, $matches);
        $parsedData['alat_tambahan'] = $matches[1] ?? null;

        preg_match('/Material Tiang:\s*([^\n]*)/', $description, $matches);
        $parsedData['material_tiang'] = $matches[1] ?? null;
        
        // daun
        preg_match('/Material Daun Rambu:\s*([^\n]*)/', $description, $matches);
        $parsedData['material_daun'] = $matches[1] ?? null;

        preg_match('/Diameter Daun Rambu:\s*([^\n]*)/', $description, $matches);
        $parsedData['ukuran_daun'] = $matches[1] ?? null;
        
        // lensa
        preg_match('/Posisi Lensa:\s*([^\n]*)/', $description, $matches);
        $parsedData['posisi_lensa'] = $matches[1] ?? null;

        return $parsedData;
    }

    public function storeStatus(Request $request, $id)
    {
        $request->validate([
            'tgl_temuan' => 'required|date',
            'label_status' => 'nullable|string',
            'deskripsi' => 'nullable|string'
        ]);

        $rambu = Rambu::findOrFail($id);

        // Buat instance Status baru
        $status = new Status();
        $status->rambu_id = $rambu->id;
        $status->tgl_temuan = $request->tgl_temuan;
        $status->label_status = $request->label_status;
        $status->deskripsi = $request->deskripsi;
        $status->save();

        return redirect()->back()->with('success', 'Data status Rambu berhasil ditambahkan');
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        $rambu = Rambu::with('fotos', 'statuses', 'tiang', 'lensa', 'daun')->findOrFail($id);

        return view('rambu.show', compact('rambu'));
    }

    public function showMap()
    {
        $rambus = DB::table('rambus')
            ->leftJoin('lokasis', 'rambus.id', '=', 'lokasis.rambu_id')
            ->leftJoin('statuses', function ($join) {
                $join->on('rambus.id', '=', 'statuses.rambu_id')
                    ->whereRaw('statuses.tgl_temuan = (SELECT MAX(t2.tgl_temuan) FROM statuses t2 WHERE t2.rambu_id = rambus.id)');
            })
            // Menambahkan join untuk tabel fotos untuk mengambil foto terbaru
            ->leftJoin('fotos', function($join) {
                $join->on('rambus.id', '=', 'fotos.rambu_id')
                    ->whereRaw('fotos.created_at = (SELECT MAX(fotos.created_at) FROM fotos WHERE fotos.rambu_id = rambus.id)');
            })
            ->select(
                'rambus.*',
                'lokasis.geojson',
                'statuses.label_status as latest_status',
                'statuses.tgl_temuan as latest_tgl_temuan',
                'statuses.deskripsi as latest_deskripsi',
                'fotos.foto_path' // Menyertakan foto terbaru
            )
            ->get();


        $kecamatans = Kecamatan::all();

        // dd($rambus);

        return view('rambu.map', compact('rambus', 'kecamatans'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id)
    {
        $rambu = Rambu::findOrFail($id);
        $kecamatans = Kecamatan::all();
        
        return view('rambu.edit', compact('rambu', 'kecamatans'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $request->validate([
            'nama_rambu' => 'required|string|max:255',
            'id_rambu' => 'nullable|string|max:255',
            'kategori_rambu' => 'nullable|string|max:255',
            'jenis_rambu' => 'nullable|string|max:255',
            'harga' => 'nullable|string|max:255',
            'kecamatan_id' => 'required|exists:kecamatans,id',
        ]);

        $rambu = Rambu::findOrFail($id);
        $rambu->update([
            'nama_rambu' => $request->nama_rambu,
            'id_rambu' => $request->id_rambu,
            'kategori_rambu' => $request->kategori_rambu,
            'jenis_rambu' => $request->jenis_rambu,
            'harga' => $request->harga,
            'kecamatan_id' => $request->kecamatan_id,
        ]);

        return redirect()->route('data-rambu.show', $id)->with('success', 'Data CCTV berhasil diupdate.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $rambu = Rambu::findOrFail($id);
        $rambu->delete();

        return redirect()->route('data-rambu.peta')->with('success', 'Data berhasil dihapus.');
    }

    public function uploadPhoto(Request $request, $id)
    {
        $request->validate([
            'photo' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048'
        ]);

        $file = $request->file('photo');

        if ($file) {
            // Menentukan path untuk penyimpanan
            $path = 'foto-rambu/';
            $new_name = 'rambu-' . $id . '-' . date('Ymd') . '-' . uniqid() . '.' . $file->getClientOriginalExtension();
    
            // Memindahkan file ke folder yang ditentukan
            $file->move(public_path($path), $new_name);
    
            // Menyimpan path ke database
            Foto::create([
                'cctv_id' => $id,
                'foto_path' => $path . $new_name
            ]);
    
            return response()->json(['message' => 'Foto berhasil diunggah.']);
        }

        return response()->json(['message' => 'Gagal mengunggah foto.'], 500);
    }

    public function uploadPhotoDetail(Request $request, $id)
    {
        $request->validate([
            'photo' => 'required',
            'photo.*' => 'image|mimes:jpeg,png,jpg,gif|max:2048'
        ]);

        $rambu = Rambu::findOrFail($id);
        
        // Proses setiap file yang diunggah
        foreach ($request->file('photo') as $file) {
            // Menentukan path dan nama unik untuk setiap file
            $path = 'foto-rambu/';
            $new_name = 'rambu-' . $id . '-' . date('Ymd') . '-' . uniqid() . '.' . $file->getClientOriginalExtension();
        
            // Memindahkan file ke folder yang ditentukan
            $file->move(public_path($path), $new_name);
        
            // Menyimpan path ke database
            Foto::create([
                'rambu_id' => $id,
                'foto_path' => $path . $new_name
            ]);
        }
    
        return redirect()->route('data-rambu.show', $rambu->id)->with('success', 'Foto berhasil diupload');
    }
}
