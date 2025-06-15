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
                            <a href="javascript:;" class="btn btn-sm btn-icon-only bg-gradient-light position-absolute bottom-0 end-0 mb-n2 me-n2" data-bs-toggle="modal" data-bs-target="#changePhotoModal">
                                <i class="fas fa-camera"></i>
                            </a>
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
                    <div class="col-12 col-xl-6">
                        <div class="card card-plain h-100">
                            <div class="card-header pb-0 p-3">
                                <div class="row">
                                    <div class="col-md-8 d-flex align-items-center">
                                        <h6 class="mb-0">Informasi Profil</h6>
                                    </div>
                                    <div class="col-md-4 text-end">
                                        <a href="javascript:;" class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#editProfileModal">
                                            <i class="fas fa-user-edit text-white"></i> Edit Profil
                                        </a>
                                    </div>
                                </div>
                            </div>
                            <div class="card-body p-3">
                                <hr class="horizontal gray-light my-4">
                                <ul class="list-group">
                                    <li class="list-group-item border-0 ps-0 pt-0 text-sm">
                                        <strong class="text-dark">Nama Lengkap:</strong> &nbsp; {{ auth()->user()->name }}
                                    </li>
                                    <li class="list-group-item border-0 ps-0 text-sm">
                                        <strong class="text-dark">Email:</strong> &nbsp; {{ auth()->user()->email }}
                                    </li>
                                    <li class="list-group-item border-0 ps-0 text-sm">
                                        <strong class="text-dark">No. Telepon:</strong> &nbsp; {{ auth()->user()->phone ?? '-' }}
                                    </li>
                                    <li class="list-group-item border-0 ps-0 text-sm">
                                        <strong class="text-dark">Lokasi:</strong> &nbsp; {{ auth()->user()->location ?? '-' }}
                                    </li>
                                    <li class="list-group-item border-0 ps-0 text-sm">
                                        <strong class="text-dark">Status Akun:</strong> &nbsp; 
                                        <span class="badge bg-gradient-success">Aktif</span>
                                    </li>
                                    <li class="list-group-item border-0 ps-0 text-sm">
                                        <strong class="text-dark">Terakhir Login:</strong> &nbsp; 
                                        {{ auth()->user()->last_login_at ? auth()->user()->last_login_at->format('d M Y H:i') : '-' }}
                                    </li>
                                </ul>
                            </div>
                        </div>
                    </div>

                    <div class="col-12 col-xl-6">
                        <div class="card card-plain h-100">
                            <div class="card-header pb-0 p-3">
                                <div class="row">
                                    <div class="col-md-8 d-flex align-items-center">
                                        <h6 class="mb-0">Pengaturan Akun</h6>
                                    </div>
                                    <div class="col-md-4 text-end">
                                        <a href="javascript:;" class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#changePasswordModal">
                                            <i class="fas fa-key text-white"></i> Ubah Password
                                        </a>
                                    </div>
                                </div>
                            </div>
                            <div class="card-body p-3">
                                <h6 class="text-uppercase text-body text-xs font-weight-bolder mb-3">Notifikasi</h6>
                                <ul class="list-group">
                                    <li class="list-group-item border-0 px-0">
                                        <div class="form-check form-switch ps-0">
                                            <input class="form-check-input ms-auto" type="checkbox" id="notifEmail" checked>
                                            <label class="form-check-label text-body ms-3 text-truncate w-80 mb-0" for="notifEmail">
                                                Notifikasi Email
                                            </label>
                                        </div>
                                    </li>
                                    <li class="list-group-item border-0 px-0">
                                        <div class="form-check form-switch ps-0">
                                            <input class="form-check-input ms-auto" type="checkbox" id="notifSystem" checked>
                                            <label class="form-check-label text-body ms-3 text-truncate w-80 mb-0" for="notifSystem">
                                                Notifikasi Sistem
                                            </label>
                                        </div>
                                    </li>
                                </ul>

                                <h6 class="text-uppercase text-body text-xs font-weight-bolder mt-4 mb-3">Keamanan</h6>
                                <ul class="list-group">
                                    <li class="list-group-item border-0 px-0">
                                        <div class="form-check form-switch ps-0">
                                            <input class="form-check-input ms-auto" type="checkbox" id="twoFactor" checked>
                                            <label class="form-check-label text-body ms-3 text-truncate w-80 mb-0" for="twoFactor">
                                                Autentikasi 2 Faktor
                                            </label>
                                        </div>
                                    </li>
                                    <li class="list-group-item border-0 px-0">
                                        <div class="form-check form-switch ps-0">
                                            <input class="form-check-input ms-auto" type="checkbox" id="loginHistory" checked>
                                            <label class="form-check-label text-body ms-3 text-truncate w-80 mb-0" for="loginHistory">
                                                Riwayat Login
                                            </label>
                                        </div>
                                    </li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Aktivitas Terakhir -->
                <div class="row mt-4">
                    <div class="col-12">
                        <div class="card card-plain">
                            <div class="card-header pb-0 p-3">
                                <h6 class="mb-0">Aktivitas Terakhir</h6>
                            </div>
                            <div class="card-body p-3">
                                <div class="timeline timeline-one-side">
                                    <div class="timeline-block mb-3">
                                        <span class="timeline-step">
                                            <i class="fas fa-sign-in-alt text-success"></i>
                                        </span>
                                        <div class="timeline-content">
                                            <h6 class="text-dark text-sm font-weight-bold mb-0">Login ke sistem</h6>
                                            <p class="text-secondary font-weight-bold text-xs mt-1 mb-0">22 DEC 7:20 PM</p>
                                        </div>
                                    </div>
                                    <div class="timeline-block mb-3">
                                        <span class="timeline-step">
                                            <i class="fas fa-edit text-info"></i>
                                        </span>
                                        <div class="timeline-content">
                                            <h6 class="text-dark text-sm font-weight-bold mb-0">Mengubah profil</h6>
                                            <p class="text-secondary font-weight-bold text-xs mt-1 mb-0">21 DEC 11:01 AM</p>
                                        </div>
                                    </div>
                                    <div class="timeline-block">
                                        <span class="timeline-step">
                                            <i class="fas fa-key text-warning"></i>
                                        </span>
                                        <div class="timeline-content">
                                            <h6 class="text-dark text-sm font-weight-bold mb-0">Mengubah password</h6>
                                            <p class="text-secondary font-weight-bold text-xs mt-1 mb-0">21 DEC 9:56 AM</p>
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

    <!-- Edit Profile Modal -->
    <div class="modal fade" id="editProfileModal" tabindex="-1" role="dialog" aria-labelledby="editProfileModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="editProfileModalLabel">Edit Profil</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form action="{{ route('user-profile.update') }}" method="POST">
                    @csrf
                    @method('PUT')
                    <div class="modal-body">
                        <div class="form-group">
                            <label for="name">Nama Lengkap</label>
                            <input type="text" class="form-control" id="name" name="name" value="{{ auth()->user()->name }}" required>
                        </div>
                        <div class="form-group">
                            <label for="email">Email</label>
                            <input type="email" class="form-control" id="email" name="email" value="{{ auth()->user()->email }}" required>
                        </div>
                        <div class="form-group">
                            <label for="phone">No. Telepon</label>
                            <input type="text" class="form-control" id="phone" name="phone" value="{{ auth()->user()->phone }}">
                        </div>
                        <div class="form-group">
                            <label for="location">Lokasi</label>
                            <input type="text" class="form-control" id="location" name="location" value="{{ auth()->user()->location }}">
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Change Password Modal -->
    <div class="modal fade" id="changePasswordModal" tabindex="-1" role="dialog" aria-labelledby="changePasswordModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="changePasswordModalLabel">Ubah Password</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                
            </div>
        </div>
    </div>

    <!-- Change Photo Modal -->
    <div class="modal fade" id="changePhotoModal" tabindex="-1" role="dialog" aria-labelledby="changePhotoModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="changePhotoModalLabel">Ubah Foto Profil</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                
            </div>
        </div>
    </div>
</x-layout>
