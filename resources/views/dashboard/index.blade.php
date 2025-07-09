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
                            <div class="icon icon-lg icon-shape bg-gradient-secondary shadow-secondary text-center border-radius-xl mt-n4 position-absolute">
                                <i class="material-icons opacity-10">assignment</i>
                            </div>
                            <div class="text-end pt-1">
                                <p class="text-sm mb-0 text-capitalize">Siswa Testing</p>
                                <h4 class="mb-0">{{ $testingStats['count'] }}</h4>
                            </div>
                        </div>
                        <hr class="dark horizontal my-0">
                        <div class="card-footer p-3">
                            <div class="row">
                                <div class="col-12">
                                    <p class="mb-0 text-sm">Jumlah siswa data testing</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-xl-3 col-sm-6 mb-xl-0 mb-4">
                    <div class="card">
                        <div class="card-header p-3 pt-2">
                            <div class="icon icon-lg icon-shape bg-gradient-success shadow-success text-center border-radius-xl mt-n4 position-absolute">
                                <i class="material-icons opacity-10">check_circle</i>
                            </div>
                            <div class="text-end pt-1">
                                <p class="text-sm mb-0 text-capitalize">Lulus (Testing)</p>
                                <h4 class="mb-0">{{ $testingStats['lulus'] }}</h4>
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
                            <div class="icon icon-lg icon-shape bg-gradient-warning shadow-warning text-center border-radius-xl mt-n4 position-absolute">
                                <i class="material-icons opacity-10">assignment_late</i>
                            </div>
                            <div class="text-end pt-1">
                                <p class="text-sm mb-0 text-capitalize">Lulus Bersyarat (Testing)</p>
                                <h4 class="mb-0">{{ $testingStats['lulus_bersyarat'] }}</h4>
                            </div>
                        </div>
                        <hr class="dark horizontal my-0">
                        <div class="card-footer p-3">
                            <p class="mb-0">Siswa dengan status prediksi lulus bersyarat</p>
                        </div>
                    </div>
                </div>
                <div class="col-xl-3 col-sm-6 mb-xl-0 mb-4">
                    <div class="card">
                        <div class="card-header p-3 pt-2">
                            <div class="icon icon-lg icon-shape bg-gradient-danger shadow-danger text-center border-radius-xl mt-n4 position-absolute">
                                <i class="material-icons opacity-10">cancel</i>
                            </div>
                            <div class="text-end pt-1">
                                <p class="text-sm mb-0 text-capitalize">Tidak Lulus (Testing)</p>
                                <h4 class="mb-0">{{ $testingStats['tidak_lulus'] }}</h4>
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
                <div class="col-xl-3 col-sm-6 mb-xl-0 mb-4">
                    <div class="card">
                        <div class="card-header p-3 pt-2">
                            <div class="icon icon-lg icon-shape bg-gradient-info shadow-info text-center border-radius-xl mt-n4 position-absolute">
                                <i class="material-icons opacity-10">school</i>
                            </div>
                            <div class="text-end pt-1">
                                <p class="text-sm mb-0 text-capitalize">Siswa Training</p>
                                <h4 class="mb-0">{{ $trainingStats['count'] }}</h4>
                            </div>
                        </div>
                        <hr class="dark horizontal my-0">
                        <div class="card-footer p-3">
                            <div class="row">
                                <div class="col-12">
                                    <p class="mb-0 text-sm">Jumlah siswa data training</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-xl-3 col-sm-6 mb-xl-0 mb-4">
                    <div class="card">
                        <div class="card-header p-3 pt-2">
                            <div class="icon icon-lg icon-shape bg-gradient-success shadow-success text-center border-radius-xl mt-n4 position-absolute">
                                <i class="material-icons opacity-10">check_circle</i>
                            </div>
                            <div class="text-end pt-1">
                                <p class="text-sm mb-0 text-capitalize">Lulus (Training)</p>
                                <h4 class="mb-0">{{ $trainingStats['lulus'] }}</h4>
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
                            <div class="icon icon-lg icon-shape bg-gradient-warning shadow-warning text-center border-radius-xl mt-n4 position-absolute">
                                <i class="material-icons opacity-10">assignment_late</i>
                            </div>
                            <div class="text-end pt-1">
                                <p class="text-sm mb-0 text-capitalize">Lulus Bersyarat (Training)</p>
                                <h4 class="mb-0">{{ $trainingStats['lulus_bersyarat'] }}</h4>
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
                            <div class="icon icon-lg icon-shape bg-gradient-danger shadow-danger text-center border-radius-xl mt-n4 position-absolute">
                                <i class="material-icons opacity-10">cancel</i>
                            </div>
                            <div class="text-end pt-1">
                                <p class="text-sm mb-0 text-capitalize">Tidak Lulus (Training)</p>
                                <h4 class="mb-0">{{ $trainingStats['tidak_lulus'] }}</h4>
                            </div>
                        </div>
                        <hr class="dark horizontal my-0">
                        <div class="card-footer p-3">
                            <p class="mb-0">Siswa dengan status prediksi tidak lulus</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- CHARTS DATA TESTING -->
            <div class="row mt-4">
                <!-- Pie Chart - Graduation Distribution (Testing) -->
                <div class="col-lg-6 col-md-6 mt-4 mb-4">
                    <div class="card z-index-2">
                        <div class="card-body">
                            <h6 class="mb-0">Distribusi Prediksi Kelulusan (Testing)</h6>
                            <p class="text-sm">Persentase status prediksi kelulusan siswa testing</p>
                            <div class="chart my-4">
                                <canvas id="graduation-distribution-chart-testing" class="chart-canvas bg-white p-2 rounded" height="250"></canvas>
                            </div>
                            <hr class="dark horizontal">
                            <div class="d-flex">
                                <i class="material-icons text-sm my-auto me-1">schedule</i>
                                <p class="mb-0 text-sm">Data prediksi dari seluruh siswa testing</p>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- Line Chart - Semester Trend (Testing) -->
                <div class="col-lg-6 col-md-6 mt-4 mb-4">
                    <div class="card z-index-2">
                        <div class="card-body">
                            <h6 class="mb-0">Tren Nilai Rata-rata per Semester (Testing)</h6>
                            <p class="text-sm">Perkembangan nilai siswa testing dari semester ke semester</p>
                            <div class="chart my-4">
                                <canvas id="semester-trend-chart-testing" class="chart-canvas bg-white p-2 rounded" height="250"></canvas>
                            </div>
                            <hr class="dark horizontal">
                            <div class="d-flex">
                                <i class="material-icons text-sm my-auto me-1">schedule</i>
                                <p class="mb-0 text-sm">Data tren nilai berdasarkan status prediksi kelulusan siswa testing</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <!-- CHARTS DATA TRAINING -->
            <div class="row mt-4">
                <!-- Pie Chart - Graduation Distribution (Training) -->
                <div class="col-lg-6 col-md-6 mt-4 mb-4">
                    <div class="card z-index-2">
                        <div class="card-body">
                            <h6 class="mb-0">Distribusi Status Kelulusan (Training)</h6>
                            <p class="text-sm">Persentase status kelulusan siswa training</p>
                            <div class="chart my-4">
                                <canvas id="graduation-distribution-chart-training" class="chart-canvas bg-white p-2 rounded" height="250"></canvas>
                            </div>
                            <hr class="dark horizontal">
                            <div class="d-flex">
                                <i class="material-icons text-sm my-auto me-1">schedule</i>
                                <p class="mb-0 text-sm">Data status kelulusan dari seluruh siswa training</p>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- Line Chart - Semester Trend (Training) -->
                <div class="col-lg-6 col-md-6 mt-4 mb-4">
                    <div class="card z-index-2">
                        <div class="card-body">
                            <h6 class="mb-0">Tren Nilai Rata-rata per Semester (Training)</h6>
                            <p class="text-sm">Perkembangan nilai siswa training dari semester ke semester</p>
                            <div class="chart my-4">
                                <canvas id="semester-trend-chart-training" class="chart-canvas bg-white p-2 rounded" height="250"></canvas>
                            </div>
                            <hr class="dark horizontal">
                            <div class="d-flex">
                                <i class="material-icons text-sm my-auto me-1">schedule</i>
                                <p class="mb-0 text-sm">Data tren nilai berdasarkan status kelulusan siswa training</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Semester Average Values -->
            <!-- <div class="row mb-4">
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
                </div> -->

            <x-footers.auth></x-footers.auth>
        </div>
    </main>
    <x-plugins></x-plugins>
    @push('js')
    <script src="{{ asset('assets') }}/js/plugins/chartjs.min.js"></script>
    <script>
            document.addEventListener('DOMContentLoaded', function () {
                // Pie Chart - Graduation Distribution (Testing)
                var ctx1 = document.getElementById("graduation-distribution-chart-testing").getContext("2d");
                new Chart(ctx1, {
                    type: "pie",
                    data: {
                        labels: ["lulus", "lulus bersyarat", "tidak lulus"],
                        datasets: [{
                            data: [
                                {{ $testingStats['lulus'] }},
                                {{ $testingStats['lulus_bersyarat'] }},
                                {{ $testingStats['tidak_lulus'] }}
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
                // Line Chart - Semester Trend (Testing)
                var ctx2 = document.getElementById("semester-trend-chart-testing").getContext("2d");
                new Chart(ctx2, {
                    type: "line",
                    data: {
                        labels: {!! json_encode($semesterTrendData['labels']) !!},
                        datasets: [
                            {
                                label: 'Lulus',
                                data: {!! json_encode($semesterTrendData['datasets']['lulus']) !!},
                                borderColor: 'rgba(75, 192, 192, 1)',
                                backgroundColor: 'rgba(75, 192, 192, 0.2)',
                                fill: false,
                                tension: 0.4
                            },
                            {
                                label: 'Lulus Bersyarat',
                                data: {!! json_encode($semesterTrendData['datasets']['lulus bersyarat']) !!},
                                borderColor: 'rgba(255, 205, 86, 1)',
                                backgroundColor: 'rgba(255, 205, 86, 0.2)',
                                fill: false,
                                tension: 0.4
                            },
                            {
                                label: 'Tidak Lulus',
                                data: {!! json_encode($semesterTrendData['datasets']['tidak lulus']) !!},
                                borderColor: 'rgba(255, 99, 132, 1)',
                                backgroundColor: 'rgba(255, 99, 132, 0.2)',
                                fill: false,
                                tension: 0.4
                            }
                        ]
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
                // Pie Chart - Graduation Distribution (Training)
                var ctx3 = document.getElementById("graduation-distribution-chart-training").getContext("2d");
                new Chart(ctx3, {
                    type: "pie",
                    data: {
                        labels: ["lulus", "lulus bersyarat", "tidak lulus"],
                        datasets: [{
                            data: [
                                {{ $trainingStats['lulus'] }},
                                {{ $trainingStats['lulus_bersyarat'] }},
                                {{ $trainingStats['tidak_lulus'] }}
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
                // Line Chart - Semester Trend (Training)
                var ctx4 = document.getElementById("semester-trend-chart-training").getContext("2d");
                new Chart(ctx4, {
                    type: "line",
                    data: {
                        labels: {!! json_encode($semesterTrendData['labels']) !!},
                        datasets: [
                            {
                                label: 'Lulus',
                                data: {!! json_encode($semesterTrendData['datasets']['lulus']) !!},
                                borderColor: 'rgba(75, 192, 192, 1)',
                                backgroundColor: 'rgba(75, 192, 192, 0.2)',
                                fill: false,
                                tension: 0.4
                            },
                            {
                                label: 'Lulus Bersyarat',
                                data: {!! json_encode($semesterTrendData['datasets']['lulus bersyarat']) !!},
                                borderColor: 'rgba(255, 205, 86, 1)',
                                backgroundColor: 'rgba(255, 205, 86, 0.2)',
                                fill: false,
                                tension: 0.4
                            },
                            {
                                label: 'Tidak Lulus',
                                data: {!! json_encode($semesterTrendData['datasets']['tidak lulus']) !!},
                                borderColor: 'rgba(255, 99, 132, 1)',
                                backgroundColor: 'rgba(255, 99, 132, 0.2)',
                                fill: false,
                                tension: 0.4
                            }
                        ]
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
            });
    </script>
    @endpush
</x-layout>