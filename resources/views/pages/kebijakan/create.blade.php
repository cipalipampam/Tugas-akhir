<x-layout bodyClass="g-sidenav-show bg-gray-200">
    <x-navbars.sidebar activePage="kebijakan.index"></x-navbars.sidebar>
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
                            <p class="text-sm text-secondary mb-0">Tambahkan aturan kelulusan dengan mengisi form di
                                bawah ini.</p>
                        </div>
                        <div class="card-body p-3">
                            <form action="{{ route('kebijakan.store') }}" method="POST" id="ruleForm">
                                @csrf
                                <div id="rules-container">
                                    <div class="rule-entry mb-4 p-3 border rounded">
                                        <div class="row">
                                            <div class="col-md-4">
                                                <div class="form-group">
                                                    <label class="form-label">Jenis Nilai</label>
                                                    <select class="form-select @error('rules.0.attribute') is-invalid @enderror"
                                                        name="rules[0][attribute]" required>
                                                        <option value="">Pilih Jenis Nilai</option>
                                                        <option value="rata_rata">Rata-rata Nilai</option>
                                                        <option value="usp">Nilai USP</option>
                                                        <option value="sikap">Sikap</option>
                                                        <option value="kerajinan">Kerajinan</option>
                                                        <option value="kerapian">Kerapian</option>
                                                    </select>
                                                    <small class="form-text text-muted">Pilih jenis nilai yang akan digunakan</small>
                                                </div>
                                            </div>
                                            <div class="col-md-4">
                                                <div class="form-group">
                                                    <label class="form-label">Kondisi</label>
                                                    <select class="form-select @error('rules.0.operator') is-invalid @enderror"
                                                        name="rules[0][operator]" required>
                                                        <option value="">Pilih Kondisi</option>
                                                        <option value=">">Lebih dari</option>
                                                        <option value="<">Kurang dari</option>
                                                        <option value="=">Sama dengan</option>
                                                        <option value=">=">Lebih dari atau sama dengan</option>
                                                        <option value="<=">Kurang dari atau sama dengan</option>
                                                    </select>
                                                    <small class="form-text text-muted">Pilih kondisi perbandingan</small>
                                                </div>
                                            </div>
                                            <div class="col-md-4">
                                                <div class="form-group">
                                                    <label class="form-label">Nilai Patokan</label>
                                                    <input type="number"
                                                        class="form-control @error('rules.0.value') is-invalid @enderror"
                                                        name="rules[0][value]" step="0.01" required>
                                                    <small class="form-text text-muted">Masukkan nilai patokan</small>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="row mt-3">
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label class="form-label">Hasil Jika Syarat Terpenuhi</label>
                                                    <select class="form-select @error('rules.0.category') is-invalid @enderror"
                                                        name="rules[0][category]" required>
                                                        <option value="">Pilih Hasil</option>
                                                        <option value="lulus">Lulus</option>
                                                        <option value="lulus bersyarat">Lulus Bersyarat</option>
                                                        <option value="tidak lulus">Tidak Lulus</option>
                                                    </select>
                                                    <small class="form-text text-muted">Pilih hasil prediksi</small>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label class="form-label">Urutan Aturan</label>
                                                    <input type="number"
                                                        class="form-control @error('rules.0.priority') is-invalid @enderror"
                                                        name="rules[0][priority]" min="1" required>
                                                    <small class="form-text text-muted">1 = paling penting</small>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="row mt-3">
                                    <div class="col-12">
                                        <button type="button" class="btn btn-info" id="add-rule">
                                            <i class="fas fa-plus me-2"></i>Tambah Aturan
                                        </button>
                                        <button type="submit" class="btn btn-primary">
                                            <i class="fas fa-save me-2"></i>Simpan Semua Aturan
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
        document.addEventListener('DOMContentLoaded', function () {
            const rulesContainer = document.getElementById('rules-container');
            const addRuleButton = document.getElementById('add-rule');
            let ruleCount = 1;

            function updateValueConstraints(ruleEntry) {
                const attributeSelect = ruleEntry.querySelector('select[name^="rules"][name$="[attribute]"]');
                const valueInput = ruleEntry.querySelector('input[name^="rules"][name$="[value]"]');
                const valueHelp = valueInput.nextElementSibling;

                function updateConstraints() {
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

                attributeSelect.addEventListener('change', updateConstraints);
                updateConstraints();
            }

            function addRule() {
                const template = rulesContainer.children[0].cloneNode(true);
                const newIndex = ruleCount++;

                // Update all input names with new index
                template.querySelectorAll('[name]').forEach(input => {
                    input.name = input.name.replace(/rules\[\d+\]/, `rules[${newIndex}]`);
                    input.value = '';
                });

                // Add remove button if not the first rule
                if (newIndex > 0) {
                    const removeButton = document.createElement('button');
                    removeButton.type = 'button';
                    removeButton.className = 'btn btn-danger btn-sm position-absolute top-0 end-0 m-2';
                    removeButton.innerHTML = '<i class="fas fa-times"></i>';
                    removeButton.onclick = function() {
                        template.remove();
                    };
                    template.style.position = 'relative';
                    template.appendChild(removeButton);
                }

                rulesContainer.appendChild(template);
                updateValueConstraints(template);
            }

            addRuleButton.addEventListener('click', addRule);

            // Initialize constraints for the first rule
            updateValueConstraints(rulesContainer.children[0]);

            // Form submission validation
            document.getElementById('ruleForm').addEventListener('submit', function(e) {
                const ruleEntries = rulesContainer.children;
                let isValid = true;

                for (let entry of ruleEntries) {
                    const attributeSelect = entry.querySelector('select[name^="rules"][name$="[attribute]"]');
                    const valueInput = entry.querySelector('input[name^="rules"][name$="[value]"]');
                    const selectedAttribute = attributeSelect.value;
                    const value = parseFloat(valueInput.value);

                    if (selectedAttribute === 'sikap' || selectedAttribute === 'kerajinan' || selectedAttribute === 'kerapian') {
                        if (value < 0 || value > 1) {
                            e.preventDefault();
                            alert(`Nilai untuk ${selectedAttribute} harus antara 0 dan 1`);
                            valueInput.focus();
                            isValid = false;
                            break;
                        }
                    } else if (selectedAttribute === 'rata_rata' || selectedAttribute === 'usp') {
                        if (value < 0 || value > 100) {
                            e.preventDefault();
                            alert(`Nilai untuk ${selectedAttribute} harus antara 0 dan 100`);
                            valueInput.focus();
                            isValid = false;
                            break;
                        }
                    }
                }

                return isValid;
            });
        });
    </script>
</x-layout>