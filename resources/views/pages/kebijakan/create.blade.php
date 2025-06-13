<x-layout bodyClass="g-sidenav-show bg-gray-200">
    <x-navbars.sidebar activePage="kebijakan.create"></x-navbars.sidebar>
    <main class="main-content position-relative max-height-vh-100 h-100 border-radius-lg">
        <x-navbars.navs.auth titlePage="Tambah Aturan Kelulusan"></x-navbars.navs.auth>

        <div class="container-fluid py-4">
            @if($errors->any())
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <ul class="mb-0">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

            <div class="row">
                <div class="col-12">
                    <div class="card mb-4">
                        <div class="card-header p-3">
                            <h6 class="mb-0"><i class="fas fa-graduation-cap me-2"></i>Form Aturan Kelulusan</h6>
                            <p class="text-sm text-secondary mb-0">Tambahkan aturan kelulusan dengan mengisi form di bawah ini.</p>
                        </div>
                        <div class="card-body p-3">
                            <form action="{{ route('kebijakan.store') }}" method="POST" id="ruleForm">
                                @csrf
                                <div class="row">
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label class="form-label">Jenis Nilai</label>
                                            <select class="form-select @error('attribute') is-invalid @enderror" name="attribute" id="attribute" required>
                                                <option value="">Pilih Jenis Nilai</option>
                                                <option value="rata_rata" {{ old('attribute') == 'rata_rata' ? 'selected' : '' }}>Rata-rata Nilai</option>
                                                <option value="usp" {{ old('attribute') == 'usp' ? 'selected' : '' }}>Nilai USP</option>
                                                <option value="sikap" {{ old('attribute') == 'sikap' ? 'selected' : '' }}>Sikap</option>
                                                <option value="kerajinan" {{ old('attribute') == 'kerajinan' ? 'selected' : '' }}>Kerajinan</option>
                                                <option value="kerapian" {{ old('attribute') == 'kerapian' ? 'selected' : '' }}>Kerapian</option>
                                            </select>
                                            @error('attribute')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                            <small class="form-text text-muted">Pilih jenis nilai yang akan digunakan</small>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label class="form-label">Kondisi</label>
                                            <select class="form-select @error('operator') is-invalid @enderror" name="operator" required>
                                                <option value="">Pilih Kondisi</option>
                                                <option value=">" {{ old('operator') == '>' ? 'selected' : '' }}>Lebih dari</option>
                                                <option value="<" {{ old('operator') == '<' ? 'selected' : '' }}>Kurang dari</option>
                                                <option value="=" {{ old('operator') == '=' ? 'selected' : '' }}>Sama dengan</option>
                                                <option value=">=" {{ old('operator') == '>=' ? 'selected' : '' }}>Lebih dari atau sama dengan</option>
                                                <option value="<=" {{ old('operator') == '<=' ? 'selected' : '' }}>Kurang dari atau sama dengan</option>
                                            </select>
                                            @error('operator')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                            <small class="form-text text-muted">Pilih kondisi perbandingan</small>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label class="form-label">Nilai Patokan</label>
                                            <input type="number" class="form-control @error('value') is-invalid @enderror" 
                                                name="value" id="value" step="0.01" value="{{ old('value') }}" required>
                                            @error('value')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                            <small class="form-text text-muted" id="valueHelp">Masukkan nilai patokan</small>
                                        </div>
                                    </div>
                                </div>

                                <div class="row mt-3">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label class="form-label">Hasil Jika Syarat Terpenuhi</label>
                                            <select class="form-select @error('category') is-invalid @enderror" name="category" required>
                                                <option value="">Pilih Hasil</option>
                                                <option value="lulus" {{ old('category') == 'lulus' ? 'selected' : '' }}>Lulus</option>
                                                <option value="lulus bersyarat" {{ old('category') == 'lulus bersyarat' ? 'selected' : '' }}>Lulus Bersyarat</option>
                                                <option value="tidak lulus" {{ old('category') == 'tidak lulus' ? 'selected' : '' }}>Tidak Lulus</option>
                                            </select>
                                            @error('category')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                            <small class="form-text text-muted">Pilih hasil prediksi</small>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label class="form-label">Urutan Aturan</label>
                                            <input type="number" class="form-control @error('priority') is-invalid @enderror" 
                                                name="priority" min="1" value="{{ old('priority') }}" required>
                                            @error('priority')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                            <small class="form-text text-muted">1 = paling penting</small>
                                        </div>
                                    </div>
                                </div>

                                <div class="row mt-4">
                                    <div class="col-12">
                                        <button type="submit" class="btn btn-primary">
                                            <i class="fas fa-save me-2"></i>Simpan Aturan
                                        </button>
                                        <a href="{{ route('kebijakan.index') }}" class="btn btn-secondary">
                                            <i class="fas fa-arrow-left me-2"></i>Kembali
                                        </a>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </main>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const attributeSelect = document.getElementById('attribute');
            const valueInput = document.getElementById('value');
            const valueHelp = document.getElementById('valueHelp');
            const form = document.getElementById('ruleForm');

            function updateValueConstraints() {
                const selectedAttribute = attributeSelect.value;
                
                if (selectedAttribute === 'sikap' || selectedAttribute === 'kerajinan' || selectedAttribute === 'kerapian') {
                    valueInput.min = '0';
                    valueInput.max = '1';
                    valueInput.step = '0.1';
                    valueHelp.textContent = 'Masukkan nilai antara 0 dan 1';
                } else if (selectedAttribute === 'rata_rata' || selectedAttribute === 'usp') {
                    valueInput.min = '0';
                    valueInput.max = '100';
                    valueInput.step = '0.01';
                    valueHelp.textContent = 'Masukkan nilai antara 0 dan 100';
                } else {
                    valueInput.removeAttribute('min');
                    valueInput.removeAttribute('max');
                    valueInput.step = '0.01';
                    valueHelp.textContent = 'Masukkan nilai patokan';
                }
            }

            // Update constraints when attribute changes
            attributeSelect.addEventListener('change', updateValueConstraints);

            // Initial update
            updateValueConstraints();

            // Form submission validation
            form.addEventListener('submit', function(e) {
                const selectedAttribute = attributeSelect.value;
                const value = parseFloat(valueInput.value);

                if (selectedAttribute === 'sikap' || selectedAttribute === 'kerajinan' || selectedAttribute === 'kerapian') {
                    if (value < 0 || value > 1) {
                        e.preventDefault();
                        alert('Nilai harus antara 0 dan 1');
                        valueInput.focus();
                    }
                } else if (selectedAttribute === 'rata_rata' || selectedAttribute === 'usp') {
                    if (value < 0 || value > 100) {
                        e.preventDefault();
                        alert('Nilai harus antara 0 dan 100');
                        valueInput.focus();
                    }
                }
            });
        });
    </script>
</x-layout> 