<x-layout bodyClass="g-sidenav-show bg-gray-200">
    <x-navbars.sidebar activePage="visualisasi-data"></x-navbars.sidebar>
    <main class="main-content position-relative max-height-vh-100 h-100 border-radius-lg">
        <!-- Navbar -->
        <x-navbars.navs.auth titlePage="Visualisasi Data"></x-navbars.navs.auth>
        <!-- End Navbar -->
        <div class="container-fluid py-4">
            <!-- Filter Panel -->
            <div class="row mb-4">
                <div class="col-12">
                    <div class="card">
                        <div class="card-header p-3">
                            <h5 class="mb-0">Filter Data Visualisasi</h5>
                        </div>
                        <div class="card-body p-3">
                            <form method="GET" action="{{ route('visualisasi-data') }}" id="filterForm">
                                <div class="row">
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label class="form-label">Filter Status</label>
                                            <select name="status" class="form-select">
                                                <option value="all" {{ $statusFilter === 'all' ? 'selected' : '' }}>Semua Status</option>
                                                <option value="lulus" {{ $statusFilter === 'lulus' ? 'selected' : '' }}>Lulus</option>
                                                <option value="lulus bersyarat" {{ $statusFilter === 'lulus bersyarat' ? 'selected' : '' }}>Lulus Bersyarat</option>
                                                <option value="tidak lulus" {{ $statusFilter === 'tidak lulus' ? 'selected' : '' }}>Tidak Lulus</option>
                                            </select>
                                        </div>
                                    </div>
                                    <!-- Sembunyikan filter tahun angkatan, tetap ada di form sebagai input hidden -->
                                    <div class="col-md-4" style="display:none;">
                                        <div class="form-group">
                                            <label class="form-label">Filter Tahun Angkatan</label>
                                            <select name="tahun_angkatan" class="form-select">
                                                <option value="all" selected>Semua Tahun</option>
                                                @foreach($availableTahunAngkatan as $tahun)
                                                    <option value="{{ $tahun }}" {{ $tahunAngkatanFilter === $tahun ? 'selected' : '' }}>
                                                        {{ $tahun }}
                                                    </option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label class="form-label">Filter Semester</label>
                                            <div class="row">
                                                @for($i = 1; $i <= 6; $i++)
                                                    <div class="col-4">
                                                        <div class="form-check">
                                                            <input type="checkbox" 
                                                        class="form-check-input" 
                                                                name="semester[]" 
                                                        value="{{ $i }}" 
                                                        id="semester{{ $i }}" 
                                                                {{ in_array($i, $semesterFilter) ? 'checked' : '' }}>
                                                    <label class="form-check-label" for="semester{{ $i }}">
                                                        Semester {{ $i }}
                                                    </label>
                                                        </div>
                                                </div>
                                            @endfor
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="row">
                                    <div class="col-12 text-end">
                                        <button type="submit" class="btn btn-primary">
                                            <i class="fas fa-filter me-2"></i>Terapkan Filter
                                        </button>
                                        <a href="{{ route('visualisasi-data') }}" class="btn btn-outline-secondary">
                                            <i class="fas fa-redo me-2"></i>Reset
                                        </a>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Chart Row 1: Pie and Bar Charts -->
            @if(array_sum($statusCounts) === 0)
                <div class="alert alert-warning">Tidak ada data untuk filter yang dipilih.</div>
            @else
            <div class="row mb-4">
                <!-- Pie Chart - Distribution of Graduation Status -->
                <div class="col-lg-6 col-md-12 mb-md-0 mb-4">
                    <div class="card z-index-2 h-100">
                        <div class="card-header pb-0">
                            <h6>Distribusi Status Prediksi Kelulusan</h6>
                            <p class="text-sm mb-0">
                                <i class="fa fa-pie-chart text-success me-1"></i>
                                Persentase hasil prediksi kelulusan siswa
                                <span data-bs-toggle="tooltip" title="Menampilkan distribusi status kelulusan berdasarkan filter."><i class="fas fa-info-circle"></i></span>
                            </p>
                        </div>
                        <div class="card-body p-3">
                            <div class="chart">
                                <canvas id="pie-chart" class="chart-canvas" height="300"></canvas>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Bar Chart - Academic vs Non-Academic -->
                <div class="col-lg-6 col-md-12 mb-md-0 mb-4">
                    <div class="card z-index-2 h-100">
                        <div class="card-header pb-0">
                            <h6>Perbandingan Nilai Akademik vs Non-Akademik</h6>
                            <p class="text-sm mb-0">
                                <i class="fa fa-bar-chart text-info me-1"></i>
                                Rata-rata nilai berdasarkan kategori
                                <span data-bs-toggle="tooltip" title="Menampilkan rata-rata nilai akademik dan non-akademik berdasarkan filter."><i class="fas fa-info-circle"></i></span>
                            </p>
                        </div>
                        <div class="card-body p-3">
                            <div class="chart">
                                <canvas id="academic-bar-chart" class="chart-canvas" height="300"></canvas>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Chart Row 2: Line and Heatmap Charts -->
            <div class="row mb-4">
                <!-- Line Chart - Semester Trends -->
                <div class="col-lg-6 col-md-12 mb-md-0 mb-4">
                    <div class="card z-index-2 h-100">
                        <div class="card-header pb-0">
                            <h6>Tren Nilai Rata-rata per Semester</h6>
                            <p class="text-sm mb-0">
                                <i class="fa fa-line-chart text-primary me-1"></i>
                                Perkembangan nilai siswa dari semester ke semester
                                <span data-bs-toggle="tooltip" title="Menampilkan tren rata-rata nilai per semester berdasarkan filter."><i class="fas fa-info-circle"></i></span>
                            </p>
                        </div>
                        <div class="card-body p-3">
                            <div class="chart">
                                <canvas id="semester-line-chart" class="chart-canvas" height="300"></canvas>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Card Histogram Sebaran Nilai -->
                <div class="col-lg-6 col-md-12 mb-md-0 mb-4">
                    <div class="card z-index-2 h-100">
                        <div class="card-header pb-0">
                            <h6>Histogram Sebaran Nilai</h6>
                            <p class="text-sm mb-0">
                                <i class="fa fa-chart-bar text-primary me-1"></i>
                                Distribusi nilai USP, rata-rata semester, dan non-akademik berdasarkan filter.
                            </p>
                        </div>
                        <div class="card-body p-3">
                            <div class="chart">
                                <canvas id="histogram-chart" class="chart-canvas" height="300"></canvas>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            @endif
            <!-- Tabel Data Siswa Interaktif -->
            <div class="row mt-4">
                <div class="col-12">
                    <div class="card shadow-sm">
                        <div class="card-header bg-light p-3 d-flex justify-content-between align-items-center">
                            <h6 class="mb-0"><i class="fas fa-table me-2"></i>Tabel Data Siswa (Interaktif)</h6>
                        </div>
                        <div class="card-body">
                            @if(isset($studentTableData) && count($studentTableData) > 0)
                                <div class="table-responsive">
                                    <table class="table table-bordered table-hover" id="studentTable">
                                        <thead class="bg-light">
                                            <tr>
                                                <th>Nama</th>
                                                <th>NISN</th>
                                                <th>Tahun Angkatan</th>
                                                <th>Semester 1</th>
                                                <th>Semester 2</th>
                                                <th>Semester 3</th>
                                                <th>Semester 4</th>
                                                <th>Semester 5</th>
                                                <th>Semester 6</th>
                                                <th>USP</th>
                                                <th>Sikap</th>
                                                <th>Kerapian</th>
                                                <th>Kerajinan</th>
                                                <th>Status Prediksi</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($studentTableData as $row)
                                                <tr>
                                                    <td>{{ $row['nama'] }}</td>
                                                    <td>{{ $row['nisn'] }}</td>
                                                    <td>{{ $row['tahun_angkatan'] }}</td>
                                                    <td>{{ $row['semester_1'] ?? '-' }}</td>
                                                    <td>{{ $row['semester_2'] ?? '-' }}</td>
                                                    <td>{{ $row['semester_3'] ?? '-' }}</td>
                                                    <td>{{ $row['semester_4'] ?? '-' }}</td>
                                                    <td>{{ $row['semester_5'] ?? '-' }}</td>
                                                    <td>{{ $row['semester_6'] ?? '-' }}</td>
                                                    <td>{{ $row['usp'] ?? '-' }}</td>
                                                    <td>{{ $row['sikap'] ?? '-' }}</td>
                                                    <td>{{ $row['kerapian'] ?? '-' }}</td>
                                                    <td>{{ $row['kerajinan'] ?? '-' }}</td>
                                                    <td>
                                                        <span class="badge bg-{{ $row['status_prediksi'] == 'lulus' ? 'success' : ($row['status_prediksi'] == 'lulus bersyarat' ? 'warning' : 'danger') }}">
                                                            {{ ucwords($row['status_prediksi']) }}
                                                        </span>
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            @else
                                <div class="alert alert-warning mb-0">Tidak ada data siswa untuk filter ini.</div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
            @push('css')
            <link rel="stylesheet" href="https://cdn.datatables.net/1.13.4/css/jquery.dataTables.min.css">
            <style>
                /* Perbesar search box dan rapikan */
                .dataTables_filter label {
                    width: 100%;
                    display: flex;
                    align-items: center;
                    gap: 0.5rem;
                }
                .dataTables_filter input[type="search"] {
                    width: 300px;
                    max-width: 100%;
                    margin-left: 0.5rem;
                    border-radius: 0.5rem;
                    border: 1px solid #ced4da;
                    padding: 0.5rem 1rem;
                    font-size: 1rem;
                    background: #f8f9fa;
                }
                /* Pagination modern look & Bootstrap style */
                .dataTables_paginate {
                    display: flex;
                    justify-content: center;
                    align-items: center;
                    gap: 0.25rem;
                    margin-top: 1rem;
                }
                .dataTables_paginate .paginate_button {
                    border-radius: 0.5rem !important;
                    margin: 0 4px !important;
                    border: none !important;
                    background: #fff !important;
                    color: #5e72e4 !important;
                    padding: 0.5rem 1.1rem !important;
                    font-weight: 500;
                    font-size: 1.1rem;
                    box-shadow: 0 1px 3px rgba(94,114,228,0.07);
                    transition: background 0.2s, color 0.2s, box-shadow 0.2s;
                    outline: none !important;
                }
                .dataTables_paginate .paginate_button.current,
                .dataTables_paginate .paginate_button:active,
                .dataTables_paginate .paginate_button:hover {
                    background: #5e72e4 !important;
                    color: #fff !important;
                    box-shadow: 0 2px 8px rgba(94,114,228,0.18);
                }
                .dataTables_paginate .paginate_button.disabled {
                    background: #f0f0f0 !important;
                    color: #bdbdbd !important;
                    cursor: not-allowed !important;
                    box-shadow: none !important;
                }
                .dataTables_length select {
                    border-radius: 0.375rem;
                    border: 1px solid #ced4da;
                    padding: 0.2rem 0.7rem;
                    background: #f8f9fa;
                }
                .dataTables_info {
                    margin-top: 0.5rem;
                    color: #6c757d;
                }
            </style>
            @endpush
            @push('js')
            <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
            <script src="https://cdn.datatables.net/1.13.4/js/jquery.dataTables.min.js"></script>
            <script>
                document.addEventListener('DOMContentLoaded', function() {
                    if (window.jQuery && $('#studentTable').length) {
                        $('#studentTable').DataTable({
                            language: {
                                search: '',
                                searchPlaceholder: 'Cari siswa, NISN, status, dll...',
                                lengthMenu: 'Tampilkan _MENU_ entri',
                                info: 'Menampilkan _START_ sampai _END_ dari _TOTAL_ entri',
                                paginate: {
                                    previous: '<span aria-label="Sebelumnya">&laquo;</span>',
                                    next: '<span aria-label="Berikutnya">&raquo;</span>'
                                }
                            },
                            pageLength: 30,
                            dom: '<"row mb-3"<"col-md-6"l><"col-md-6 text-end"f>>rt<"row mt-2"<"col-md-6"i><"col-md-6"p>>',
                        });
                    }
                });
            </script>
            @endpush
        </div>
    </main>

    <x-plugins></x-plugins>

    <!-- Chart.js and other necessary libraries -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <!-- For Heatmap plugin -->
    <script src="https://cdn.jsdelivr.net/npm/chartjs-chart-matrix@1.2.0/dist/chartjs-chart-matrix.min.js"></script>
    
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Ensure all checkboxes aren't unchecked
            document.getElementById('filterForm').addEventListener('submit', function(e) {
                const checkboxes = document.querySelectorAll('input[name="semester[]"]:checked');
                if (checkboxes.length === 0) {
                    e.preventDefault();
                    alert('Silakan pilih minimal satu semester.');
                }
            });
            
            // Aktifkan tooltip Bootstrap
            var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
            tooltipTriggerList.map(function (tooltipTriggerEl) {
                return new bootstrap.Tooltip(tooltipTriggerEl);
            });
            
            // Pie Chart - Graduation Status Distribution
            const pieCtx = document.getElementById('pie-chart').getContext('2d');
            new Chart(pieCtx, {
                type: 'doughnut',
                data: {
                    labels: ['Lulus', 'Lulus Bersyarat', 'Tidak Lulus'],
                    datasets: [{
                        data: [
                            {{ $statusCounts['lulus'] }},
                            {{ $statusCounts['lulus_bersyarat'] }},
                            {{ $statusCounts['tidak_lulus'] }}
                        ],
                        backgroundColor: [
                            'rgba(76, 175, 80, 0.8)',
                            'rgba(255, 152, 0, 0.8)',
                            'rgba(244, 67, 54, 0.8)'
                        ],
                        borderColor: [
                            'rgb(76, 175, 80)',
                            'rgb(255, 152, 0)',
                            'rgb(244, 67, 54)'
                        ],
                        borderWidth: 1
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            position: 'bottom',
                            labels: {
                                padding: 20,
                                usePointStyle: true
                            }
                        },
                        tooltip: {
                            callbacks: {
                                label: function(context) {
                                    const total = context.dataset.data.reduce((a, b) => a + b, 0);
                                    const value = context.raw;
                                    const percentage = total > 0 ? Math.round((value / total) * 100) : 0;
                                    return `${context.label}: ${value} (${percentage}%)`;
                                }
                            }
                        }
                    }
                }
            });
            
            // Bar Chart - Academic vs Non-Academic
            const academicData = @json($acadVsNonAcadData);
            const academicLabels = [];
            const academicValues = [];
            const nonAcademicLabels = [];
            const nonAcademicValues = [];
            
            // Process academic data
            for (const [key, value] of Object.entries(academicData.academic)) {
                let label = key;
                if (key.startsWith('Rata-Rata')) {
                    label = key.replace('Rata-Rata ', '');
                } else if (key === 'usp') {
                    label = 'USP';
                }
                academicLabels.push(label);
                academicValues.push(parseFloat(value));
            }
            
            // Process non-academic data
            for (const [key, value] of Object.entries(academicData.non_academic)) {
                nonAcademicLabels.push(key.charAt(0).toUpperCase() + key.slice(1));
                nonAcademicValues.push(parseFloat(value));
            }
            
            const barCtx = document.getElementById('academic-bar-chart').getContext('2d');
            new Chart(barCtx, {
                type: 'bar',
                data: {
                    labels: [...academicLabels, ...nonAcademicLabels],
                    datasets: [{
                        label: 'Nilai Rata-rata',
                        data: [...academicValues, ...nonAcademicValues],
                        backgroundColor: [
                            // Warna untuk nilai akademik
                            'rgba(54, 162, 235, 0.7)',
                            'rgba(54, 162, 235, 0.6)',
                            'rgba(54, 162, 235, 0.5)',
                            'rgba(54, 162, 235, 0.4)',
                            'rgba(54, 162, 235, 0.3)',
                            'rgba(54, 162, 235, 0.2)',
                            'rgba(75, 192, 192, 0.7)',
                            // Warna untuk nilai non-akademik
                            'rgba(255, 99, 132, 0.7)',
                            'rgba(255, 159, 64, 0.7)',
                            'rgba(153, 102, 255, 0.7)'
                        ],
                        borderColor: 'rgba(54, 162, 235, 1)',
                        borderWidth: 1
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    scales: {
                        y: {
                            beginAtZero: true,
                            max: 3,
                            title: {
                                display: true,
                                text: 'Nilai Rata-rata'
                            }
                        },
                        x: {
                            title: {
                                display: true,
                                text: 'Kategori'
                            }
                        }
                    },
                    plugins: {
                        legend: {
                            display: false
                        },
                        tooltip: {
                            callbacks: {
                                title: function(context) {
                                    const label = context[0].label;
                                    const isNonAcademic = nonAcademicLabels.includes(label);
                                    return `${label} (${isNonAcademic ? 'Non-Akademik' : 'Akademik'})`;
                                },
                                label: function(context) {
                                    const value = context.formattedValue;
                                    const label = context[0].label;
                                    const isNonAcademic = nonAcademicLabels.includes(label);
                                    
                                    if (isNonAcademic) {
                                        const numericValue = parseFloat(value);
                                        let textValue = '';
                                        if (numericValue >= 2.5) textValue = 'Baik';
                                        else if (numericValue >= 1.5) textValue = 'Cukup Baik';
                                        else textValue = 'Kurang Baik';
                                        return [`Nilai: ${value}`, `Kategori: ${textValue}`];
                                    }
                                    return `Nilai: ${value}`;
                                }
                            }
                        }
                    }
                }
            });
            
            // Line Chart - Semester Trends
            const semesterData = @json($semesterTrendData);
            const lineDatasets = [];
            
            // Define colors and labels for each status
            const statusColors = {
                'lulus': {
                    borderColor: 'rgb(76, 175, 80)',
                    backgroundColor: 'rgba(76, 175, 80, 0.1)'
                },
                'lulus bersyarat': {
                    borderColor: 'rgb(255, 152, 0)',
                    backgroundColor: 'rgba(255, 152, 0, 0.1)'
                },
                'tidak lulus': {
                    borderColor: 'rgb(244, 67, 54)',
                    backgroundColor: 'rgba(244, 67, 54, 0.1)'
                }
            };
            
            const statusLabels = {
                'lulus': 'Lulus',
                'lulus bersyarat': 'Lulus Bersyarat',
                'tidak lulus': 'Tidak Lulus'
            };
            
            // Build datasets for line chart
            for (const [status, values] of Object.entries(semesterData.datasets)) {
                if (values.length > 0) {
                    lineDatasets.push({
                        label: statusLabels[status],
                        data: values,
                        borderColor: statusColors[status].borderColor,
                        backgroundColor: statusColors[status].backgroundColor,
                        fill: false,
                        tension: 0.4
                    });
                }
            }
            
            const lineCtx = document.getElementById('semester-line-chart').getContext('2d');
            new Chart(lineCtx, {
                type: 'line',
                data: {
                    labels: ['Semester 1', 'Semester 2', 'Semester 3', 'Semester 4', 'Semester 5', 'Semester 6'],
                    datasets: lineDatasets
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    scales: {
                        y: {
                            beginAtZero: true,
                            max: 100,
                            min: 0,
                            title: {
                                display: true,
                                text: 'Nilai Rata-rata'
                            },
                            ticks: {
                                stepSize: 10
                            }
                        },
                        x: {
                            title: {
                                display: true,
                                text: 'Semester'
                            }
                        }
                    },
                    plugins: {
                        legend: {
                            position: 'bottom',
                            labels: {
                                padding: 20,
                                usePointStyle: true
                            }
                        },
                        tooltip: {
                            callbacks: {
                                label: function(context) {
                                    return `${context.dataset.label}: ${context.formattedValue}`;
                                }
                            }
                        }
                    }
                }
            });
            
            // Data histogram dari backend
            const histogramData = @json($histogramData ?? []);
            if (histogramData && histogramData.labels && histogramData.labels.length > 0) {
                new Chart(document.getElementById('histogram-chart').getContext('2d'), {
                    type: 'bar',
                    data: {
                        labels: histogramData.labels,
                        datasets: histogramData.datasets
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            legend: { display: true },
                            tooltip: { enabled: true }
                        },
                        scales: {
                            x: { title: { display: true, text: 'Nilai' } },
                            y: { title: { display: true, text: 'Jumlah Siswa' }, beginAtZero: true }
                        }
                    }
                });
            } else {
                document.getElementById('histogram-chart').parentElement.innerHTML = '<div class="alert alert-warning mb-0">Tidak ada data untuk histogram pada filter ini.</div>';
            }
        });
    </script>
</x-layout> 