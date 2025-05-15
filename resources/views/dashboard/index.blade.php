<x-layout bodyClass="g-sidenav-show  bg-gray-200">
    <x-navbars.sidebar activePage='dashboard'></x-navbars.sidebar>
    <main class="main-content position-relative max-height-vh-100 h-100 border-radius-lg ">
        <!-- Navbar -->
        <x-navbars.navs.auth titlePage="Dashboard"></x-navbars.navs.auth>
        <!-- End Navbar -->
        <div class="container-fluid py-4">
            <!-- Student Statistics Cards -->
            <div class="row">
                <div class="col-xl-3 col-sm-6 mb-xl-0 mb-4">
                    <div class="card">
                        <div class="card-header p-3 pt-2">
                            <div
                                class="icon icon-lg icon-shape bg-gradient-primary shadow-primary text-center border-radius-xl mt-n4 position-absolute">
                                <i class="material-icons opacity-10">people</i>
                            </div>
                            <div class="text-end pt-1">
                                <p class="text-sm mb-0 text-capitalize">Total Siswa</p>
                                <h4 class="mb-0">{{ $totalStudents }}</h4>
                            </div>
                        </div>
                        <hr class="dark horizontal my-0">
                        <div class="card-footer p-3">
                            <div class="row">
                                <div class="col-6">
                                    <p class="mb-0 text-sm">Training: <span class="font-weight-bold">{{ $graduationStats['training_count'] }}</span></p>
                                </div>
                                <div class="col-6 text-end">
                                    <p class="mb-0 text-sm">Testing: <span class="font-weight-bold">{{ $graduationStats['testing_count'] }}</span></p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-xl-3 col-sm-6 mb-xl-0 mb-4">
                    <div class="card">
                        <div class="card-header p-3 pt-2">
                            <div
                                class="icon icon-lg icon-shape bg-gradient-success shadow-success text-center border-radius-xl mt-n4 position-absolute">
                                <i class="material-icons opacity-10">school</i>
                            </div>
                            <div class="text-end pt-1">
                                <p class="text-sm mb-0 text-capitalize">Lulus</p>
                                <h4 class="mb-0">{{ $graduationStats['lulus'] }}</h4>
                            </div>
                        </div>
                        <hr class="dark horizontal my-0">
                        <div class="card-footer p-3">
                            <p class="mb-0">Siswa dengan status prediksi lulus</p>
                        </div>
                    </div>
                </div>
                <div class="col-xl-3 col-sm-6 mb-xl-0 mb-4">
                    <div class="card">
                        <div class="card-header p-3 pt-2">
                            <div
                                class="icon icon-lg icon-shape bg-gradient-warning shadow-warning text-center border-radius-xl mt-n4 position-absolute">
                                <i class="material-icons opacity-10">assignment_late</i>
                            </div>
                            <div class="text-end pt-1">
                                <p class="text-sm mb-0 text-capitalize">Lulus Bersyarat</p>
                                <h4 class="mb-0">{{ $graduationStats['lulus_bersyarat'] }}</h4>
                            </div>
                        </div>
                        <hr class="dark horizontal my-0">
                        <div class="card-footer p-3">
                            <p class="mb-0">Siswa dengan status prediksi lulus bersyarat</p>
                        </div>
                    </div>
                </div>
                <div class="col-xl-3 col-sm-6">
                    <div class="card">
                        <div class="card-header p-3 pt-2">
                            <div
                                class="icon icon-lg icon-shape bg-gradient-danger shadow-danger text-center border-radius-xl mt-n4 position-absolute">
                                <i class="material-icons opacity-10">cancel</i>
                            </div>
                            <div class="text-end pt-1">
                                <p class="text-sm mb-0 text-capitalize">Tidak Lulus</p>
                                <h4 class="mb-0">{{ $graduationStats['tidak_lulus'] }}</h4>
                            </div>
                        </div>
                        <hr class="dark horizontal my-0">
                        <div class="card-footer p-3">
                            <p class="mb-0">Siswa dengan status prediksi tidak lulus</p>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row mt-4">
                <!-- Pie Chart - Graduation Distribution -->
                <div class="col-lg-5 col-md-6 mt-4 mb-4">
                    <div class="card z-index-2">
                        <div class="card-body">
                            <h6 class="mb-0">Distribusi Prediksi Kelulusan</h6>
                            <p class="text-sm">Persentase status prediksi kelulusan siswa</p>
                            <div class="chart my-4">
                                <canvas id="graduation-distribution-chart" class="chart-canvas bg-white p-2 rounded"
                                    height="250"></canvas>
                            </div>
                            <hr class="dark horizontal">
                            <div class="d-flex">
                                <i class="material-icons text-sm my-auto me-1">schedule</i>
                                <p class="mb-0 text-sm">Data prediksi dari seluruh siswa yang terdaftar</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Bar Chart - Academic vs Non-Academic -->
                <div class="col-lg-7 col-md-6 mt-4 mb-4">
                    <div class="card z-index-2">
                        <div class="card-body">
                            <h6 class="mb-0">Tren Nilai Rata-rata per Semester</h6>
                            <p class="text-sm">Perkembangan nilai siswa dari semester ke semester</p>
                            <div class="chart my-4">
                                <canvas id="semester-trend-chart" class="chart-canvas bg-white p-2 rounded"
                                    height="250"></canvas>
                            </div>
                            <hr class="dark horizontal">
                            <div class="d-flex">
                                <i class="material-icons text-sm my-auto me-1">schedule</i>
                                <p class="mb-0 text-sm">Data tren nilai berdasarkan status prediksi kelulusan</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Semester Average Values -->
            <div class="row mb-4">
                    <div class="col-12">
                    <div class="card">
                        <div class="card-header pb-0">
                                <h6>Rata-Rata Nilai Per Semester</h6>
                                    <p class="text-sm mb-0">
                                    <i class="fa fa-chart-bar text-info" aria-hidden="true"></i>
                                    <span class="font-weight-bold ms-1">Perbandingan nilai rata-rata</span> seluruh
                                    semester
                                </p>
                        </div>
                        <div class="card-body px-0 pb-2">
                                <div class="chart-container" style="position: relative; height:300px; width:100%">
                                    <canvas id="semester-averages-chart"></canvas>
                                </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

            <x-footers.auth></x-footers.auth>
        </div>
    </main>
    <x-plugins></x-plugins>
    @push('js')
    <script src="{{ asset('assets') }}/js/plugins/chartjs.min.js"></script>
    <script>
            document.addEventListener('DOMContentLoaded', function () {
                // Graduation Distribution Chart (Pie Chart)
                var ctx1 = document.getElementById("graduation-distribution-chart").getContext("2d");
                new Chart(ctx1, {
                    type: "pie",
            data: {
                        labels: ["Lulus", "Lulus Bersyarat", "Tidak Lulus"],
                datasets: [{
                            data: [
                                {{ $graduationStats['lulus'] }},
                                {{ $graduationStats['lulus_bersyarat'] }},
                                {{ $graduationStats['tidak_lulus'] }}
                            ],
                            backgroundColor: [
                                'rgba(75, 192, 192, 0.8)',
                                'rgba(255, 205, 86, 0.8)',
                                'rgba(255, 99, 132, 0.8)'
                            ],
                            borderColor: [
                                'rgb(75, 192, 192)',
                                'rgb(255, 205, 86)',
                                'rgb(255, 99, 132)'
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
                                    color: '#343a40'
                                }
                            }
                        }
                    }
                });

                // Semester Trend Chart (Line Chart)
                var ctx2 = document.getElementById("semester-trend-chart").getContext("2d");
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
                
        new Chart(ctx2, {
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

                // Semester Average Values (Bar Chart)
                var ctx3 = document.getElementById("semester-averages-chart").getContext("2d");
                
                // Get the semester values
                const semesterValues = [
                    {{ $semesterAverages['Semester 1'] ?? 0 }},
                    {{ $semesterAverages['Semester 2'] ?? 0 }},
                    {{ $semesterAverages['Semester 3'] ?? 0 }},
                    {{ $semesterAverages['Semester 4'] ?? 0 }},
                    {{ $semesterAverages['Semester 5'] ?? 0 }},
                    {{ $semesterAverages['Semester 6'] ?? 0 }}
                ];
                
                // Calculate max value and round up to nearest whole number
                const maxValue = Math.ceil(Math.max(...semesterValues));
                
        new Chart(ctx3, {
                    type: "bar",
            data: {
                        labels: ["Semester 1", "Semester 2", "Semester 3", "Semester 4", "Semester 5", "Semester 6"],
                datasets: [{
                            label: "Rata-rata Nilai",
                            data: semesterValues,
                            backgroundColor: [
                                'rgba(75, 192, 192, 0.7)',
                                'rgba(54, 162, 235, 0.7)',
                                'rgba(153, 102, 255, 0.7)',
                                'rgba(255, 159, 64, 0.7)',
                                'rgba(255, 99, 132, 0.7)',
                                'rgba(255, 205, 86, 0.7)'
                            ],
                            borderColor: [
                                'rgb(75, 192, 192)',
                                'rgb(54, 162, 235)',
                                'rgb(153, 102, 255)',
                                'rgb(255, 159, 64)',
                                'rgb(255, 99, 132)',
                                'rgb(255, 205, 86)'
                            ],
                            borderWidth: 1
                        }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                scales: {
                    y: {
                                beginAtZero: true,
                                max: maxValue,
                                title: {
                            display: true,
                                    text: 'Nilai Rata-rata'
                                },
                                ticks: {
                                    stepSize: 1,
                                    callback: function(value) {
                                        return value.toFixed(1);
                                    }
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
                                display: false
                            },
                            tooltip: {
                                callbacks: {
                                    label: function(context) {
                                        return `Nilai: ${context.formattedValue}`;
                                    }
                                }
                            }
                        }
                    }
                });
        });
    </script>
    @endpush
</x-layout>