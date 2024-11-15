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
            // Inisialisasi peta
            var map = L.map('map').setView([-0.03568, 109.33296], 13);
        
            // Tambahkan tile layer OpenStreetMap
            var osmLayer = L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                maxZoom: 19,
                attribution: '© OpenStreetMap'
            }).addTo(map);
        
            // Tambahkan tile layer Esri World Imagery
            var esriLayer = L.tileLayer('https://server.arcgisonline.com/ArcGIS/rest/services/World_Imagery/MapServer/tile/{z}/{y}/{x}', {
                attribution: 'Tiles © Esri'
            });
        
            // Tambahkan grup layer untuk kategori_rambu
            var peringatanGroup = L.layerGroup();
            var laranganGroup = L.layerGroup();
            var perintahGroup = L.layerGroup();
            var petunjukGroup = L.layerGroup();
            var lampu3Group = L.layerGroup();
            var lampu2Group = L.layerGroup();
            var lampu1Group = L.layerGroup();
            var countdownGroup = L.layerGroup();
        
            // Tambahkan grup layer untuk jenis_rambu
            var bersuarGroup = L.layerGroup();
            var tidakBersuarGroup = L.layerGroup();
        
            // Data rambus dari server
            var rambus = @json($rambus);
        
            // Custom icons for each category
            var peringatanIcon = L.icon({
                iconUrl: 'placeholder.png',
                iconSize: [32, 32],
                iconAnchor: [16, 32],
                popupAnchor: [0, -32]
            });

            var laranganIcon = L.icon({
                iconUrl: 'alert.png',
                iconSize: [32, 32],
                iconAnchor: [16, 32],
                popupAnchor: [0, -32]
            });

            var perintahIcon = L.icon({
                iconUrl: '/path/to/perintah-icon.png',
                iconSize: [32, 32],
                iconAnchor: [16, 32],
                popupAnchor: [0, -32]
            });

            var petunjukIcon = L.icon({
                iconUrl: 'turn-left.png',
                iconSize: [32, 32],
                iconAnchor: [16, 32],
                popupAnchor: [0, -32]
            });

            var lampu3Icon = L.icon({
                iconUrl: '/path/to/lampu3-icon.png',
                iconSize: [32, 32],
                iconAnchor: [16, 32],
                popupAnchor: [0, -32]
            });

            var lampu2Icon = L.icon({
                iconUrl: '/path/to/lampu2-icon.png',
                iconSize: [32, 32],
                iconAnchor: [16, 32],
                popupAnchor: [0, -32]
            });

            var lampu1Icon = L.icon({
                iconUrl: '/path/to/lampu1-icon.png',
                iconSize: [32, 32],
                iconAnchor: [16, 32],
                popupAnchor: [0, -32]
            });

            var countdownIcon = L.icon({
                iconUrl: '/path/to/countdown-icon.png',
                iconSize: [32, 32],
                iconAnchor: [16, 32],
                popupAnchor: [0, -32]
            });

            // Iterate over rambus data and add custom icons to each layer
            rambus.forEach(function(rambu) {
                if (rambu.lokasi) {
                    var geojson = JSON.parse(rambu.lokasi.geojson);

                    var detailButton = `<a href="/data-rambu/${rambu.id}" target="_blank" class="btn btn-sm btn-info text-white mx-1"><i class="fas fa-eye"></i> Lihat Detail</a>`;
                    
                    var popupContent = `
                        <div class="carousel-container mb-3">
                            ${rambu.fotos && rambu.fotos.length ? rambu.fotos.map(foto => `<img src="/${foto.foto_path}" class="img-thumbnail m-2" style="width:100px;height:100px;">`).join('') : "<p class='text-center'>Tidak ada foto tersedia.</p>"}
                        </div>
                        <table class="table table-bordered">
                            <tr><th>Nama Rambu</th><td>${rambu.nama_rambu}</td></tr>
                            <tr><th>Kategori Rambu</th><td>${rambu.kategori_rambu}</td></tr>
                            <tr><th>Jenis Rambu</th><td>${rambu.jenis_rambu}</td></tr>
                        </table>
                        <div class="text-center mt-3">
                            ${detailButton} <!-- Menambahkan tombol Detail -->
                        </div>
                    `;

                    // Determine the icon based on category
                    var icon;
                    switch (rambu.kategori_rambu) {
                        case "Rambu Peringatan":
                            icon = peringatanIcon;
                            peringatanGroup.addLayer(L.geoJSON(geojson, {
                                pointToLayer: function(feature, latlng) {
                                    return L.marker(latlng, { icon: icon }).bindPopup(popupContent);
                                }
                            }));
                            break;
                        case "Rambu Larangan":
                            icon = laranganIcon;
                            laranganGroup.addLayer(L.geoJSON(geojson, {
                                pointToLayer: function(feature, latlng) {
                                    return L.marker(latlng, { icon: icon }).bindPopup(popupContent);
                                }
                            }));
                            break;
                        case "Rambu Perintah":
                            icon = perintahIcon;
                            perintahGroup.addLayer(L.geoJSON(geojson, {
                                pointToLayer: function(feature, latlng) {
                                    return L.marker(latlng, { icon: icon }).bindPopup(popupContent);
                                }
                            }));
                            break;
                        case "Rambu Petunjuk":
                            icon = petunjukIcon;
                            petunjukGroup.addLayer(L.geoJSON(geojson, {
                                pointToLayer: function(feature, latlng) {
                                    return L.marker(latlng, { icon: icon }).bindPopup(popupContent);
                                }
                            }));
                            break;
                        case "Lampu 3 Warna":
                            icon = lampu3Icon;
                            lampu3Group.addLayer(L.geoJSON(geojson, {
                                pointToLayer: function(feature, latlng) {
                                    return L.marker(latlng, { icon: icon }).bindPopup(popupContent);
                                }
                            }));
                            break;
                        case "Lampu 2 Warna":
                            icon = lampu2Icon;
                            lampu2Group.addLayer(L.geoJSON(geojson, {
                                pointToLayer: function(feature, latlng) {
                                    return L.marker(latlng, { icon: icon }).bindPopup(popupContent);
                                }
                            }));
                            break;
                        case "Lampu 1 Warna":
                            icon = lampu1Icon;
                            lampu1Group.addLayer(L.geoJSON(geojson, {
                                pointToLayer: function(feature, latlng) {
                                    return L.marker(latlng, { icon: icon }).bindPopup(popupContent);
                                }
                            }));
                            break;
                        case "Countdown":
                            icon = countdownIcon;
                            countdownGroup.addLayer(L.geoJSON(geojson, {
                                pointToLayer: function(feature, latlng) {
                                    return L.marker(latlng, { icon: icon }).bindPopup(popupContent);
                                }
                            }));
                            break;
                    }
                }
            });
        
            // Tambahkan grup layer ke peta
            peringatanGroup.addTo(map);
            laranganGroup.addTo(map);
            perintahGroup.addTo(map);
            petunjukGroup.addTo(map);
            lampu3Group.addTo(map);
            lampu2Group.addTo(map);
            lampu1Group.addTo(map);
            countdownGroup.addTo(map);
        
            bersuarGroup.addTo(map);
            tidakBersuarGroup.addTo(map);
        
            // Tambahkan kontrol layer untuk kategori_rambu dan jenis_rambu
            var baseLayers = {
                "OpenStreetMap": osmLayer,
                "Esri World Imagery": esriLayer
            };
        
            var kategoriRambuOverlays = {
                "Rambu Peringatan": peringatanGroup,
                "Rambu Larangan": laranganGroup,
                "Rambu Perintah": perintahGroup,
                "Rambu Petunjuk": petunjukGroup,
                "Lampu 3 Warna": lampu3Group,
                "Lampu 2 Warna": lampu2Group,
                "Lampu 1 Warna": lampu1Group,
                "Countdown": countdownGroup
            };
        
            var jenisRambuOverlays = {
                "Bersuar": bersuarGroup,
                "Tidak Bersuar": tidakBersuarGroup
            };
        
            // Tambahkan kontrol layer ke peta
            L.control.layers(baseLayers, kategoriRambuOverlays, { collapsed: false, position: 'topright' }).addTo(map);
            L.control.layers(null, jenisRambuOverlays, { collapsed: false, position: 'topright' }).addTo(map);
        
        </script>
    @endpush
@endsection