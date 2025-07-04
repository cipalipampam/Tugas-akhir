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
                                                            id="manual_input" value="manual"
                                                            {{ ($activeInputMethod ?? 'manual') == 'manual' ? 'checked' : '' }}
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
                                                            {{ ($activeInputMethod ?? '') == 'excel' ? 'checked' : '' }}
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

                            <!-- Manual Input Form & Hasil Manual -->
                            <div id="manual-section" class="manual-section {{ ($activeInputMethod ?? 'manual') == 'manual' ? '' : 'd-none' }}">
                                <form method="POST" action="{{ route('prediction.process') }}" id="manual_form">
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
                                                                                    <input type="number" step="0.1" min="0" max="100"
                                                                                    name="data[semester_{{ $i }}]"
                                                                                    class="form-control form-control-sm"
                                                                                    placeholder="0.00" 
                                                                                    oninput="validasiInput(this)">
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
                                @if(isset($manualPrediction))
                                    <div class="card shadow-sm mt-4">
                                        <div class="card-header bg-light p-3 d-flex justify-content-between align-items-center">
                                            <h6 class="mb-0"><i class="fas fa-chart-pie me-2"></i>Hasil Prediksi (Manual)</h6>
                                        </div>
                                        <div class="card-body">
                                            <div class="row text-center justify-content-center align-items-center g-3">
                                                <div class="col-md-3 col-12">
                                                    <div class="p-3 border rounded bg-white h-100 d-flex flex-column align-items-center">
                                                        <div class="mb-2 text-primary" style="font-size: 2rem;"><i class="fas fa-id-card"></i></div>
                                                        <div class="fw-bold text-secondary">NISN</div>
                                                        <div class="fs-5">{{ $manualPrediction['nisn'] }}</div>
                                                    </div>
                                                </div>
                                                <div class="col-md-3 col-12">
                                                    <div class="p-3 border rounded bg-white h-100 d-flex flex-column align-items-center">
                                                        <div class="mb-2 text-success" style="font-size: 2rem;"><i class="fas fa-user"></i></div>
                                                        <div class="fw-bold text-secondary">Nama</div>
                                                        <div class="fs-5">{{ $manualPrediction['name'] }}</div>
                                                    </div>
                                                </div>
                                                <div class="col-md-3 col-12">
                                                    <div class="p-3 border rounded bg-white h-100 d-flex flex-column align-items-center">
                                                        <div class="mb-2 text-warning" style="font-size: 2rem;"><i class="fas fa-graduation-cap"></i></div>
                                                        <div class="fw-bold text-secondary">Status Prediksi</div>
                                                        <div>
                                                            <span class="badge bg-{{ $manualPrediction['status'] == 'lulus' ? 'success' : ($manualPrediction['status'] == 'lulus bersyarat' ? 'warning' : 'danger') }} px-4 py-2 fs-6 shadow">
                                                                {{ ucwords($manualPrediction['status']) }}
                                                            </span>
                                                        </div>
                                                    </div>
                                                </div>
                                                @php
                                                    $predictedClass = $manualPrediction['status'] ?? null;
                                                    $ratio = isset($manualPrediction['ratios']) ? collect($manualPrediction['ratios'])->firstWhere('class', $predictedClass) : null;
                                                @endphp
                                                @if($ratio)
                                                    <div class="col-md-3 col-12">
                                                        <div class="p-3 border rounded bg-white h-100 d-flex flex-column align-items-center">
                                                            <div class="mb-2 text-info" style="font-size: 2rem;"><i class="fas fa-balance-scale"></i></div>
                                                            <div class="fw-bold text-secondary">Derajat Keanggotaan Fuzzy</div>
                                                            <div class="w-100 mt-2">
                                                               
                                                                <div>μ = {{ number_format($ratio->weight_ratio, 3) }}</div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                @endif
                                            </div>
                                            @if(isset($manualPrediction['neighbors']) && count($manualPrediction['neighbors']) > 0)
                                                <div class="mt-4">
                                                    <b>Data Tetangga Terdekat:</b>
                                                    <div class="table-responsive mt-2">
                                                        <table class="table table-sm table-bordered mb-0">
                                                            <thead class="bg-light">
                                                                <tr>
                                                                    <th>NISN</th>
                                                                    <th>Nama</th>
                                                                    <th>Status</th>
                                                                    <th>Jarak</th>
                                                                    <th>Bobot</th>
                                                                </tr>
                                                            </thead>
                                                            <tbody>
                                                                @foreach($manualPrediction['neighbors'] as $neighbor)
                                                                    <tr>
                                                                        <td>{{ $neighbor['nisn'] }}</td>
                                                                        <td>{{ $neighbor['nama'] }}</td>
                                                                        <td>
                                                                            <span class="badge bg-{{ $neighbor['true_status'] == 'lulus' ? 'success' : ($neighbor['true_status'] == 'lulus bersyarat' ? 'warning' : 'danger') }}">
                                                                                {{ ucwords($neighbor['true_status']) }}
                                                                            </span>
                                                                        </td>
                                                                        <td>{{ number_format($neighbor['distance'], 4) }}</td>
                                                                        <td>{{ number_format($neighbor['weight'], 4) }}</td>
                                                                    </tr>
                                                                @endforeach
                                                            </tbody>
                                                        </table>
                                                    </div>
                                                </div>
                                            @endif
                                        </div>
                                    </div>
                                @endif
                            </div>

                            <!-- Excel Input Form & Hasil Excel -->
                            <div id="excel-section" class="excel-section {{ ($activeInputMethod ?? '') == 'excel' ? '' : 'd-none' }}">
                                <form method="POST" action="{{ route('prediction.upload.excel') }}" enctype="multipart/form-data">
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
                                @if(isset($excelPredictions) && count($excelPredictions) > 0)
                                    <div class="row mt-4">
                                        <div class="col-12">
                                            <div class="card shadow-sm">
                                    <div class="card-header bg-light p-3 d-flex justify-content-between align-items-center">
                                                    <h6 class="mb-0"><i class="fas fa-users me-2"></i>Hasil Prediksi Siswa (Batch Excel)</h6>
                                                    <span class="badge bg-primary">Total: {{ count($excelPredictions) }}</span>
                                                </div>
                                                <div class="card-body">
                                                    <div class="row">
                                                        @foreach($excelPredictions as $pred)
                                                            <div class="col-md-4 mb-4">
                                                                <div class="card border shadow-sm h-100">
                                                <div class="card-header bg-light">
                                                                        <h6 class="mb-0">NISN: {{ $pred['nisn'] }}</h6>
                                                </div>
                                                                    <div class="card-body">
                                                                        <div><b>Nama:</b> {{ $pred['name'] }}</div>
                                                                        <div class="mt-2">
                                                                            <b>Status:</b>
                                                                            <span class="badge bg-{{ $pred['status'] == 'lulus' ? 'success' : ($pred['status'] == 'lulus bersyarat' ? 'warning' : 'danger') }} px-3 py-2">
                                                                                {{ ucwords($pred['status']) }}
                                                                    </span>
                                                                </div>
                                                                        @php
                                                                            $predictedClass = $pred['status'] ?? null;
                                                                            $ratio = isset($pred['ratios']) ? collect($pred['ratios'])->firstWhere('class', $predictedClass) : null;
                                                                        @endphp
                                                                        @if($ratio)
                                                                            <div class="col-md-3 col-12">
                                                                                <div class="p-3 border rounded bg-white h-100 d-flex flex-column align-items-center">
                                                                                    <div class="mb-2 text-info" style="font-size: 2rem;"><i class="fas fa-balance-scale"></i></div>
                                                                                    <div class="fw-bold text-secondary">Fuzzy KNN</div>
                                                                                    <div class="w-100 mt-2">
                                                                                        <div><b>{{ ucwords($predictedClass) }}</b></div>
                                                                                        <div>Bobot Fuzzy: {{ number_format($ratio->total_weight, 4) }}</div>
                                                                                        <div>μ = {{ number_format($ratio->weight_ratio, 3) }}</div>
                                                                                    </div>
                                                                                </div>
                                                                            </div>
                                                                        @endif
                                                                        @if(isset($pred['neighbors']) && count($pred['neighbors']) > 0)
                                                                            <div class="mt-3">
                                                                                <b>Data Tetangga Terdekat:</b>
                                                                                <div class="table-responsive mt-2">
                                                                                    <table class="table table-sm table-bordered mb-0">
                                                        <thead class="bg-light">
                                                            <tr>
                                                                                                <th>NISN</th>
                                                                <th>Nama</th>
                                                                <th>Status</th>
                                                                <th>Jarak</th>
                                                                                                <th>Bobot</th>
                                                            </tr>
                                                        </thead>
                                                        <tbody>
                                                                                            @foreach($pred['neighbors'] as $neighbor)
                                                                                                <tr>
                                                                                                    <td>{{ $neighbor['nisn'] }}</td>
                                                                                                    <td>{{ $neighbor['nama'] }}</td>
                                                                                                    <td>
                                                                                                        <span class="badge bg-{{ $neighbor['true_status'] == 'lulus' ? 'success' : ($neighbor['true_status'] == 'lulus bersyarat' ? 'warning' : 'danger') }}">
                                                                                                            {{ ucwords($neighbor['true_status']) }}
                                                                                                                    </span>
                                                                                                                </td>
                                                                                                    <td>{{ number_format($neighbor['distance'], 4) }}</td>
                                                                                                    <td>{{ number_format($neighbor['weight'], 4) }}</td>
                                                                                                            </tr>
                                                            @endforeach
                                                        </tbody>
                                                    </table>
                                                                                </div>
                                                                            </div>
                                                                        @endif
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        @endforeach
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
        </div>
    </main>
    <x-plugins></x-plugins>

    @push('js')
        <script>
            function validasiInput(el) {
    let value = parseFloat(el.value);
    if (isNaN(value)) {
        el.value = '';
        return;
    }
    if (value < 0) el.value = 0;
    if (value > 100) el.value = 100;
}
            function toggleInputMethod(method) {
                document.getElementById('manual-section').classList.add('d-none');
                document.getElementById('excel-section').classList.add('d-none');
                if (method === 'manual') {
                    document.getElementById('manual-section').classList.remove('d-none');
                } else {
                    document.getElementById('excel-section').classList.remove('d-none');
                }
            }

            // On page load, set active section
            document.addEventListener('DOMContentLoaded', function () {
                var active = '{{ $activeInputMethod ?? 'manual' }}';
                toggleInputMethod(active);
            });

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
                const semesterInputs = document.querySelectorAll('input[name^="data[semester_"]');
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