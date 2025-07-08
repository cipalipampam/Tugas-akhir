@props(['activePage'])

<aside
    class="sidenav navbar navbar-vertical navbar-expand-xs border-0 border-radius-xl my-3 fixed-start ms-3   bg-gradient-dark"
    id="sidenav-main">
    <div class="sidenav-header">
        <i class="fas fa-times p-3 cursor-pointer text-white opacity-5 position-absolute end-0 top-0 d-none d-xl-none"
            aria-hidden="true" id="iconSidenav"></i>
        <a class="navbar-brand m-0 d-flex text-wrap align-items-center" href=" {{ route('dashboard') }} ">
            <img src="{{ asset('assets') }}/img/logo-ct.png" class="navbar-brand-img h-100" alt="main_logo">
            <span class="ms-2 font-weight-bold text-white">Fuzzy K-NN</span>
        </a>
    </div>
    <hr class="horizontal light mt-0 mb-2">
    <div class="collapse navbar-collapse  w-auto  max-height-vh-100" id="sidenav-collapse-main">
        <ul class="navbar-nav">
            <li class="nav-item mt-3">
                <h6 class="ps-4 ms-2 text-uppercase text-xs text-white font-weight-bolder opacity-8">Pages</h6>
            </li>
            <li class="nav-item">
                <a class="nav-link text-white {{ $activePage == 'dashboard' ? ' active bg-gradient-primary' : '' }} "
                    href="{{ route('dashboard') }}">
                    <div class="text-white text-center me-2 d-flex align-items-center justify-content-center">
                        <i class="material-icons opacity-10">dashboard</i>
                    </div>
                    <span class="nav-link-text ms-1">Beranda</span>
                </a>
            </li>
            @if(auth()->user()->role === 'superadministrator')
                <li class="nav-item">
                    <a class="nav-link text-white {{ $activePage == 'input-data' ? ' active bg-gradient-primary' : '' }} "
                        href="{{ route('input-data') }}">
                        <div class="text-white text-center me-2 d-flex align-items-center justify-content-center">
                            <i class="material-icons opacity-10">table_view</i>
                        </div>
                        <span class="nav-link-text ms-1">Data Latih</span>
                    </a>
                </li>
            @endif
            <li class="nav-item">
                <a class="nav-link text-white {{ $activePage == 'prediksi' ? ' active bg-gradient-primary' : '' }}  "
                    href="{{ route('prediksi') }}">
                    <div class="text-white text-center me-2 d-flex align-items-center justify-content-center">
                        <i class="material-icons opacity-10">insights</i>
                    </div>
                    <span class="nav-link-text ms-1">Prediksi</span>
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link text-white {{ $activePage == 'performa' ? ' active bg-gradient-primary' : '' }}  "
                    href="{{ route('performa') }}">
                    <div class="text-white text-center me-2 d-flex align-items-center justify-content-center">
                        <i class="material-icons opacity-10">assessment</i>
                    </div>
                    <span class="nav-link-text ms-1">Performa</span>
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link text-white {{ $activePage == 'visualisasi-data' ? ' active bg-gradient-primary' : '' }}  "
                    href="{{ route('visualisasi-data') }}">
                    <div class="text-white text-center me-2 d-flex align-items-center justify-content-center">
                        <i class="material-icons opacity-10">bar_chart</i>
                    </div>
                    <span class="nav-link-text ms-1">Visualisasi Data</span>
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link text-white {{ $activePage == 'export' ? ' active bg-gradient-primary' : '' }}  "
                    href="{{ route('export') }}">
                    <div class="text-white text-center me-2 d-flex align-items-center justify-content-center">
                        <i class="material-icons opacity-10">file_download</i>
                    </div>
                    <span class="nav-link-text ms-1">Export</span>
                </a>
            </li>
            <li class="nav-item mt-3">
                <h6 class="ps-4 ms-2 text-uppercase text-xs text-white font-weight-bolder opacity-8">Account pages</h6>
            </li>
            <li class="nav-item">
                <a class="nav-link text-white {{ $activePage == 'profile' ? ' active bg-gradient-primary' : '' }}  "
                    href="{{ route('profile') }}">
                    <div class="text-white text-center me-2 d-flex align-items-center justify-content-center">
                        <i class="material-icons opacity-10">person</i>
                    </div>
                    <span class="nav-link-text ms-1">Profil</span>
                </a>
            </li>
            <li class="nav-item mt-3">
                <h6 class="ps-4 ms-2 text-uppercase text-xs text-white font-weight-bolder opacity-8">Manajemen Pages
                </h6>
            </li>
            @if(auth()->user()->role === 'superadministrator')

                <!-- <li class="nav-item">
                    <a class="nav-link text-white {{ $activePage == 'kebijakan.index' ? ' active bg-gradient-primary' : '' }}  "
                        href="{{ route('kebijakan.index') }}">
                        <div class="text-white text-center me-2 d-flex align-items-center justify-content-center">
                            <i class="material-icons opacity-10">policy</i>
                        </div>
                        <span class="nav-link-text ms-1">Kebijakan</span>
                    </a>
                </li> -->
                <li class="nav-item">
                    <a class="nav-link text-white {{ $activePage == 'user-management.index' ? ' active bg-gradient-primary' : '' }}  "
                        href="{{ route('user-management.index') }}">
                        <div class="text-white text-center me-2 d-flex align-items-center justify-content-center">
                            <i class="material-icons opacity-10">man</i>
                        </div>
                        <span class="nav-link-text ms-1">Manajemen Pengguna</span>
                    </a>
                </li>
            @endif
            
        </ul>
    </div>
</aside>