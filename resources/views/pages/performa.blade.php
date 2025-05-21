<x-layout bodyClass="g-sidenav-show bg-gray-200">
    <x-navbars.sidebar activePage="performa"></x-navbars.sidebar>
    <main class="main-content position-relative max-height-vh-100 h-100 border-radius-lg">
        <x-navbars.navs.auth titlePage="Performa"></x-navbars.navs.auth>

        <div class="container-fluid py-4">
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
                            <h6 class="mb-0"><i class="fas fa-chart-line me-2"></i>Evaluasi Performa</h6>
                            <span class="badge bg-gradient-primary">Fuzzy K-NN Algorithm</span>
                        </div>

                        <div class="card-body p-3">
                            <!-- Form Pengaturan Evaluasi -->
                            <div class="card border shadow-sm mb-4">
                                <div class="card-header bg-light p-3">
                                    <h6 class="mb-0 text-primary">Pengaturan Evaluasi</h6>
                                </div>
                                <div class="card-body">
                                    <form action="{{ route('performa.evaluate') }}" method="POST">
                                        @csrf
                                        <div class="row">
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label for="training_percentage">Persentase Data Latih</label>
                                                    <select class="form-control" id="training_percentage" name="training_percentage" required>
                                                        @for($i = 10; $i <= 90; $i += 10)
                                                            <option value="{{ $i }}">{{ $i }}%</option>
                                                        @endfor
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="alert alert-info mb-0">
                                                    <i class="fas fa-info-circle me-2"></i>
                                                    Nilai K (Jumlah Tetangga) telah ditetapkan: <strong>5</strong>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row mt-3">
                                            <div class="col-12">
                                                <button type="submit" class="btn btn-primary">
                                                    <i class="fas fa-play me-2"></i>Evaluasi Sekarang
                                                </button>
                                            </div>
                                        </div>
                                    </form>
                                </div>
                            </div>

                            <!-- Hasil Evaluasi Terbaru -->
                            @if(isset($latestEvaluation))
                            <div class="card border shadow-sm mb-4">
                                <div class="card-header bg-light p-3">
                                    <h6 class="mb-0 text-primary">Hasil Evaluasi Terbaru</h6>
                                </div>
                                <div class="card-body">
                                    <!-- Data Distribution -->
                                    <div class="row mb-4">
                                        <div class="col-md-6">
                                            <div class="card bg-gradient-info">
                                                <div class="card-body">
                                                    <h6 class="text-white">Data Latih</h6>
                                                    <h2 class="text-white">{{ $latestEvaluation->training_data_count }} Data</h2>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="card bg-gradient-warning">
                                                <div class="card-body">
                                                    <h6 class="text-white">Data Uji</h6>
                                                    <h2 class="text-white">{{ $latestEvaluation->test_data_count }} Data</h2>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Performance Metrics -->
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="card bg-gradient-success">
                                                <div class="card-body">
                                                    <h6 class="text-white">Akurasi</h6>
                                                    <h2 class="text-white">{{ number_format($latestEvaluation->accuracy, 2) }}%</h2>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="card bg-gradient-danger">
                                                <div class="card-body">
                                                    <h6 class="text-white">Error Rate</h6>
                                                    <h2 class="text-white">{{ number_format($latestEvaluation->error_rate, 2) }}%</h2>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Additional Metrics -->
                                    <div class="row mt-4">
                                        <div class="col-md-4">
                                            <div class="card bg-gradient-primary">
                                                <div class="card-body">
                                                    <h6 class="text-white">Precision</h6>
                                                    <h2 class="text-white">{{ number_format($latestEvaluation->precision, 2) }}%</h2>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="card bg-gradient-info">
                                                <div class="card-body">
                                                    <h6 class="text-white">Recall</h6>
                                                    <h2 class="text-white">{{ number_format($latestEvaluation->recall, 2) }}%</h2>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="card bg-gradient-success">
                                                <div class="card-body">
                                                    <h6 class="text-white">F1 Score</h6>
                                                    <h2 class="text-white">{{ number_format($latestEvaluation->f1_score, 2) }}%</h2>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Confusion Matrix -->
                                    <div class="card border shadow-sm mt-4">
                                        <div class="card-header bg-light p-3">
                                            <h6 class="mb-0 text-primary">Confusion Matrix</h6>
                                        </div>
                                        <div class="card-body">
                                            <div class="table-responsive">
                                                <table class="table align-items-center mb-0">
                                                    <thead>
                                                        <tr>
                                                            <th></th>
                                                            <th class="text-center">Prediksi Lulus</th>
                                                            <th class="text-center">Prediksi Lulus Bersyarat</th>
                                                            <th class="text-center">Prediksi Tidak Lulus</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        <tr>
                                                            <td><strong>Aktual Lulus</strong></td>
                                                            <td class="text-center">{{ $latestEvaluation->confusion_matrix['lulus']['lulus'] }}</td>
                                                            <td class="text-center">{{ $latestEvaluation->confusion_matrix['lulus']['lulus_bersyarat'] }}</td>
                                                            <td class="text-center">{{ $latestEvaluation->confusion_matrix['lulus']['tidak_lulus'] }}</td>
                                                        </tr>
                                                        <tr>
                                                            <td><strong>Aktual Lulus Bersyarat</strong></td>
                                                            <td class="text-center">{{ $latestEvaluation->confusion_matrix['lulus_bersyarat']['lulus'] }}</td>
                                                            <td class="text-center">{{ $latestEvaluation->confusion_matrix['lulus_bersyarat']['lulus_bersyarat'] }}</td>
                                                            <td class="text-center">{{ $latestEvaluation->confusion_matrix['lulus_bersyarat']['tidak_lulus'] }}</td>
                                                        </tr>
                                                        <tr>
                                                            <td><strong>Aktual Tidak Lulus</strong></td>
                                                            <td class="text-center">{{ $latestEvaluation->confusion_matrix['tidak_lulus']['lulus'] }}</td>
                                                            <td class="text-center">{{ $latestEvaluation->confusion_matrix['tidak_lulus']['lulus_bersyarat'] }}</td>
                                                            <td class="text-center">{{ $latestEvaluation->confusion_matrix['tidak_lulus']['tidak_lulus'] }}</td>
                                                        </tr>
                                                    </tbody>
                                                </table>
                                            </div>
                                            <div class="mt-3">
                                                <p class="text-sm text-muted mb-0">
                                                    <i class="fas fa-info-circle me-1"></i>
                                                    Matrix menunjukkan jumlah prediksi untuk setiap kombinasi status aktual dan prediksi.
                                                </p>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Metric Explanations -->
                                    <div class="card border shadow-sm mt-4">
                                        <div class="card-header bg-light p-3">
                                            <h6 class="mb-0 text-primary">Penjelasan Metrik</h6>
                                        </div>
                                        <div class="card-body">
                                            <div class="row">
                                                <div class="col-md-3">
                                                    <h6 class="text-primary">Akurasi</h6>
                                                    <p class="text-sm">Proporsi prediksi yang benar dari total prediksi.</p>
                                                </div>
                                                <div class="col-md-3">
                                                    <h6 class="text-primary">Precision (Macro)</h6>
                                                    <p class="text-sm">Rata-rata precision untuk setiap kelas. Mengukur seberapa akurat prediksi positif untuk setiap kelas.</p>
                                                </div>
                                                <div class="col-md-3">
                                                    <h6 class="text-primary">Recall (Macro)</h6>
                                                    <p class="text-sm">Rata-rata recall untuk setiap kelas. Mengukur kemampuan model menemukan semua kasus untuk setiap kelas.</p>
                                                </div>
                                                <div class="col-md-3">
                                                    <h6 class="text-primary">F1 Score (Macro)</h6>
                                                    <p class="text-sm">Rata-rata harmonik dari precision dan recall untuk setiap kelas.</p>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            @endif

                            <!-- Riwayat Evaluasi -->
                            <div class="card border shadow-sm">
                                <div class="card-header bg-light p-3">
                                    <h6 class="mb-0 text-primary">Riwayat Evaluasi</h6>
                                </div>
                                <div class="card-body px-0 pt-0 pb-2">
                                    <div class="table-responsive p-0">
                                        <table class="table align-items-center mb-0">
                                            <thead>
                                                <tr>
                                                    <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Tanggal</th>
                                                    <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">Data Latih</th>
                                                    <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Nilai K</th>
                                                    <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Akurasi</th>
                                                    <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Error Rate</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @forelse($evaluationHistory as $evaluation)
                                                <tr>
                                                    <td>
                                                        <p class="text-xs font-weight-bold mb-0 px-3">{{ $evaluation->created_at->format('d/m/Y H:i') }}</p>
                                                    </td>
                                                    <td>
                                                        <p class="text-xs font-weight-bold mb-0">{{ $evaluation->training_percentage }}%</p>
                                                    </td>
                                                    <td class="align-middle text-center text-sm">
                                                        <span class="badge badge-sm bg-gradient-success">{{ $evaluation->k_value }}</span>
                                                    </td>
                                                    <td class="align-middle text-center">
                                                        <span class="text-secondary text-xs font-weight-bold">{{ number_format($evaluation->accuracy, 2) }}%</span>
                                                    </td>
                                                    <td class="align-middle text-center">
                                                        <span class="text-secondary text-xs font-weight-bold">{{ number_format($evaluation->error_rate, 2) }}%</span>
                                                    </td>
                                                </tr>
                                                @empty
                                                <tr>
                                                    <td colspan="5" class="text-center">Belum ada data evaluasi</td>
                                                </tr>
                                                @endforelse
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </main>
    <x-plugins></x-plugins>

    <!-- Edit Profile Modal -->
    <div class="modal fade" id="editProfileModal" tabindex="-1" role="dialog" aria-labelledby="editProfileModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="editProfileModalLabel">Edit Profil</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form action="{{ route('user-profile.update') }}" method="POST">
                    @csrf
                    @method('PUT')
                    <div class="modal-body">
                        <div class="form-group">
                            <label for="name">Nama Lengkap</label>
                            <input type="text" class="form-control" id="name" name="name" value="{{ auth()->user()->name }}" required>
                        </div>
                        <div class="form-group">
                            <label for="email">Email</label>
                            <input type="email" class="form-control" id="email" name="email" value="{{ auth()->user()->email }}" required>
                        </div>
                        <div class="form-group">
                            <label for="phone">No. Telepon</label>
                            <input type="text" class="form-control" id="phone" name="phone" value="{{ auth()->user()->phone }}">
                        </div>
                        <div class="form-group">
                            <label for="location">Lokasi</label>
                            <input type="text" class="form-control" id="location" name="location" value="{{ auth()->user()->location }}">
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-layout>
