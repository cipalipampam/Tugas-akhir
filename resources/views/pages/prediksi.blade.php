<x-layout bodyClass="g-sidenav-show bg-gray-200">
    <x-navbars.sidebar activePage="prediksi"></x-navbars.sidebar>

    <main class="main-content position-relative max-height-vh-100 h-100 border-radius-lg">
        <x-navbars.navs.auth titlePage="Prediksi"></x-navbars.navs.auth>

        <div class="container-fluid py-4">
            <div class="card mb-3">
                <div class="card-header pb-0 px-4">
                    <h6 class="mb-0">Form Prediksi Kelulusan</h6>
                </div>

                <div class="card-body pt-4 px-4">
                    <div class="row">
                        <!-- Form Input -->
                        <div class="col-md-12">
                            <form method="POST" action="{{ route('prediction.process') }}">

                                @csrf
                                <div class="row">
                                    <!-- Loop semester values -->
                                    @for ($i = 1; $i <= 6; $i++)
                                        <div class="col-md-2">
                                            <div class="form-group">
                                                <label for="semester_{{ $i }}">Semester {{ $i }}</label>
                                                <input type="number" step="0.01" name="data[Rata-Rata Semester {{ $i }}]"
                                                    class="form-control" required>
                                            </div>
                                        </div>
                                    @endfor

                                    <!-- USP input -->
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label for="usp">Nilai USP</label>
                                            <input type="number" step="0.01" name="data[usp]" class="form-control"
                                                required>
                                        </div>
                                    </div>

                                    <!-- Non-academic inputs -->
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label for="sikap">Sikap</label>
                                            <select name="data[sikap]" class="form-control" required>
                                                <option value="">Pilih</option>
                                                <option value="baik">Baik</option>
                                                <option value="cukup baik">Cukup Baik</option>
                                                <option value="kurang baik">Kurang Baik</option>
                                            </select>
                                        </div>
                                    </div>

                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label for="kerapian">Kerapian</label>
                                            <select name="data[kerapian]" class="form-control" required>
                                                <option value="">Pilih</option>
                                                <option value="baik">Baik</option>
                                                <option value="cukup baik">Cukup Baik</option>
                                                <option value="kurang baik">Kurang Baik</option>
                                            </select>
                                        </div>
                                    </div>

                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label for="kerajinan">Kerajinan</label>
                                            <select name="data[kerajinan]" class="form-control" required>
                                                <option value="">Pilih</option>
                                                <option value="baik">Baik</option>
                                                <option value="cukup baik">Cukup Baik</option>
                                                <option value="kurang baik">Kurang Baik</option>
                                            </select>
                                        </div>
                                    </div>
                                </div>

                                <!-- Input untuk Nilai K -->
                                <div class="row">
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label for="k_value">Nilai K</label>
                                            <input type="number" name="k_value" class="form-control" value="5" required>
                                        </div>
                                    </div>
                                </div>

                                <div class="text-center mt-4">
                                    <button type="submit" class="btn btn-primary btn-lg">Prediksi Kelulusan</button>
                                </div>
                            </form>

                            <!-- Display prediction results -->
                            @if(isset($results) && count($results) > 0)
                                <div class="table-responsive mt-4">
                                    <table class="table table-striped table-hover">
                                        <thead>
                                            <tr>
                                                <th>NISN</th>
                                                <th>Nama</th>
                                                <th>Status Prediksi</th>
                                                <th>Jarak</th>
                                                <th>Bobot</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($results as $result)
                                                <tr>
                                                    <td>{{ $result['nisn'] }}</td>
                                                    <td>{{ $result['nama'] }}</td>
                                                    <td>{{ $result['predicted_status'] }}</td>
                                                    <td>{{ number_format($result['distance'], 3) }}</td>
                                                    <td>{{ number_format($result['weight'], 3) }}</td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            @else
                                <div class="alert alert-info mt-4 text-center">
                                    Belum ada hasil prediksi. Silakan masukkan data dan tekan tombol <strong>Prediksi
                                        Kelulusan</strong>.
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </main>
</x-layout>
