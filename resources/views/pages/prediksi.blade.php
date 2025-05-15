<x-layout bodyClass="g-sidenav-show bg-gray-200">
    <x-navbars.sidebar activePage="prediksi"></x-navbars.sidebar>

    <main class="main-content position-relative max-height-vh-100 h-100 border-radius-lg">
        <x-navbars.navs.auth titlePage="Prediksi"></x-navbars.navs.auth>

        <div class="container-fluid py-4">
            <!-- Notification Section -->
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
                    <div class="card mb-4">
                        <div class="card-header p-3 d-flex justify-content-between align-items-center">
                            <h6 class="mb-0"><i class="fas fa-brain me-2"></i>Prediksi Kelulusan</h6>
                            <span class="badge bg-gradient-primary">Fuzzy K-NN Algorithm</span>
                        </div>

                        <div class="card-body p-3">
                            <div class="row">
                                <!-- Metode Input Selector -->
                                <div class="col-12 mb-4">
                                    <div class="card border shadow-sm">
                                        <div class="card-header bg-light p-3">
                                            <h6 class="mb-0 text-primary">Metode Input</h6>
                                        </div>
                                        <div class="card-body">
                                            <div class="row">
                                                <div class="col-md-6">
                                                    <div class="form-check mb-3">
                                                        <input class="form-check-input" type="radio" name="input_method"
                                                            id="manual_input" value="manual" checked
                                                            onclick="toggleInputMethod('manual')">
                                                        <label class="form-check-label" for="manual_input">
                                                            <i class="fas fa-keyboard me-2"></i>Input Manual
                                                        </label>
                                                    </div>
                                                </div>
                                                <div class="col-md-6">
                                                    <div class="form-check mb-3">
                                                        <input class="form-check-input" type="radio" name="input_method"
                                                            id="excel_input" value="excel"
                                                            onclick="toggleInputMethod('excel')">
                                                        <label class="form-check-label" for="excel_input">
                                                            <i class="fas fa-file-excel me-2"></i>Upload Excel
                                                        </label>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Manual Input Form -->
                            <form method="POST" action="{{ route('prediction.process') }}" id="manual_form"
                                class="manual-section">
                                @csrf
                                <div class="row">
                                    <!-- Data Siswa -->
                                    <div class="col-md-12 mb-4">
                                        <div class="card border shadow-sm">
                                            <div class="card-header bg-light p-3">
                                                <h6 class="mb-0 text-primary">Data Siswa</h6>
                                            </div>
                                            <div class="card-body">
                                                <div class="row">
                                                    <div class="col-md-6 mb-3">
                                                        <div class="form-group">
                                                            <label for="name" class="form-label">Nama Siswa</label>
                                                            <input type="text" name="data[nama]" class="form-control"
                                                                placeholder="Masukkan Nama Siswa" required>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-6 mb-3">
                                                        <div class="form-group">
                                                            <label for="nisn" class="form-label">NISN</label>
                                                            <input type="text" name="data[nisn]" class="form-control"
                                                                placeholder="Masukkan NISN Siswa" required>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Data Akademik -->
                                    <div class="col-md-8 mb-4">
                                        <div class="card border shadow-sm h-100">
                                            <div class="card-header bg-light p-3">
                                                <h6 class="mb-0 text-primary">Data Akademik</h6>
                                            </div>
                                            <div class="card-body">
                                                <div class="row">
                                                    <div class="col-12">
                                                        <div class="table-responsive">
                                                            <table class="table align-items-center mb-0">
                                                                <thead>
                                                                    <tr>
                                                                        <th
                                                                            class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">
                                                                            Semester</th>
                                                                        <th
                                                                            class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">
                                                                            Nilai Rata-rata</th>
                                                                    </tr>
                                                                </thead>
                                                                <tbody>
                                                                    @for ($i = 1; $i <= 6; $i++)
                                                                        <tr>
                                                                            <td>
                                                                                <div class="d-flex px-2 py-1">
                                                                                    <div
                                                                                        class="d-flex flex-column justify-content-center">
                                                                                        <h6 class="mb-0 text-sm">Semester
                                                                                            {{ $i }}</h6>
                                                                                    </div>
                                                                                </div>
                                                                            </td>
                                                                            <td>
                                                                                <input type="number" step="0.01"
                                                                                    name="data[Rata-Rata Semester {{ $i }}]"
                                                                                    class="form-control form-control-sm"
                                                                                    placeholder="0.00">
                                                                            </td>
                                                                        </tr>
                                                                    @endfor
                                                                    <tr>
                                                                        <td>
                                                                            <div class="d-flex px-2 py-1">
                                                                                <div
                                                                                    class="d-flex flex-column justify-content-center">
                                                                                    <h6 class="mb-0 text-sm">Nilai USP
                                                                                    </h6>
                                                                                </div>
                                                                            </div>
                                                                        </td>
                                                                        <td>
                                                                            <input type="number" step="0.01"
                                                                                name="data[usp]"
                                                                                class="form-control form-control-sm"
                                                                                placeholder="0.00">
                                                                        </td>
                                                                    </tr>
                                                                </tbody>
                                                            </table>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Data Non-Akademik & Parameter -->
                                    <div class="col-md-4 mb-4">
                                        <div class="card border shadow-sm mb-4">
                                            <div class="card-header bg-light p-3">
                                                <h6 class="mb-0 text-primary">Data Non-Akademik</h6>
                                            </div>
                                            <div class="card-body">
                                                <div class="form-group mb-3">
                                                    <label for="sikap" class="form-label">Sikap</label>
                                                    <select name="data[sikap]" class="form-select">
                                                        <option value="">Pilih</option>
                                                        <option value="baik">Baik</option>
                                                        <option value="cukup baik">Cukup Baik</option>
                                                        <option value="kurang baik">Kurang Baik</option>
                                                    </select>
                                                </div>
                                                <div class="form-group mb-3">
                                                    <label for="kerapian" class="form-label">Kerapian</label>
                                                    <select name="data[kerapian]" class="form-select">
                                                        <option value="">Pilih</option>
                                                        <option value="baik">Baik</option>
                                                        <option value="cukup baik">Cukup Baik</option>
                                                        <option value="kurang baik">Kurang Baik</option>
                                                    </select>
                                                </div>
                                                <div class="form-group">
                                                    <label for="kerajinan" class="form-label">Kerajinan</label>
                                                    <select name="data[kerajinan]" class="form-select">
                                                        <option value="">Pilih</option>
                                                        <option value="baik">Baik</option>
                                                        <option value="cukup baik">Cukup Baik</option>
                                                        <option value="kurang baik">Kurang Baik</option>
                                                    </select>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="card border shadow-sm">
                                            <div class="card-header bg-light p-3">
                                                <h6 class="mb-0 text-primary">Parameter</h6>
                                            </div>
                                            <div class="card-body">
                                                <div class="form-group">
                                                    <label for="k_value" class="form-label">Nilai K</label>
                                                    <input type="number" name="k_value" id="k_value"
                                                        class="form-control" value="5" min="1">
                                                    <small class="text-muted">Jumlah tetangga terdekat</small>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="text-center mt-4">
                                    <button type="submit" class="btn btn-primary btn-lg px-5">
                                        <i class="fas fa-brain me-2"></i>Prediksi Kelulusan
                                    </button>
                                </div>
                            </form>

                            <!-- Excel Input Form -->
                            <form method="POST" action="{{ route('prediction.upload.excel') }}"
                                enctype="multipart/form-data" id="excel_form" class="excel-section d-none">
                                @csrf
                                <div class="row">
                                    <div class="col-md-8 mb-4">
                                        <div class="card border shadow-sm">
                                            <div class="card-header bg-light p-3">
                                                <h6 class="mb-0 text-primary">Upload File Excel</h6>
                                            </div>
                                            <div class="card-body">
                                                <div class="row">
                                                    <div class="col-12">
                                                        <div class="form-group mb-3">
                                                            <label for="excel_file" class="form-label">Pilih File
                                                                Excel</label>
                                                            <div class="input-group">
                                                                <input type="file" class="form-control" id="excel_file"
                                                                    name="excel_file" accept=".xlsx, .xls, .csv"
                                                                    required>
                                                                <a href="{{ route('template.download') }}"
                                                                    class="btn btn-outline-primary">
                                                                    <i class="fas fa-download me-1"></i> Template
                                                                </a>
                                                            </div>
                                                            <small class="text-muted">Format file: .xls, .xlsx, atau
                                                                .csv</small>
                                                        </div>

                                                        <div class="alert alert-info">
                                                            <i class="fas fa-info-circle me-2"></i>
                                                            <strong>Panduan Import Excel:</strong>
                                                            <ol class="mb-0 ps-3 mt-2">
                                                                <li>Unduh template Excel menggunakan tombol "Template"
                                                                </li>
                                                                <li>Isi data siswa sesuai format (nama, NISN, nilai
                                                                    semester 1-6, USP, sikap, kerapian, kerajinan)</li>
                                                                <li>Simpan file Excel Anda</li>
                                                                <li>Unggah kembali menggunakan form di atas</li>
                                                            </ol>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="col-md-4 mb-4">
                                        <div class="card border shadow-sm">
                                            <div class="card-header bg-light p-3">
                                                <h6 class="mb-0 text-primary">Parameter</h6>
                                            </div>
                                            <div class="card-body">
                                                <div class="form-group">
                                                    <label for="k_value_excel" class="form-label">Nilai K</label>
                                                    <input type="number" name="k_value" id="k_value_excel"
                                                        class="form-control" value="5" min="1" required>
                                                    <small class="text-muted">Jumlah tetangga terdekat</small>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="text-center mt-4">
                                    <button type="submit" class="btn btn-primary btn-lg px-5">
                                        <i class="fas fa-brain me-2"></i>Prediksi Kelulusan
                                    </button>
                                </div>
                            </form>

                            <!-- Hasil Prediksi -->
                            @if(isset($results) && count($results) > 0)
                                <div class="card shadow-sm mt-4">
                                    <div class="card-header bg-light p-3 d-flex justify-content-between align-items-center">
                                        <h6 class="mb-0"><i class="fas fa-chart-pie me-2"></i>Hasil Prediksi</h6>
                                        <span class="badge bg-primary">KNN
                                            K={{ count($results) > 0 ? count($results) : 0 }}</span>
                                    </div>
                                    <div class="card-body">
                                        <!-- Final Prediction Result -->
                                        @if(isset($finalPrediction))
                                            <div class="alert alert-info mb-4">
                                                <div class="row align-items-center">
                                                    <div class="col-md-6">
                                                        <h5 class="mb-1">
                                                            <i class="fas fa-user-graduate me-2"></i>
                                                            {{ $testStudent->name ?? 'Siswa' }}
                                                            ({{ $testStudent->nisn ?? 'NISN' }})
                                                        </h5>
                                                        <p class="mb-0 small">Prediksi kelulusan berdasarkan data yang diinput
                                                        </p>
                                                    </div>
                                                    <div class="col-md-6 text-md-end mt-3 mt-md-0">
                                                        <h5 class="mb-0">
                                                            Status Prediksi:
                                                            <span
                                                                class="badge bg-{{ $finalPrediction == 'lulus' ? 'success' : ($finalPrediction == 'lulus bersyarat' ? 'warning' : 'danger') }} px-3 py-2">
                                                                {{ ucwords($finalPrediction) }}
                                                            </span>
                                                        </h5>
                                                    </div>
                                                </div>
                                            </div>
                                        @endif
                                        <!-- Nearest Neighbors Table -->
                                        <div class="card border">
                                            <div class="card-header bg-light">
                                                <h6 class="mb-0"><i class="fas fa-users me-2"></i>Data Tetangga Terdekat
                                                </h6>
                                            </div>
                                            <div class="card-body p-0">
                                                <div class="table-responsive">
                                                    <table class="table table-hover mb-0">
                                                        <thead class="bg-light">
                                                            <tr>
                                                                <th class="ps-3">NISN</th>
                                                                <th>Nama</th>
                                                                <th>Status</th>
                                                                <th>Jarak</th>
                                                                <th class="pe-3">Bobot</th>
                                                            </tr>
                                                        </thead>
                                                        <tbody>
                                                            @foreach($results as $result)
                                                                                                            <tr>
                                                                                                                <td class="ps-3">{{ $result['nisn'] }}</td>
                                                                                                                <td>{{ $result['nama'] }}</td>
                                                                                                                <td>
                                                                                                                    <span class="badge bg-{{ 
                                                                                                                                $result['true_status'] == 'lulus' ? 'success' :
                                                                ($result['true_status'] == 'lulus bersyarat' ? 'warning' : 'danger') 
                                                                                                                            }}">
                                                                                                                        {{ ucwords($result['true_status']) }}
                                                                                                                    </span>
                                                                                                                </td>
                                                                                                                <td>{{ number_format($result['distance'], 4) }}</td>
                                                                                                                <td class="pe-3">{{ number_format($result['weight'], 4) }}
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
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </main>

    @push('js')
        <script>
            function toggleInputMethod(method) {
                if (method === 'manual') {
                    document.querySelectorAll('.excel-section').forEach(el => el.classList.add('d-none'));
                    document.querySelectorAll('.manual-section').forEach(el => el.classList.remove('d-none'));
                } else {
                    document.querySelectorAll('.excel-section').forEach(el => el.classList.remove('d-none'));
                    document.querySelectorAll('.manual-section').forEach(el => el.classList.add('d-none'));
                }
            }

            // Form validation
            document.getElementById('manual_form').addEventListener('submit', function (e) {
                const kValue = document.getElementById('k_value').value;

                if (!kValue) {
                    e.preventDefault();
                    alert('Nilai K harus diisi!');
                    return false;
                }

                // Validate NISN and name
                const nisn = document.querySelector('input[name="data[nisn]"]').value;
                const name = document.querySelector('input[name="data[nama]"]').value;

                if (!nisn || !name) {
                    e.preventDefault();
                    alert('NISN dan Nama Siswa harus diisi!');
                    return false;
                }

                // Validate manual inputs
                let valid = true;
                const semesterInputs = document.querySelectorAll('input[name^="data[Rata-Rata Semester"]');
                semesterInputs.forEach(input => {
                    if (!input.value) {
                        valid = false;
                    }
                });

                const uspInput = document.querySelector('input[name="data[usp]"]');
                if (!uspInput.value) {
                    valid = false;
                }

                const selectInputs = document.querySelectorAll('select[name^="data["]');
                selectInputs.forEach(select => {
                    if (!select.value) {
                        valid = false;
                    }
                });

                if (!valid) {
                    e.preventDefault();
                    alert('Semua data harus diisi terlebih dahulu!');
                    return false;
                }
            });

            // Auto-close alerts after 5 seconds
            document.addEventListener('DOMContentLoaded', function () {
                setTimeout(function () {
                    const alerts = document.querySelectorAll('.alert');
                    alerts.forEach(function (alert) {
                        // Create a new bootstrap alert instance and close it
                        const bsAlert = new bootstrap.Alert(alert);
                        bsAlert.close();
                    });
                }, 5000);
            });
        </script>
    @endpush
</x-layout>