<x-layout bodyClass="g-sidenav-show bg-gray-200">
    <x-navbars.sidebar activePage="input-data"></x-navbars.sidebar>
    <main class="main-content position-relative max-height-vh-100 h-100 border-radius-lg">
        <x-navbars.navs.auth titlePage="Manajemen Data Latih"></x-navbars.navs.auth>
        <div class="container-fluid py-4">
            <div class="row mb-3 align-items-center">
                <div class="col-6 col-md-8">
                    <h4 class="fw-semibold text-dark mb-0" style="font-size:1.4rem; letter-spacing:0.5px;">Manajemen Data Latih</h4>
                </div>
                <div class="col-6 col-md-4 text-end">
                    <a href="{{ route('input-data') }}" class="btn btn-primary rounded-pill px-4 py-2 shadow-sm" style="background:#4f8cff; border:none; font-weight:500; font-size:1rem;">
                        <i class="fas fa-plus me-2"></i>Tambah Data Latih
                    </a>
                </div>
            </div>
            @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    <span class="alert-text">{{ session('success') }}</span>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif
            <div class="card mb-4">
                <div class="card-header p-3 d-flex justify-content-between align-items-center">
                    <h6 class="mb-0"><i class="fas fa-database me-2"></i>Data Latih (Training)</h6>
                </div>
                <div class="card-body p-3">
                    <div class="table-responsive">
                        <table class="table mb-0">
                            <thead>
                                <tr>
                                    <th class="py-3 px-3 text-secondary">No</th>
                                    <th class="py-3 px-3 text-secondary">NISN</th>
                                    <th class="py-3 px-3 text-secondary">Nama</th>
                                    <th class="py-3 px-3 text-secondary">Tahun</th>
                                    <th class="py-3 px-3 text-secondary text-center">Status</th>
                                    <th class="py-3 px-3 text-secondary text-center">USP</th>
                                    <th class="py-3 px-3 text-secondary text-center">Rata-Rata Nilai</th>
                                    <th class="py-3 px-3 text-secondary text-center">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($students as $i => $student)
                                    @php
                                        $nilai = collect($student->studentValues->whereIn('key', ['semester_1','semester_2','semester_3','semester_4','semester_5','semester_6'])->pluck('value')->map(fn($v)=>floatval($v)));
                                        $avg = $nilai->count() ? number_format($nilai->avg(),2) : '-';
                                        $usp = $student->studentValues->where('key','usp')->first()->value ?? '-';
                                        $status = strtolower($student->true_status);
                                    @endphp
                                    <tr style="border-bottom:1px solid #f0f1f3;">
                                        <td class="py-3 px-3 align-middle">{{ $i+1 }}</td>
                                        <td class="py-3 px-3 align-middle">{{ $student->nisn }}</td>
                                        <td class="py-3 px-3 align-middle">
                                            <a href="#" class="text-primary fw-semibold text-decoration-none" data-bs-toggle="modal" data-bs-target="#modalDetail{{ $student->id }}">{{ $student->name }}</a>
                                        </td>
                                        <td class="py-3 px-3 align-middle">{{ $student->tahun_angkatan }}</td>
                                        <td class="py-3 px-3 align-middle text-center">
                                            @if($status === 'lulus')
                                                <span class="badge rounded-pill" style="background:#d1f5e6; color:#1e824c; font-weight:600; font-size:0.95rem;">LULUS</span>
                                            @elseif($status === 'lulus bersyarat')
                                                <span class="badge rounded-pill" style="background:#fff7d6; color:#bfa100; font-weight:600; font-size:0.95rem;">LULUS BERSYARAT</span>
                                            @elseif($status === 'tidak lulus')
                                                <span class="badge rounded-pill" style="background:#ffe0e0; color:#d32f2f; font-weight:600; font-size:0.95rem;">TIDAK LULUS</span>
                                            @else
                                                <span class="badge rounded-pill bg-secondary-subtle text-secondary">-</span>
                                            @endif
                                        </td>
                                        <td class="py-3 px-3 align-middle text-center">{{ $usp }}</td>
                                        <td class="py-3 px-3 align-middle text-center">{{ $avg }}</td>
                                        <td class="py-3 px-3 align-middle text-center">
                                            <button class="btn btn-sm btn-warning rounded-pill px-3 me-1 d-inline-flex align-items-center btn-edit" style="font-weight:500; font-size:0.98rem;" data-bs-toggle="modal" data-bs-target="#modalEdit{{ $student->id }}">
                                                <span class="me-1">✏️</span> Edit
                                            </button>
                                            <form action="{{ route('training.destroy', $student->id) }}" method="POST" style="display:inline-block;" onsubmit="return confirm('Yakin ingin menghapus data ini?')">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-sm btn-danger rounded-pill px-3 d-inline-flex align-items-center btn-delete" style="font-weight:500; font-size:0.98rem;">
                                                    <span class="me-1">🗑️</span> Hapus
                                                </button>
                                            </form>
                                        </td>
                                    </tr>
                                    <!-- Modal Detail Nilai -->
                                    <div class="modal fade" id="modalDetail{{ $student->id }}" tabindex="-1" aria-labelledby="modalDetailLabel{{ $student->id }}" aria-hidden="true">
                                        <div class="modal-dialog modal-dialog-centered">
                                            <div class="modal-content">
                                                <div class="modal-header" style="background:#f5f7fa;">
                                                    <h6 class="modal-title" id="modalDetailLabel{{ $student->id }}">Detail Nilai Siswa</h6>
                                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                                </div>
                                                <div class="modal-body">
                                                    <div class="mb-2"><b>Nama:</b> {{ $student->name }}</div>
                                                    <div class="mb-2"><b>NISN:</b> {{ $student->nisn }}</div>
                                                    <div class="mb-2"><b>Tahun Angkatan:</b> {{ $student->tahun_angkatan }}</div>
                                                    <div class="mb-2"><b>Status:</b> {{ $student->true_status }}</div>
                                                    <hr>
                                                    <div class="mb-2"><b>Semester 1:</b> {{ $student->studentValues->where('key','semester_1')->first()->value ?? '-' }}</div>
                                                    <div class="mb-2"><b>Semester 2:</b> {{ $student->studentValues->where('key','semester_2')->first()->value ?? '-' }}</div>
                                                    <div class="mb-2"><b>Semester 3:</b> {{ $student->studentValues->where('key','semester_3')->first()->value ?? '-' }}</div>
                                                    <div class="mb-2"><b>Semester 4:</b> {{ $student->studentValues->where('key','semester_4')->first()->value ?? '-' }}</div>
                                                    <div class="mb-2"><b>Semester 5:</b> {{ $student->studentValues->where('key','semester_5')->first()->value ?? '-' }}</div>
                                                    <div class="mb-2"><b>Semester 6:</b> {{ $student->studentValues->where('key','semester_6')->first()->value ?? '-' }}</div>
                                                    <div class="mb-2"><b>USP:</b> {{ $usp }}</div>
                                                    <div class="mb-2"><b>Sikap:</b> {{ $student->studentValues->where('key','sikap')->first()->value ?? '-' }}</div>
                                                    <div class="mb-2"><b>Kerapian:</b> {{ $student->studentValues->where('key','kerapian')->first()->value ?? '-' }}</div>
                                                    <div class="mb-2"><b>Kerajinan:</b> {{ $student->studentValues->where('key','kerajinan')->first()->value ?? '-' }}</div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <!-- Modal Edit Data Siswa -->
                                    <div class="modal fade" id="modalEdit{{ $student->id }}" tabindex="-1" aria-labelledby="modalEditLabel{{ $student->id }}" aria-hidden="true">
                                        <div class="modal-dialog modal-xl modal-dialog-centered">
                                            <div class="modal-content">
                                                <form method="POST" action="{{ route('training.update', $student->id) }}">
                                                    @csrf
                                                    <div class="modal-header bg-primary text-white d-flex align-items-center">
                                                        <i class="fas fa-user-edit me-2"></i>
                                                        <div>
                                                            <div class="fw-bold" style="font-size:1.25rem;">Edit Data Siswa</div>
                                                            <div class="text-white-50" style="font-size:1rem;">Perbarui data siswa dan nilai/atribut di bawah ini. Pastikan data sudah benar sebelum menyimpan perubahan.</div>
                                                        </div>
                                                        <button type="button" class="btn-close btn-close-white ms-auto" data-bs-dismiss="modal" aria-label="Close"></button>
                                                    </div>
                                                    <div class="modal-body bg-white">
                                                        <div class="row g-4">
                                                            <!-- Data Siswa -->
                                                            <div class="col-md-6 border-md-end" style="border-right:1px solid #f0f1f3;">
                                                                <h6 class="fw-semibold mb-3 text-primary"><i class="fas fa-id-card me-1"></i> Data Siswa</h6>
                                                                <div class="d-flex align-items-center mb-4">
                                                                    <img src="https://ui-avatars.com/api/?name={{ urlencode($student->name) }}&background=4f8cff&color=fff&size=64" alt="Avatar" class="rounded-circle me-3 shadow-sm" style="width:56px;height:56px;">
                                                                    <div>
                                                                        <div class="fw-semibold" style="font-size:1.15rem;">{{ $student->name }}</div>
                                                                        <div class="text-muted" style="font-size:0.98rem;">NISN: {{ $student->nisn }}</div>
                                                                    </div>
                                                                </div>
                                                                <div class="form-floating mb-3">
                                                                    <input type="text" name="nisn" class="form-control" id="nisn{{ $student->id }}" value="{{ $student->nisn }}" required>
                                                                    <label for="nisn{{ $student->id }}"><i class="fas fa-id-badge me-1 text-primary"></i> NISN</label>
                                                                </div>
                                                                <div class="form-floating mb-3">
                                                                    <input type="text" name="name" class="form-control" id="name{{ $student->id }}" value="{{ $student->name }}" required>
                                                                    <label for="name{{ $student->id }}"><i class="fas fa-user me-1 text-primary"></i> Nama</label>
                                                                </div>
                                                                <div class="form-floating mb-3">
                                                                    <input type="text" name="tahun_angkatan" class="form-control" id="tahun_angkatan{{ $student->id }}" value="{{ $student->tahun_angkatan }}">
                                                                    <label for="tahun_angkatan{{ $student->id }}"><i class="fas fa-calendar-alt me-1 text-primary"></i> Tahun Angkatan</label>
                                                                </div>
                                                                <div class="mb-2">
                                                                    <label class="form-label mb-1"><i class="fas fa-graduation-cap me-1 text-primary"></i> Status Siswa</label>
                                                                    <select name="true_status" class="form-select" id="true_status{{ $student->id }}">
                                                                        <option value="lulus" @if($student->true_status=='lulus') selected @endif>Lulus</option>
                                                                        <option value="lulus bersyarat" @if($student->true_status=='lulus bersyarat') selected @endif>Lulus Bersyarat</option>
                                                                        <option value="tidak lulus" @if($student->true_status=='tidak lulus') selected @endif>Tidak Lulus</option>
                                                                    </select>
                                                                    <div class="mt-2">
                                                                        @if($student->true_status=='lulus')
                                                                            <span class="badge bg-success">Lulus</span>
                                                                        @elseif($student->true_status=='lulus bersyarat')
                                                                            <span class="badge bg-warning text-dark">Lulus Bersyarat</span>
                                                                        @elseif($student->true_status=='tidak lulus')
                                                                            <span class="badge bg-danger">Tidak Lulus</span>
                                                                        @else
                                                                            <span class="badge bg-secondary">-</span>
                                                                        @endif
                                                                    </div>
                                                                </div>
                                                            </div>
                                                            <!-- Nilai/Atribut -->
                                                            <div class="col-md-6">
                                                                <h6 class="fw-semibold mb-3 text-primary"><i class="fas fa-list-ol me-1"></i> Nilai & Atribut</h6>
                                                                <div class="row g-3 mb-2">
                                                                    @for($j=1; $j<=6; $j++)
                                                                        <div class="col-6">
                                                                            <div class="form-floating mb-2">
                                                                                <input type="number" step="0.01" name="values[semester_{{ $j }}]" class="form-control" id="semester_{{ $j }}_{{ $student->id }}" value="{{ $student->studentValues->where('key','semester_'.$j)->first()->value ?? '' }}">
                                                                                <label for="semester_{{ $j }}_{{ $student->id }}"><i class="fas fa-book-open me-1 text-primary"></i> Semester {{ $j }}</label>
                                                                            </div>
                                                                        </div>
                                                                    @endfor
                                                                </div>
                                                                <div class="form-floating mb-3">
                                                                    <input type="number" step="0.01" name="values[usp]" class="form-control" id="usp{{ $student->id }}" value="{{ $student->studentValues->where('key','usp')->first()->value ?? '' }}">
                                                                    <label for="usp{{ $student->id }}"><i class="fas fa-certificate me-1 text-primary"></i> Nilai USP</label>
                                                                </div>
                                                                <div class="form-floating mb-3">
                                                                    <select name="values[sikap]" class="form-select" id="sikap{{ $student->id }}">
                                                                        <option value="baik" @if(($student->studentValues->where('key','sikap')->first()->value ?? '')=='baik') selected @endif>Baik</option>
                                                                        <option value="cukup baik" @if(($student->studentValues->where('key','sikap')->first()->value ?? '')=='cukup baik') selected @endif>Cukup Baik</option>
                                                                        <option value="kurang baik" @if(($student->studentValues->where('key','sikap')->first()->value ?? '')=='kurang baik') selected @endif>Kurang Baik</option>
                                                                    </select>
                                                                    <label for="sikap{{ $student->id }}"><i class="fas fa-smile me-1 text-primary"></i> Sikap</label>
                                                                </div>
                                                                <div class="form-floating mb-3">
                                                                    <select name="values[kerapian]" class="form-select" id="kerapian{{ $student->id }}">
                                                                        <option value="baik" @if(($student->studentValues->where('key','kerapian')->first()->value ?? '')=='baik') selected @endif>Baik</option>
                                                                        <option value="cukup baik" @if(($student->studentValues->where('key','kerapian')->first()->value ?? '')=='cukup baik') selected @endif>Cukup Baik</option>
                                                                        <option value="kurang baik" @if(($student->studentValues->where('key','kerapian')->first()->value ?? '')=='kurang baik') selected @endif>Kurang Baik</option>
                                                                    </select>
                                                                    <label for="kerapian{{ $student->id }}"><i class="fas fa-tshirt me-1 text-primary"></i> Kerapian</label>
                                                                </div>
                                                                <div class="form-floating mb-3">
                                                                    <select name="values[kerajinan]" class="form-select" id="kerajinan{{ $student->id }}">
                                                                        <option value="baik" @if(($student->studentValues->where('key','kerajinan')->first()->value ?? '')=='baik') selected @endif>Baik</option>
                                                                        <option value="cukup baik" @if(($student->studentValues->where('key','kerajinan')->first()->value ?? '')=='cukup baik') selected @endif>Cukup Baik</option>
                                                                        <option value="kurang baik" @if(($student->studentValues->where('key','kerajinan')->first()->value ?? '')=='kurang baik') selected @endif>Kurang Baik</option>
                                                                    </select>
                                                                    <label for="kerajinan{{ $student->id }}"><i class="fas fa-clipboard-check me-1 text-primary"></i> Kerajinan</label>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="modal-footer d-flex flex-column flex-md-row justify-content-end gap-2 mt-2 bg-light">
                                                        <button type="button" class="btn btn-secondary px-4 mb-2 mb-md-0" data-bs-dismiss="modal"><i class="fas fa-arrow-left me-1"></i> Batal</button>
                                                        <button type="submit" class="btn btn-primary px-4"><i class="fas fa-save me-1"></i> Simpan Perubahan</button>
                                                    </div>
                                                </form>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </main>
    <x-plugins></x-plugins>
</x-layout> 