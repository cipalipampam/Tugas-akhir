<x-layout bodyClass="g-sidenav-show bg-gray-200">
    <x-navbars.sidebar activePage="input-data"></x-navbars.sidebar>
    <main class="main-content position-relative max-height-vh-100 h-100 border-radius-lg">
        <div class="container-fluid py-4">
            <form method="POST" action="{{ route('training.update', $student->id) }}">
                @csrf
                <div class="row justify-content-center">
                    <div class="col-12 col-lg-10">
                        <div class="card mb-4">
                            <div class="card-header d-flex align-items-center">
                                <i class="fas fa-user-edit me-2"></i>
                                <div>
                                    <div class="fw-bold fs-5">Edit Data Siswa</div>
                                    <div class="text-muted fs-6">Perbarui data siswa dan nilai/atribut di bawah ini. Pastikan data sudah benar sebelum menyimpan perubahan.</div>
                                </div>
                            </div>
                            <div class="card-body">
                                <div class="row g-4">
                                    <!-- Data Siswa -->
                                    <div class="col-md-6 border-md-end">
                                        <h6 class="fw-semibold mb-3 text-primary"><i class="fas fa-id-card me-1"></i> Data Siswa</h6>
                                        <div class="d-flex align-items-center mb-4">
                                            <img src="https://ui-avatars.com/api/?name={{ urlencode($student->name) }}&background=4f8cff&color=fff&size=64" alt="Avatar" class="rounded-circle me-3 shadow-sm" style="width:56px;height:56px;">
                                            <div>
                                                <div class="fw-semibold" style="font-size:1.15rem;">{{ $student->name }}</div>
                                                <div class="text-muted" style="font-size:0.98rem;">NISN: {{ $student->nisn }}</div>
                                            </div>
                                        </div>
                                        <div class="form-floating mb-3">
                                            <input type="text" name="nisn" class="form-control" id="nisn" value="{{ $student->nisn }}" required>
                                            <label for="nisn" class="text-body"><i class="fas fa-id-badge me-1 text-primary"></i> NISN</label>
                                        </div>
                                        <div class="form-floating mb-3">
                                            <input type="text" name="name" class="form-control" id="name" value="{{ $student->name }}" required>
                                            <label for="name" class="text-body"><i class="fas fa-user me-1 text-primary"></i> Nama</label>
                                        </div>
                                        <div class="form-floating mb-3">
                                            <input type="text" name="tahun_angkatan" class="form-control" id="tahun_angkatan" value="{{ $student->tahun_angkatan }}">
                                            <label for="tahun_angkatan" class="text-body"><i class="fas fa-calendar-alt me-1 text-primary"></i> Tahun Angkatan</label>
                                        </div>
                                        <div class="mb-2">
                                            <label class="form-label mb-1"><i class="fas fa-graduation-cap me-1 text-primary"></i> Status Siswa</label>
                                            <select name="true_status" class="form-select" id="true_status">
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
                                            @for($i=1; $i<=6; $i++)
                                                <div class="col-6">
                                                    <div class="form-floating mb-2">
                                                        <input type="number" step="0.01" name="values[semester_{{ $i }}]" class="form-control" id="semester_{{ $i }}" value="{{ $student->studentValues->where('key','semester_'.$i)->first()->value ?? '' }}">
                                                        <label for="semester_{{ $i }}" class="text-body"><i class="fas fa-book-open me-1 text-primary"></i> Semester {{ $i }}</label>
                                                    </div>
                                                </div>
                                            @endfor
                                        </div>
                                        <div class="form-floating mb-3">
                                            <input type="number" step="0.01" name="values[usp]" class="form-control" id="usp" value="{{ $student->studentValues->where('key','usp')->first()->value ?? '' }}">
                                            <label for="usp" class="text-body"><i class="fas fa-certificate me-1 text-primary"></i> Nilai USP</label>
                                        </div>
                                        <div class="form-floating mb-3">
                                            <select name="values[sikap]" class="form-select" id="sikap">
                                                <option value="baik" @if(($student->studentValues->where('key','sikap')->first()->value ?? '')=='baik') selected @endif>Baik</option>
                                                <option value="cukup baik" @if(($student->studentValues->where('key','sikap')->first()->value ?? '')=='cukup baik') selected @endif>Cukup Baik</option>
                                                <option value="kurang baik" @if(($student->studentValues->where('key','sikap')->first()->value ?? '')=='kurang baik') selected @endif>Kurang Baik</option>
                                            </select>
                                            <label for="sikap" class="text-body"><i class="fas fa-smile me-1 text-primary"></i> Sikap</label>
                                        </div>
                                        <div class="form-floating mb-3">
                                            <select name="values[kerapian]" class="form-select" id="kerapian">
                                                <option value="baik" @if(($student->studentValues->where('key','kerapian')->first()->value ?? '')=='baik') selected @endif>Baik</option>
                                                <option value="cukup baik" @if(($student->studentValues->where('key','kerapian')->first()->value ?? '')=='cukup baik') selected @endif>Cukup Baik</option>
                                                <option value="kurang baik" @if(($student->studentValues->where('key','kerapian')->first()->value ?? '')=='kurang baik') selected @endif>Kurang Baik</option>
                                            </select>
                                            <label for="kerapian" class="text-body"><i class="fas fa-tshirt me-1 text-primary"></i> Kerapian</label>
                                        </div>
                                        <div class="form-floating mb-3">
                                            <select name="values[kerajinan]" class="form-select" id="kerajinan">
                                                <option value="baik" @if(($student->studentValues->where('key','kerajinan')->first()->value ?? '')=='baik') selected @endif>Baik</option>
                                                <option value="cukup baik" @if(($student->studentValues->where('key','kerajinan')->first()->value ?? '')=='cukup baik') selected @endif>Cukup Baik</option>
                                                <option value="kurang baik" @if(($student->studentValues->where('key','kerajinan')->first()->value ?? '')=='kurang baik') selected @endif>Kurang Baik</option>
                                            </select>
                                            <label for="kerajinan" class="text-body"><i class="fas fa-clipboard-check me-1 text-primary"></i> Kerajinan</label>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="d-flex flex-column flex-md-row justify-content-end gap-2 mt-2">
                            <a href="{{ route('training.index') }}" class="btn btn-secondary px-4 mb-2 mb-md-0"><i class="fas fa-arrow-left me-1"></i> Batal</a>
                            <button type="submit" class="btn btn-primary px-4"><i class="fas fa-save me-1"></i> Simpan Perubahan</button>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </main>
    <x-plugins></x-plugins>
</x-layout> 