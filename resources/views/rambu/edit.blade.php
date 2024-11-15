@extends('layouts.app')

@section('title')
    Edit Data Rambu
@endsection

@section('content')
    @push('css-plugins')
        
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
                                <h4 class="mb-sm-0">Edit Data Rambu</h4>

                                <div class="page-title-right">
                                    <ol class="breadcrumb m-0">
                                        <li class="breadcrumb-item"><a href="javascript: void(0);">Dashboard</a></li>
                                        <li class="breadcrumb-item"><a href="{{ route('data-rambu.index') }}">Rambu</a></li>
                                        <li class="breadcrumb-item"><a href="javascript: void(0);">Detail Rambu</a></li>
                                        <li class="breadcrumb-item active">Edit Rambu</li>
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
        
                                    <h4 class="card-title">Edit Data Rambu {{ $rambu->nama_rambu }}</h4>

                                    <form action="{{ route('data-rambu.update', $rambu->id) }}" method="POST">
                                        @csrf
                                        @method('PUT')
                                        
                                        <div class="row mb-3">
                                            <label for="nama_rambu" class="col-sm-2 col-form-label">Nama Rambu</label>
                                            <div class="col-sm-10">
                                                <input class="form-control @error('nama_rambu') is-invalid @enderror" type="text" id="nama_rambu" name="nama_rambu" value="{{ old('nama_rambu', $rambu->nama_rambu ?? '') }}" required>
                                                @error('nama_rambu')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>

                                        <div class="row mb-3">
                                            <label for="id_rambu" class="col-sm-2 col-form-label">Jenis Rambu</label>
                                            <div class="col-sm-10">
                                                <input class="form-control @error('id_rambu') is-invalid @enderror" type="text" id="id_rambu" name="id_rambu" value="{{ old('id_rambu', $rambu->id_rambu ?? '') }}" required>
                                                @error('id_rambu')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>

                                        <div class="row mb-3">
                                            <label for="kategori_rambu" class="col-sm-2 col-form-label">Panel Rambu</label>
                                            <div class="col-sm-10">
                                                <input class="form-control @error('kategori_rambu') is-invalid @enderror" type="text" id="kategori_rambu" name="kategori_rambu" value="{{ old('kategori_rambu', $rambu->kategori_rambu ?? '') }}" required>
                                                @error('kategori_rambu')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>

                                        <div class="row mb-3">
                                            <label for="jenis_rambu" class="col-sm-2 col-form-label">Jenis Rambu</label>
                                            <div class="col-sm-10">
                                                <input class="form-control @error('jenis_rambu') is-invalid @enderror" type="text" id="jenis_rambu" name="jenis_rambu" value="{{ old('jenis_rambu', $rambu->jenis_rambu ?? '') }}" required>
                                                @error('jenis_rambu')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>

                                        <div class="row mb-3">
                                            <label for="harga" class="col-sm-2 col-form-label">Harga</label>
                                            <div class="col-sm-10">
                                                <input class="form-control @error('harga') is-invalid @enderror" type="text" id="harga" name="harga" value="{{ old('harga', $rambu->harga ?? '') }}" required>
                                                @error('harga')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>

                                        <div class="row mb-3">
                                            <label for="kecamatan_id" class="col-sm-2 col-form-label">Kecamatan</label>
                                            <div class="col-sm-10">
                                                <select class="form-control @error('kecamatan_id') is-invalid @enderror" id="kecamatan_id" name="kecamatan_id" required>
                                                    <option value="">-- Pilih Kecamatan --</option>
                                                    @foreach($kecamatans as $kecamatan)
                                                        <option value="{{ $kecamatan->id }}" {{ old('kecamatan_id', $rambu->kecamatan_id ?? '') == $kecamatan->id ? 'selected' : '' }}>
                                                            {{ $kecamatan->nama_kecamatan }}
                                                        </option>
                                                    @endforeach
                                                </select>
                                                @error('kecamatan_id')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>

                                        <button type="submit" class="btn btn-primary">Update Data Rambu</button>
                                    </form>


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
        
    @endpush
@endsection