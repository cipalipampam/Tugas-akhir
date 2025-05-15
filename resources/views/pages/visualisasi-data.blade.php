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
                                    <div class="col-md-6 mb-3">
                                        <label for="status" class="form-label">Status Prediksi Kelulusan</label>
                                        <select class="form-select" id="status" name="status">
                                            <option value="all" {{ $statusFilter == 'all' ? 'selected' : '' }}>Semua</option>
                                            <option value="lulus" {{ $statusFilter == 'lulus' ? 'selected' : '' }}>Lulus</option>
                                            <option value="lulus bersyarat" {{ $statusFilter == 'lulus bersyarat' ? 'selected' : '' }}>Lulus Bersyarat</option>
                                            <option value="tidak lulus" {{ $statusFilter == 'tidak lulus' ? 'selected' : '' }}>Tidak Lulus</option>
                                        </select>
                                    </div>
                                    
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label d-block">Semester</label>
                                        <div class="d-flex flex-wrap">
                                            @for ($i = 1; $i <= 6; $i++)
                                                <div class="form-check me-3">
                                                    <input 
                                                        class="form-check-input" 
                                                        type="checkbox" 
                                                        value="{{ $i }}" 
                                                        id="semester{{ $i }}" 
                                                        name="semester[]" 
                                                        {{ in_array($i, $semesterFilter) ? 'checked' : '' }}
                                                    >
                                                    <label class="form-check-label" for="semester{{ $i }}">
                                                        Semester {{ $i }}
                                                    </label>
                                                </div>
                                            @endfor
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
            <div class="row mb-4">
                <!-- Pie Chart - Distribution of Graduation Status -->
                <div class="col-lg-6 col-md-12 mb-md-0 mb-4">
                    <div class="card z-index-2 h-100">
                        <div class="card-header pb-0">
                            <h6>Distribusi Status Prediksi Kelulusan</h6>
                            <p class="text-sm mb-0">
                                <i class="fa fa-pie-chart text-success me-1"></i>
                                Persentase hasil prediksi kelulusan siswa
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
                            </p>
                        </div>
                        <div class="card-body p-3">
                            <div class="chart">
                                <canvas id="semester-line-chart" class="chart-canvas" height="300"></canvas>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Heatmap - Correlation Between Values -->
                <div class="col-lg-6 col-md-12 mb-md-0 mb-4">
                    <div class="card z-index-2 h-100">
                        <div class="card-header pb-0">
                            <h6>Korelasi Antar Nilai</h6>
                            <p class="text-sm mb-0">
                                <i class="fa fa-th text-warning me-1"></i>
                                Hubungan antara nilai-nilai akademik dan non-akademik
                            </p>
                        </div>
                        <div class="card-body p-3">
                            <div class="chart">
                                <canvas id="heatmap-chart" class="chart-canvas" height="300"></canvas>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
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
                academicLabels.push(key.charAt(0).toUpperCase() + key.slice(1));
                academicValues.push(parseFloat(value));
            }
            
            const barCtx = document.getElementById('academic-bar-chart').getContext('2d');
            new Chart(barCtx, {
                type: 'bar',
                data: {
                    labels: academicLabels,
                    datasets: [{
                        label: 'Nilai Rata-rata',
                        data: academicValues,
                        backgroundColor: [
                            'rgba(54, 162, 235, 0.7)',
                            'rgba(54, 162, 235, 0.6)',
                            'rgba(54, 162, 235, 0.5)',
                            'rgba(54, 162, 235, 0.4)',
                            'rgba(54, 162, 235, 0.3)',
                            'rgba(54, 162, 235, 0.2)',
                            'rgba(75, 192, 192, 0.7)',
                            'rgba(153, 102, 255, 0.7)',
                            'rgba(255, 159, 64, 0.7)',
                            'rgba(255, 99, 132, 0.7)'
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
                                    return context[0].label;
                                },
                                label: function(context) {
                                    return `Nilai: ${context.formattedValue}`;
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
                    labels: semesterData.labels,
                    datasets: lineDatasets
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    scales: {
                        y: {
                            beginAtZero: false,
                            title: {
                                display: true,
                                text: 'Nilai Rata-rata'
                            }
                        },
                        x: {
                            title: {
                                display: true,
                                text: 'Semester'
                            }
                        }
                    }
                }
            });
            
            // Heatmap - Correlation Matrix
            const correlationData = @json($correlationData);
            const heatmapData = [];
            
            // Transform the correlation matrix into the format needed for the heatmap
            for (let i = 0; i < correlationData.labels.length; i++) {
                for (let j = 0; j < correlationData.labels.length; j++) {
                    const value = correlationData.data[correlationData.labels[i]][correlationData.labels[j]];
                    heatmapData.push({
                        x: correlationData.labels[j],
                        y: correlationData.labels[i],
                        v: value
                    });
                }
            }
            
            // Create a custom heatmap chart
            const heatmapCtx = document.getElementById('heatmap-chart').getContext('2d');
            
            const getColor = function(value) {
                // Define colors based on correlation strength
                // Blue for positive, red for negative correlations
                if (value === 1) return 'rgba(0, 0, 0, 0.8)';
                if (value > 0.7) return 'rgba(0, 100, 255, 0.9)';
                if (value > 0.5) return 'rgba(0, 150, 255, 0.8)';
                if (value > 0.3) return 'rgba(100, 200, 255, 0.7)';
                if (value > 0.1) return 'rgba(150, 225, 255, 0.6)';
                if (value > -0.1) return 'rgba(255, 255, 255, 0.5)';
                if (value > -0.3) return 'rgba(255, 200, 200, 0.6)';
                if (value > -0.5) return 'rgba(255, 150, 150, 0.7)';
                if (value > -0.7) return 'rgba(255, 100, 100, 0.8)';
                return 'rgba(255, 50, 50, 0.9)';
            };
            
            new Chart(heatmapCtx, {
                type: 'scatter',
                data: {
                    datasets: [{
                        label: 'Korelasi',
                        data: heatmapData,
                        backgroundColor: function(context) {
                            if (!context.dataset.data[context.dataIndex]) return 'rgba(0, 0, 0, 0)';
                            const value = context.dataset.data[context.dataIndex].v;
                            return getColor(value);
                        },
                        pointRadius: 15,
                        pointHoverRadius: 20
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    scales: {
                        y: {
                            type: 'category',
                            labels: correlationData.labels.slice().reverse(),
                            offset: true,
                            ticks: {
                                callback: function(value) {
                                    return value;
                                }
                            },
                            grid: {
                                display: false
                            }
                        },
                        x: {
                            type: 'category',
                            labels: correlationData.labels,
                            offset: true,
                            ticks: {
                                callback: function(value) {
                                    return value;
                                }
                            },
                            grid: {
                                display: false
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
                                    const data = context[0].dataset.data[context[0].dataIndex];
                                    return `${data.y} vs ${data.x}`;
                                },
                                label: function(context) {
                                    const value = context.dataset.data[context.dataIndex].v;
                                    const formattedValue = parseFloat(value).toFixed(2);
                                    let strength = '';
                                    
                                    if (Math.abs(value) >= 0.7) strength = 'Sangat Kuat';
                                    else if (Math.abs(value) >= 0.5) strength = 'Kuat';
                                    else if (Math.abs(value) >= 0.3) strength = 'Moderat';
                                    else if (Math.abs(value) >= 0.1) strength = 'Lemah';
                                    else strength = 'Sangat Lemah';
                                    
                                    return [`Korelasi: ${formattedValue}`, `Kekuatan: ${strength}`];
                                }
                            }
                        }
                    }
                }
            });
        });
    </script>
</x-layout> 