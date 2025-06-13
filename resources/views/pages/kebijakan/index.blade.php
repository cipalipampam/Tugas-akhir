<x-layout bodyClass="g-sidenav-show bg-gray-200">
    <x-navbars.sidebar activePage="kebijakan.index"></x-navbars.sidebar>
    <main class="main-content position-relative max-height-vh-100 h-100 border-radius-lg">
        <x-navbars.navs.auth titlePage="Kebijakan"></x-navbars.navs.auth>

        <div class="container-fluid py-4">
            @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    {{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

            @if(session('error'))
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    {{ session('error') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

            <div class="row">
                <div class="col-12">
                    <div class="card mb-4">
                        <div class="card-header p-3 d-flex justify-content-between align-items-center">
                            <h6 class="mb-0"><i class="fas fa-graduation-cap me-2"></i>Aturan Kelulusan Siswa</h6>
                            <a href="{{ route('kebijakan.create') }}" class="btn btn-primary btn-sm">
                                <i class="fas fa-plus me-2"></i>Tambah Aturan
                            </a>
                        </div>
                        <div class="card-body p-3">
                            @if($rules->isEmpty())
                                <div class="alert alert-info">
                                    <i class="fas fa-info-circle me-2"></i>Belum ada aturan kelulusan yang ditambahkan.
                                </div>
                            @else
                                <div class="table-responsive">
                                    <table class="table align-items-center mb-0">
                                        <thead>
                                            <tr>
                                                <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Urutan</th>
                                                <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">Jenis Nilai</th>
                                                <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">Kondisi</th>
                                                <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">Nilai Patokan</th>
                                                <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">Hasil Prediksi</th>
                                                <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">Aksi</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($rules as $rule)
                                                <tr>
                                                    <td>
                                                        <span class="text-secondary text-xs font-weight-bold px-3">{{ $rule->priority }}</span>
                                                    </td>
                                                    <td>
                                                        <span class="text-secondary text-xs font-weight-bold">
                                                            @switch($rule->attribute)
                                                                @case('rata_rata')
                                                                    Rata-rata Nilai
                                                                    @break
                                                                @case('usp')
                                                                    Nilai USP
                                                                    @break
                                                                @case('sikap')
                                                                    Sikap
                                                                    @break
                                                                @case('kerajinan')
                                                                    Kerajinan
                                                                    @break
                                                                @case('kerapian')
                                                                    Kerapian
                                                                    @break
                                                                @default
                                                                    {{ $rule->attribute }}
                                                            @endswitch
                                                        </span>
                                                    </td>
                                                    <td>
                                                        <span class="text-secondary text-xs font-weight-bold">
                                                            @switch($rule->operator)
                                                                @case('>')
                                                                    Lebih dari
                                                                    @break
                                                                @case('<')
                                                                    Kurang dari
                                                                    @break
                                                                @case('=')
                                                                    Sama dengan
                                                                    @break
                                                                @case('>=')
                                                                    Lebih dari atau sama dengan
                                                                    @break
                                                                @case('<=')
                                                                    Kurang dari atau sama dengan
                                                                    @break
                                                                @default
                                                                    {{ $rule->operator }}
                                                            @endswitch
                                                        </span>
                                                    </td>
                                                    <td>
                                                        <span class="text-secondary text-xs font-weight-bold">{{ $rule->value }}</span>
                                                    </td>
                                                    <td>
                                                        <span class="badge bg-gradient-{{ $rule->category === 'lulus' ? 'success' : ($rule->category === 'lulus bersyarat' ? 'warning' : 'danger') }}">
                                                            {{ ucfirst($rule->category) }}
                                                        </span>
                                                    </td>
                                                    <td>
                                                        <button class="btn btn-link text-danger text-gradient px-3 mb-0"
                                                            onclick="deleteRule({{ $rule->id }})">
                                                            <i class="far fa-trash-alt me-2"></i>Hapus
                                                        </button>
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </main>

    @push('scripts')
    <script>
        function deleteRule(id) {
            if (confirm('Apakah Anda yakin ingin menghapus aturan ini?')) {
                fetch(`/admin/kebijakan/${id}`, {
                    method: 'DELETE',
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Accept': 'application/json'
                    }
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        location.reload();
                    } else {
                        alert(data.message || 'Gagal menghapus aturan');
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('Terjadi kesalahan saat menghapus aturan');
                });
            }
        }
    </script>
    @endpush
</x-layout>