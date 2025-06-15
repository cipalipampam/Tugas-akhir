<x-layout bodyClass="g-sidenav-show bg-gray-200">
    <x-navbars.sidebar activePage="user-management.index"></x-navbars.sidebar>
    <main class="main-content position-relative max-height-vh-100 h-100 border-radius-lg">
        <x-navbars.navs.auth titlePage="Manajemen User"></x-navbars.navs.auth>

        <div class="container-fluid py-4">
            @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    <span class="alert-icon"><i class="fas fa-check-circle"></i></span>
                    <span class="alert-text">{{ session('success') }}</span>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
            @endif

            @if(session('error'))
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <span class="alert-icon"><i class="fas fa-exclamation-circle"></i></span>
                    <span class="alert-text">{{ session('error') }}</span>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
            @endif

            <div class="row">
                <div class="col-12">
                    <div class="card my-4">
                        <div class="card-header p-0 position-relative mt-n4 mx-3 z-index-2">
                            <div class="bg-gradient-primary shadow-primary border-radius-lg pt-4 pb-3 d-flex justify-content-between align-items-center px-4">
                                <h6 class="text-white text-capitalize ps-3 mb-0">Daftar User</h6>
                                <a href="{{ route('user-management.create') }}" class="btn btn-sm btn-outline-light">
                                    <i class="fas fa-plus me-1"></i>
                                    Tambah User
                                </a>
                            </div>
                        </div>
                        <div class="card-body px-0 pb-2">
                            <div class="table-responsive p-0">
                                <table class="table align-items-center mb-0">
                                    <thead>
                                        <tr>
                                            <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">No</th>
                                            <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">Nama</th>
                                            <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">Email</th>
                                            <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">Role</th>
                                            <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">Aksi</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($users as $index => $user)
                                        <tr>
                                            <td class="ps-4">
                                                <span class="text-secondary text-xs font-weight-bold">{{ $index + 1 }}</span>
                                            </td>
                                            <td>
                                                <span class="text-secondary text-xs font-weight-bold">{{ $user->name }}</span>
                                            </td>
                                            <td>
                                                <span class="text-secondary text-xs font-weight-bold">{{ $user->email }}</span>
                                            </td>
                                            <td>
                                                <span class="text-secondary text-xs font-weight-bold">{{ ucfirst($user->role) }}</span>
                                            </td>
                                            <td>
                                                <a href="{{ route('user-management.edit', $user) }}" class="btn btn-sm btn-info">
                                                    <i class="fas fa-edit"></i> Edit
                                                </a>
                                                @if($user->id !== auth()->id())
                                                <form action="{{ route('user-management.destroy', $user) }}" method="POST" class="d-inline">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('Apakah Anda yakin ingin menghapus user ini?')">
                                                        <i class="fas fa-trash"></i> Hapus
                                                    </button>
                                                </form>
                                                @endif
                                            </td>
                                        </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </main>
    <x-plugins></x-plugins>
</x-layout> 