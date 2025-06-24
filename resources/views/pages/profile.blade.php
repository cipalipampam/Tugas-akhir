<x-layout bodyClass="g-sidenav-show bg-gray-200">
    <x-navbars.sidebar activePage="profile"></x-navbars.sidebar>
    <div class="main-content position-relative bg-gray-100 max-height-vh-100 h-100">
        <!-- Navbar -->
        <x-navbars.navs.auth titlePage='Profile'></x-navbars.navs.auth>
        <!-- End Navbar -->
        <div class="container-fluid px-2 px-md-4">
            <div class="page-header min-height-300 border-radius-xl mt-4"
                style="background-image: url('https://images.unsplash.com/photo-1531512073830-ba890ca4eba2?ixid=MnwxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8&ixlib=rb-1.2.1&auto=format&fit=crop&w=1920&q=80');">
                <span class="mask bg-gradient-primary opacity-6"></span>
            </div>
            <div class="card card-body mx-3 mx-md-4 mt-n6">
                <div class="row gx-4 mb-2">
                    <div class="col-auto">
                        <div class="avatar avatar-xl position-relative">
                            <img src="{{ asset('assets') }}/img/bruce-mars.jpg" alt="profile_image"
                                class="w-100 border-radius-lg shadow-sm">
                        </div>
                    </div>
                    <div class="col-auto my-auto">
                        <div class="h-100">
                            <h5 class="mb-1">
                                {{ auth()->user()->name }}
                            </h5>
                            <p class="mb-0 font-weight-normal text-sm">
                                <span class="badge bg-gradient-primary">{{ auth()->user()->role ?? 'User' }}</span>
                            </p>
                            <p class="mb-0 font-weight-normal text-sm">
                                <i class="fas fa-envelope me-1"></i> {{ auth()->user()->email }}
                            </p>
                            <p class="mb-0 font-weight-normal text-sm">
                                <i class="fas fa-clock me-1"></i> Bergabung sejak {{ auth()->user()->created_at->format('d M Y') }}
                            </p>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-12">
                        <div class="card card-plain h-100">
                            <div class="card-header pb-0 p-3">
                                <div class="row">
                                    <div class="col-md-8 d-flex align-items-center">
                                        <h6 class="mb-0">Informasi Profil</h6>
                                    </div>
                                </div>
                            </div>
                            <div class="card-body p-3">
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="card bg-gradient-info">
                                            <div class="card-body p-3">
                                                <div class="d-flex align-items-center">
                                                    <div class="icon icon-shape icon-shape-white shadow text-center border-radius-md">
                                                        <i class="fas fa-user text-lg opacity-10" aria-hidden="true"></i>
                                                    </div>
                                                    <div class="ms-3">
                                                        <h6 class="text-white mb-0">Nama Lengkap</h6>
                                                        <p class="text-white text-sm mb-0">{{ auth()->user()->name }}</p>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="card bg-gradient-success">
                                            <div class="card-body p-3">
                                                <div class="d-flex align-items-center">
                                                    <div class="icon icon-shape icon-shape-white shadow text-center border-radius-md">
                                                        <i class="fas fa-envelope text-lg opacity-10" aria-hidden="true"></i>
                                                    </div>
                                                    <div class="ms-3">
                                                        <h6 class="text-white mb-0">Email</h6>
                                                        <p class="text-white text-sm mb-0">{{ auth()->user()->email }}</p>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="row mt-4">
                                    <div class="col-md-6">
                                        <div class="card bg-gradient-warning">
                                            <div class="card-body p-3">
                                                <div class="d-flex align-items-center">
                                                    <div class="icon icon-shape icon-shape-white shadow text-center border-radius-md">
                                                        <i class="fas fa-user-tag text-lg opacity-10" aria-hidden="true"></i>
                                                    </div>
                                                    <div class="ms-3">
                                                        <h6 class="text-white mb-0">Role</h6>
                                                        <p class="text-white text-sm mb-0">{{ auth()->user()->role ?? 'User' }}</p>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="card bg-gradient-danger">
                                            <div class="card-body p-3">
                                                <div class="d-flex align-items-center">
                                                    <div class="icon icon-shape icon-shape-white shadow text-center border-radius-md">
                                                        <i class="fas fa-calendar text-lg opacity-10" aria-hidden="true"></i>
                                                    </div>
                                                    <div class="ms-3">
                                                        <h6 class="text-white mb-0">Bergabung Sejak</h6>
                                                        <p class="text-white text-sm mb-0">{{ auth()->user()->created_at->format('d M Y') }}</p>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <x-footers.auth></x-footers.auth>
    </div>
    <x-plugins></x-plugins>
</x-layout>
