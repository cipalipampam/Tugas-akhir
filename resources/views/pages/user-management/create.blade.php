<x-layout bodyClass="g-sidenav-show bg-gray-200">
    <x-navbars.sidebar activePage="user-management.index"></x-navbars.sidebar>
    <main class="main-content position-relative max-height-vh-100 h-100 border-radius-lg">
        <x-navbars.navs.auth titlePage="Tambah User"></x-navbars.navs.auth>

        <div class="container-fluid py-4">
            @if($errors->any())
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <ul class="mb-0">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

            <div class="row">
                <div class="col-12">
                    <div class="card my-4">
                        <div class="card-header p-0 position-relative mt-n4 mx-3 z-index-2">
                            <div class="bg-gradient-primary shadow-primary border-radius-lg pt-4 pb-3">
                                <h6 class="text-white text-capitalize ps-3">Form Tambah User</h6>
                            </div>
                        </div>
                        <div class="card-body px-4 pb-2">
                            <form action="{{ route('user-management.store') }}" method="POST">
                                @csrf
                                <div class="input-group input-group-static mb-4">
                                    <label for="name">Nama</label>
                                    <input type="text" class="form-control @error('name') is-invalid @enderror" id="name" name="name" value="{{ old('name') }}" required>
                                    @error('name')
                                        <span class="invalid-feedback">{{ $message }}</span>
                                    @enderror
                                </div>

                                <div class="input-group input-group-static mb-4">
                                    <label for="email">Email</label>
                                    <input type="email" class="form-control @error('email') is-invalid @enderror" id="email" name="email" value="{{ old('email') }}" required>
                                    @error('email')
                                        <span class="invalid-feedback">{{ $message }}</span>
                                    @enderror
                                </div>

                                <div class="input-group input-group-static mb-4">
                                    <label for="password">Password</label>
                                    <input type="password" class="form-control @error('password') is-invalid @enderror" id="password" name="password" required>
                                    @error('password')
                                        <span class="invalid-feedback">{{ $message }}</span>
                                    @enderror
                                </div>

                                <div class="input-group input-group-static mb-4">
                                    <label for="password_confirmation">Konfirmasi Password</label>
                                    <input type="password" class="form-control" id="password_confirmation" name="password_confirmation" required>
                                </div>

                                <div class="input-group input-group-static mb-4">
                                    <label for="role">Role</label>
                                    <select class="form-control @error('role') is-invalid @enderror" id="role" name="role" required>
                                        <option value="">Pilih Role</option>
                                        <option value="admin" {{ old('role') == 'admin' ? 'selected' : '' }}>Admin</option>
                                        <option value="superadministrator" {{ old('role') == 'superadministrator' ? 'selected' : '' }}>Super Administrator</option>
                                    </select>
                                    @error('role')
                                        <span class="invalid-feedback">{{ $message }}</span>
                                    @enderror
                                </div>

                                <div class="d-flex justify-content-end">
                                    <a href="{{ route('user-management.index') }}" class="btn btn-secondary me-2">Kembali</a>
                                    <button type="submit" class="btn btn-primary">Simpan</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </main>
    <x-plugins></x-plugins>
</x-layout> 