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
                                                        <option value="cukup baik">Cukup</option>
                                                        <option value="kurang baik">Kurang</option>
                                                    </select>
                                                </div>
                                                <div class="form-group mb-3">
                                                    <label for="kerapian" class="form-label">Kerapian</label>
                                                    <select name="data[kerapian]" class="form-select">
                                                        <option value="">Pilih</option>
                                                        <option value="baik">Baik</option>
                                                        <option value="cukup baik">Cukup</option>
                                                        <option value="kurang baik">Kurang</option>
                                                    </select>
                                                </div>
                                                <div class="form-group">
                                                    <label for="kerajinan" class="form-label">Kerajinan</label>
                                                    <select name="data[kerajinan]" class="form-select">
                                                        <option value="">Pilih</option>
                                                        <option value="baik">Baik</option>
                                                        <option value="cukup baik">Cukup</option>
                                                        <option value="kurang baik">Kurang</option>
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
                                @if(isset($manualPrediction) && isset($studentValues))
                                    @php
                                        // Ambil nilai rata-rata akademik (semester_1 sampai semester_6)
                                        $akademik = collect(range(1,6))->map(function($i) use ($studentValues) {
                                            return isset($studentValues['semester_'.$i]) ? floatval($studentValues['semester_'.$i]) : null;
                                        })->filter()->avg();
                                        // Ambil nilai non-akademik (kerapian, kerajinan, sikap) dan konversi ke 0, 0.5, 1
                                        $nonAkademikMap = ['kurang baik' => 0, 'cukup baik' => 0.5, 'baik' => 1];
                                        $kerapian = isset($studentValues['kerapian']) ? $nonAkademikMap[strtolower($studentValues['kerapian'])] ?? null : null;
                                        $kerajinan = isset($studentValues['kerajinan']) ? $nonAkademikMap[strtolower($studentValues['kerajinan'])] ?? null : null;
                                        $sikap = isset($studentValues['sikap']) ? $nonAkademikMap[strtolower($studentValues['sikap'])] ?? null : null;
                                        $nonAkademik = collect([$kerapian, $kerajinan, $sikap])->filter(function($v){return $v!==null;})->avg();
                                    @endphp
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
                                            @if(isset($manualPrediction['ratios']) && count($manualPrediction['ratios']) > 0)
                                            <div class="row mt-4">
                                                <div class="col-12">
                                                    <div class="card border shadow-sm">
                                                        <div class="card-header bg-light p-3">
                                                            <h6 class="mb-0 text-primary"><i class="fas fa-chart-line me-2"></i>Kurva Derajat Keanggotaan Fuzzy</h6>
                                                        </div>
                                                        <div class="card-body">
                                                            <canvas id="fuzzyCurveChart" height="120"></canvas>
                                                        </div>
                                                    </div>
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
                                                                        <div class="row align-items-center">
                                                                            <div class="col-8">
                                                                                <div><b>Nama:</b> {{ $pred['name'] }}</div>
                                                                                <div class="mt-2">
                                                                                    <b>Status:</b>
                                                                                    <span class="badge bg-{{ $pred['status'] == 'lulus' ? 'success' : ($pred['status'] == 'lulus bersyarat' ? 'warning' : 'danger') }} px-3 py-2">
                                                                                        {{ ucwords($pred['status']) }}
                                                                    </span>
                                                                </div>
                                                                            </div>
                                                                            @php
                                                                                $predictedClass = $pred['status'] ?? null;
                                                                                $ratio = isset($pred['ratios']) ? collect($pred['ratios'])->firstWhere('class', $predictedClass) : null;
                                                                            @endphp
                                                                            @if($ratio)
                                                                            <div class="col-4">
                                                                                <div class="p-3 border rounded bg-white h-100 d-flex flex-column align-items-center">
                                                                                    <div class="mb-2 text-info" style="font-size: 2rem;"><i class="fas fa-balance-scale"></i></div>
                                                                                    <div class="fw-bold text-secondary">Derajat Keanggotaan</div>
                                                                                    <div class="w-100 mt-2">
                                                                                        <!-- <div><b>{{ ucwords($predictedClass) }}</b></div> -->
                                                                                        <!-- <div>Bobot Fuzzy: {{ number_format($ratio->total_weight, 4) }}</div> -->
                                                                                        <div>μ = {{ number_format($ratio->weight_ratio, 3) }}</div>
                                                                                    </div>
                                                                                </div>
                                                                            </div>
                                                                            @endif
                                                                        </div>
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
        <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
        @if(isset($manualPrediction) && isset($studentValues))
            @php
                // Titik-titik akademik: semester 1-6
                $akademikPoints = collect(range(1,6))->map(function($i) use ($studentValues) {
                    return isset($studentValues['semester_'.$i]) ? [
                        'x' => floatval($studentValues['semester_'.$i]),
                        'y' => null,
                        'label' => 'Semester '.$i
                    ] : null;
                })->filter()->values();
                // Titik-titik non-akademik: kerapian, kerajinan, sikap
                $nonAkademikMap = ['kurang baik' => 0, 'cukup baik' => 0.5, 'baik' => 1];
                $nonAkademikAspek = [
                    'Kerapian' => isset($studentValues['kerapian']) ? $studentValues['kerapian'] : null,
                    'Kerajinan' => isset($studentValues['kerajinan']) ? $studentValues['kerajinan'] : null,
                    'Sikap' => isset($studentValues['sikap']) ? $studentValues['sikap'] : null,
                ];
                $nonAkademikPoints = collect($nonAkademikAspek)->filter(function($v){return $v!==null;})->map(function($v,$k) use ($nonAkademikMap){
                    $x = $nonAkademikMap[strtolower($v)] ?? null;
                    return $x !== null ? ['x'=>$x,'y'=>null,'label'=>$k.' ('.$v.')'] : null;
                })->filter()->values();
            @endphp
        @endif
        @if(isset($manualPrediction['ratios']) && count($manualPrediction['ratios']) > 0)
        <script>
            // Kurva Derajat Keanggotaan Fuzzy dengan X dinormalisasi 0-1
            const fuzzyLabels = @json(collect($manualPrediction['ratios'])->pluck('class'));
            const fuzzyValues = @json(collect($manualPrediction['ratios'])->pluck('weight_ratio'));
            
            // Generate data untuk 3 kurva fuzzy membership (X: 0-1)
            const xValues = Array.from({length: 101}, (_, i) => i / 100); // 0, 0.01, ..., 1.0
            
            function tidakLulusMembership(x) {
                if (x <= 0.6) return 1;
                if (x > 0.6 && x <= 0.65) return (0.65 - x) / (0.05);
                return 0;
            }
            function lulusBersyaratMembership(x) {
                if (x < 0.6) return 0;
                if (x >= 0.6 && x <= 0.65) return (x - 0.6) / (0.05);
                if (x > 0.65 && x <= 0.75) return 1;
                if (x > 0.75 && x <= 0.8) return (0.8 - x) / (0.05);
                return 0;
            }
            function lulusMembership(x) {
                if (x < 0.75) return 0;
                if (x >= 0.75 && x <= 0.8) return (x - 0.75) / (0.05);
                if (x > 0.8 && x <= 1.0) return 1;
                return 0;
            }
            const tidakLulusData = xValues.map(x => tidakLulusMembership(x));
            const lulusBersyaratData = xValues.map(x => lulusBersyaratMembership(x));
            const lulusData = xValues.map(x => lulusMembership(x));

            // Titik prediksi: X = nilai akademik ternormalisasi siswa, Y = membership pada kelas prediksi di X
            const predictedClass = '{{ $manualPrediction['status'] ?? '' }}';
            const akademikNormalized = {{ $akademik_normalized ?? 0 }};
            let dotY = 0;
            if (predictedClass.toLowerCase() === 'tidak lulus') dotY = tidakLulusMembership(akademikNormalized);
            if (predictedClass.toLowerCase() === 'lulus bersyarat') dotY = lulusBersyaratMembership(akademikNormalized);
            if (predictedClass.toLowerCase() === 'lulus') dotY = lulusMembership(akademikNormalized);
            // Ambil μ hasil prediksi dari backend
            @php
                $predictedClass = $manualPrediction['status'] ?? '';
                $ratios = $manualPrediction['ratios'] ?? [];
                $mu_prediksi = 0;
                foreach ($ratios as $ratio) {
                    if (strtolower($ratio->class) == strtolower($predictedClass)) {
                        $mu_prediksi = $ratio->weight_ratio;
                        break;
                    }
                }
            @endphp
            const muPrediksi = {{ $mu_prediksi ?? 0 }};
            // Cari X pada kurva membership kelas prediksi sehingga membership(X) = muPrediksi
            let dotX = 0;
            if (predictedClass.toLowerCase() === 'tidak lulus') {
                for (let i = 0; i < xValues.length; i++) {
                    if (Math.abs(tidakLulusMembership(xValues[i]) - muPrediksi) < 0.01) {
                        dotX = xValues[i];
                        break;
                    }
                }
            }
            if (predictedClass.toLowerCase() === 'lulus bersyarat') {
                for (let i = 0; i < xValues.length; i++) {
                    if (Math.abs(lulusBersyaratMembership(xValues[i]) - muPrediksi) < 0.01) {
                        dotX = xValues[i];
                        break;
                    }
                }
            }
            if (predictedClass.toLowerCase() === 'lulus') {
                for (let i = 0; i < xValues.length; i++) {
                    if (Math.abs(lulusMembership(xValues[i]) - muPrediksi) < 0.01) {
                        dotX = xValues[i];
                        break;
                    }
                }
            }
            // Data untuk dot
            const predictionDotData = xValues.map((x, idx) => (Math.abs(x - dotX) < 0.005 ? muPrediksi : null));

            const ctxFuzzy = document.getElementById('fuzzyCurveChart').getContext('2d');
            new Chart(ctxFuzzy, {
                type: 'line',
                data: {
                    labels: xValues,
                    datasets: [
                        {
                            label: 'Tidak Lulus',
                            data: tidakLulusData,
                            borderColor: 'rgb(244, 67, 54)',
                            backgroundColor: 'rgba(244, 67, 54, 0.1)',
                            borderWidth: 2,
                            fill: true,
                            tension: 0.1,
                            pointRadius: 0
                        },
                        {
                            label: 'Lulus Bersyarat',
                            data: lulusBersyaratData,
                            borderColor: 'rgb(255, 152, 0)',
                            backgroundColor: 'rgba(255, 152, 0, 0.1)',
                            borderWidth: 2,
                            fill: true,
                            tension: 0.1,
                            pointRadius: 0
                        },
                        {
                            label: 'Lulus',
                            data: lulusData,
                            borderColor: 'rgb(76, 175, 80)',
                            backgroundColor: 'rgba(76, 175, 80, 0.1)',
                            borderWidth: 2,
                            fill: true,
                            tension: 0.1,
                            pointRadius: 0
                        },
                        {
                            label: 'Prediksi: ' + predictedClass,
                            data: predictionDotData,
                            borderColor: 'rgb(33, 150, 243)',
                            backgroundColor: 'rgb(33, 150, 243)',
                            borderWidth: 3,
                            fill: false,
                            tension: 0,
                            pointRadius: 8,
                            pointStyle: 'circle',
                            pointHoverRadius: 10
                        }
                    ]
                },
                options: {
                    responsive: true,
                    plugins: {
                        legend: {
                            display: true,
                            position: 'top'
                        },
                        tooltip: {
                            callbacks: {
                                label: function(context) {
                                    if (context.dataset.label.startsWith('Prediksi:')) {
                                        return context.dataset.label + ' (x = ' + dotX.toFixed(3) + ', μ = ' + muPrediksi.toFixed(3) + ')';
                                    }
                                    return context.dataset.label + ': μ = ' + context.formattedValue;
                                }
                            }
                        }
                    },
                    scales: {
                        x: {
                            title: {
                                display: true,
                                text: 'Nilai Akademik Ternormalisasi (0-1)'
                            },
                            min: 0,
                            max: 1,
                            ticks: {
                                stepSize: 0.05
                            }
                        },
                        y: {
                            title: {
                            display: true,
                                text: 'Derajat Keanggotaan (μ)'
                            },
                            beginAtZero: true,
                            max: 1
                        }
                    }
                }
            });
        </script>
        @endif
    @endpush
</x-layout>