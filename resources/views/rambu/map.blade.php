@extends('layouts.app')

@section('title')
    Peta Rambu
@endsection

@section('content')
    @push('css-plugins')
        <link rel="stylesheet" href="https://unpkg.com/leaflet@1.7.1/dist/leaflet.css">

        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">

        <style>
            .carousel-image {
                width: 100%;
                height: auto;
                max-width: 320px;
                max-height: 320px;
                object-fit: cover;
            }
        </style>
    @endpush

    <!-- Begin page -->
    <div id="layout-wrapper">

        <!-- header -->
        @include('components.navbar_admin')
        
        <!-- Start right Content here -->
        <div class="main-content">

            <div class="page-content">
                <div class="container-fluid">
                    
                    <!-- start page title -->
                    <div class="row">
                        <div class="col-12">
                            <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                                <h4 class="mb-sm-0">Peta Rambu</h4>

                                <div class="page-title-right">
                                    <ol class="breadcrumb m-0">
                                        <li class="breadcrumb-item"><a href="javascript: void(0);">Dashboard</a></li>
                                        <li class="breadcrumb-item"><a href="javascript: void(0);">Rambu</a></li>
                                        <li class="breadcrumb-item active">Peta</li>
                                    </ol>
                                </div>

                            </div>
                        </div>
                    </div>
                    <!-- end page title -->

                    @if (count($errors) > 0)
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            @foreach ($errors->all() as $error)
                                <strong>{{ $error }}</strong><br>
                            @endforeach
                        </div>
                    @endif

                    @if ($message = Session::get('success'))
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                            <strong>Success!</strong> {{ $message }}.
                        </div>
                    @endif
                    
                    <div class="row">
                        <div class="col-lg-12">
                            <div class="card">
                                <div class="card-body">
                                    <div>
                                        <div id="map" style="width: 100%; height: 500px;"></div>
                                    </div>
                                </div>
                            </div> 
                        </div>
                    </div>
                    <!-- end row -->

                </div>
                
            </div>
            <!-- End Page-content -->
           
            <!-- footer -->
            @include('components.footer_admin')
            
        </div>
        <!-- end main content-->

    </div>
    <!-- END layout-wrapper -->

    @push('javascript-plugins')
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-MrcW6ZMFYlzcLA8Nl+NtUVF0sA7MsXsP1UyJoMp4YLEuNSfAP+JcXn/tWtIaxVXM" crossorigin="anonymous"></script>


        <script src="https://unpkg.com/leaflet@1.7.1/dist/leaflet.js"></script>
        <script>
            // Membuat peta
            const map = L.map('map').setView([-0.05503, 109.3491], 13);
        
            // Menambahkan tile layer ke peta (misalnya OpenStreetMap)
            L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors'
            }).addTo(map);
        
            // Data rambu dari controller
            var rambus = @json($rambus);

            // Layer Group untuk masing-masing status
            var baikLayer = L.layerGroup().addTo(map);
            var perluTindakanLayer = L.layerGroup().addTo(map);
            var rencanaLayer = L.layerGroup().addTo(map);
            var hilangLayer = L.layerGroup().addTo(map);
            var defaultLayer = L.layerGroup().addTo(map);

            // Fungsi untuk menentukan ikon berdasarkan status
            function getIconByStatus(status) {
                switch (status) {
                    case 'Baik':
                        return L.icon({
                            iconUrl: 'baik-icon.png',
                            iconSize: [30, 30], // ukuran ikon
                            iconAnchor: [15, 30], // anchor pada ikon
                            popupAnchor: [0, -30] // posisi popup
                        });
                    case 'Perlu Tindakan':
                        return L.icon({
                            iconUrl: 'perlu-tindakan-icon.png',
                            iconSize: [30, 30],
                            iconAnchor: [15, 30],
                            popupAnchor: [0, -30]
                        });
                    case 'Rencana':
                        return L.icon({
                            iconUrl: 'rencana-icon.png',
                            iconSize: [30, 30],
                            iconAnchor: [15, 30],
                            popupAnchor: [0, -30]
                        });
                    case 'Hilang':
                        return L.icon({
                            iconUrl: 'hilang-icon.png',
                            iconSize: [30, 30],
                            iconAnchor: [15, 30],
                            popupAnchor: [0, -30]
                        });
                    default: // Jika status kosong atau tidak terdefinisi
                        return L.icon({
                            iconUrl: 'default-icon.png',
                            iconSize: [30, 30],
                            iconAnchor: [15, 30],
                            popupAnchor: [0, -30]
                        });
                }
            }

        
            // Looping melalui setiap rambu dan menambahkan marker
            rambus.forEach(function(rambu) {
                if (rambu.geojson) {
                    // Parsing geojson untuk mengambil koordinat (longitude, latitude)
                    var geojsonData = JSON.parse(rambu.geojson);
                    var coordinates = geojsonData.coordinates;
        
                    // Menentukan ikon berdasarkan status rambu
                    var icon = getIconByStatus(rambu.latest_status);

                    // Menambahkan marker dengan ikon yang sesuai
                    var marker = L.marker([coordinates[1], coordinates[0]], { icon: icon }).addTo(map);
        
                    // Menyiapkan tombol detail untuk membuka halaman lebih lanjut
                    const detailButton = `<a href="{{ url('/data-rambu') }}/${rambu.id}" class="btn btn-sm btn-info text-white mx-1" target="_blank"><i class="fas fa-eye"></i> Lihat Detail</a>`;
        
                    // Menyiapkan gambar jika ada
                    var pictureUrl = rambu.foto_path ? `{{ asset('') }}${rambu.foto_path}` : 'Tidak ada foto';
        
                    // Menyiapkan konten popup
                    const popupContent = `
                        <div class="carousel-container mb-3">
                            ${rambu.foto_path ? `<img src="${pictureUrl}" class="carousel-image" alt="Foto Rambu">` : '<p class="text-center">Tidak ada foto</p>'}
                        </div>
                        <table class="table table-bordered">
                            <tr><th>Nama Rambu</th><td>${rambu.nama_rambu || 'N/A'}</td></tr>
                            <tr><th>Kategori Rambu</th><td>${rambu.kategori_rambu || 'N/A'}</td></tr>
                            <tr><th>Jenis Rambu</th><td>${rambu.jenis_rambu || 'N/A'}</td></tr>
                            <tr><th>Harga</th><td>${rambu.harga || 'N/A'}</td></tr>
                            <tr><th>Status Terbaru</th><td>${rambu.latest_status || 'N/A'}</td></tr>
                            <tr><th>Tanggal Temuan</th><td>${rambu.latest_tgl_temuan || 'N/A'}</td></tr>
                            <tr><th>Deskripsi</th><td>${rambu.latest_deskripsi || 'N/A'}</td></tr>
                            <tr>
                                <td colspan="2" class="text-center">
                                    <div class="d-flex justify-content-center gap-2">
                                        ${detailButton}
                                    </div>
                                </td>
                            </tr>
                        </table>
                    `;
        
                    // Mengikat popup ke marker
                    marker.bindPopup(popupContent);

                    // Menambahkan marker ke layer group yang sesuai berdasarkan status
                    switch (rambu.latest_status) {
                        case 'Baik':
                            marker.addTo(baikLayer);
                            break;
                        case 'Perlu Tindakan':
                            marker.addTo(perluTindakanLayer);
                            break;
                        case 'Rencana':
                            marker.addTo(rencanaLayer);
                            break;
                        case 'Hilang':
                            marker.addTo(hilangLayer);
                            break;
                        default:
                            marker.addTo(defaultLayer);
                    }
                }
            });

            const overlayMaps = {
                "Baik": baikLayer,
                "Perlu Tindakan": perluTindakanLayer,
                "Rencana": rencanaLayer,
                "Hilang": hilangLayer,
                "Status Tidak Tersedia": defaultLayer
            };

            L.control.layers(null, overlayMaps, { collapsed: false }).addTo(map);

        </script>
    @endpush
@endsection