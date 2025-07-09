<x-layout bodyClass="g-sidenav-show bg-gray-200">

    <x-navbars.sidebar activePage="export"></x-navbars.sidebar>
    <main class="main-content position-relative max-height-vh-100 h-100 border-radius-lg ">
        <!-- Navbar -->
        <x-navbars.navs.auth titlePage="Export Data"></x-navbars.navs.auth>
        <!-- End Navbar -->
        <div class="container-fluid py-4">
            <div class="row mb-4">
                <div class="col-12">
                    <div class="card">
                        <div class="card-header bg-gradient-primary p-3">
                            <h5 class="text-white mb-0">Format Output</h5>
                        </div>
                        <div class="card-body p-3">
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="card border mb-3 format-card" data-format="pdf">
                                        <div class="card-body p-3 text-center">
                                            <div class="mb-3">
                                                <i class="material-icons text-primary" style="font-size: 2.5rem;">picture_as_pdf</i>
                                            </div>
                                            <h6 class="mb-1">PDF</h6>
                                            <div class="form-check d-flex justify-content-center mt-2">
                                                <input class="form-check-input me-2" type="radio" name="fileFormat" id="formatPDF" value="pdf" checked>
                                                <label class="form-check-label" for="formatPDF">Pilih Format</label>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="card border mb-3 format-card" data-format="csv">
                                        <div class="card-body p-3 text-center">
                                            <div class="mb-3">
                                                <i class="material-icons text-info" style="font-size: 2.5rem;">data_object</i>
                                            </div>
                                            <h6 class="mb-1">CSV</h6>
                                            <div class="form-check d-flex justify-content-center mt-2">
                                                <input class="form-check-input me-2" type="radio" name="fileFormat" id="formatCSV" value="csv">
                                                <label class="form-check-label" for="formatCSV">Pilih Format</label>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row mb-4">
                <div class="col-12">
                    <div class="card">
                        <div class="card-header bg-gradient-primary p-3">
                            <h5 class="text-white mb-0">Konfigurasi Data</h5>
                        </div>
                        <div class="card-body p-3">
                            <form id="export-form">
                                <div class="row">
                                    <!-- Data Type Selection -->
                                    <div class="col-md-6">
                                        <div class="form-group mb-4">
                                            <h6 class="fw-bold border-bottom pb-2 mb-3">Jenis Data</h6>
                                            <div class="mb-2">
                                                <div class="form-check mb-2">
                                                    <input class="form-check-input" type="radio" name="dataType" id="allData" value="all" checked>
                                                    <label class="form-check-label" for="allData">
                                                        Semua Data Siswa
                                                    </label>
                                                </div>
                                                <div class="form-check mb-2">
                                                    <input class="form-check-input" type="radio" name="dataType" id="passedData" value="passed">
                                                    <label class="form-check-label" for="passedData">
                                                        Hanya Data Siswa Lulus
                                                    </label>
                                                </div>
                                                <div class="form-check mb-2">
                                                    <input class="form-check-input" type="radio" name="dataType" id="failedData" value="failed">
                                                    <label class="form-check-label" for="failedData">
                                                        Hanya Data Siswa Tidak Lulus
                                                    </label>
                                                </div>
                                                <div class="form-check">
                                                    <input class="form-check-input" type="radio" name="dataType" id="predictionData" value="prediction">
                                                    <label class="form-check-label" for="predictionData">
                                                        Data Hasil Prediksi Terbaru
                                                    </label>
                                                </div>
                                                <div class="form-check mb-2">
                                                    <input class="form-check-input" type="radio" name="dataType" id="conditionalPassedData" value="conditional_passed">
                                                    <label class="form-check-label" for="conditionalPassedData">
                                                        Hanya Data Siswa Lulus Bersyarat
                                                    </label>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <!-- Column Selection -->
                                    <div class="col-md-6">
                                        <div class="form-group mb-4">
                                            <h6 class="fw-bold border-bottom pb-2 mb-3">Kolom yang Disertakan</h6>
                                            <div class="row">
                                                <div class="col-md-6">
                                                    <div class="form-check mb-2">
                                                        <input class="form-check-input" type="checkbox" id="includeNISN" checked>
                                                        <label class="form-check-label" for="includeNISN">
                                                            NISN
                                                        </label>
                                                    </div>
                                                    <div class="form-check mb-2">
                                                        <input class="form-check-input" type="checkbox" id="includeName" checked>
                                                        <label class="form-check-label" for="includeName">
                                                            Nama Siswa
                                                        </label>
                                                    </div>
                                                </div>
                                                <div class="col-md-6">
                                                    <div class="form-check mb-2">
                                                        <input class="form-check-input" type="checkbox" id="includeGrades" checked>
                                                        <label class="form-check-label" for="includeGrades">
                                                            Nilai Akademik
                                                        </label>
                                                    </div>
                                                    <div class="form-check mb-2">
                                                        <input class="form-check-input" type="checkbox" id="includeNonAcademic" checked>
                                                        <label class="form-check-label" for="includeNonAcademic">
                                                            Data Non-Akademik
                                                        </label>
                                                    </div>
                                                    <div class="form-check">
                                                        <input class="form-check-input" type="checkbox" id="includeStatus" checked>
                                                        <label class="form-check-label" for="includeStatus">
                                                            Status Kelulusan
                                                        </label>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Report Settings -->
                                <div class="row mt-2">
                                    <div class="col-md-4">
                                        <div class="form-group mb-3">
                                            <label for="judul" class="form-label">Judul Laporan</label>
                                            <input type="text" id="judul" class="form-control" placeholder="Masukkan judul laporan">
                                        </div>
                                    </div>
                                    <input type="hidden" id="tahunAngkatan" name="tahunAngkatan" value="">
                                </div>

                                <!-- Generate Button -->
                                <div class="text-end mt-3">
                                    <button type="button" id="generate-btn" class="btn btn-primary">
                                        <i class="material-icons me-2">download</i> Generate Laporan
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
            <x-footers.auth></x-footers.auth>
        </div>
    </main>
    <x-plugins></x-plugins>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const formatCards = document.querySelectorAll('.format-card');
            
            // Handle format card selection
            formatCards.forEach(card => {
                card.addEventListener('click', function() {
                    const format = this.getAttribute('data-format');
                    const radioInput = document.getElementById('format' + format.charAt(0).toUpperCase() + format.slice(1));
                    
                    // Uncheck all
                    formatCards.forEach(c => {
                        c.classList.remove('border-primary');
                    });
                    
                    // Check selected
                    this.classList.add('border-primary');
                    radioInput.checked = true;
                });
            });

            // Set default active format card
            const defaultFormat = document.querySelector('input[name="fileFormat"]:checked');
            if (defaultFormat) {
                const defaultCard = document.querySelector(`.format-card[data-format="${defaultFormat.value}"]`);
                defaultCard.classList.add('border-primary');
            }
            
            // Generate button click event
            document.getElementById('generate-btn').addEventListener('click', function() {
                const form = document.getElementById('export-form');
                const formData = new FormData();
                
                // Get selected format
                const format = document.querySelector('input[name="fileFormat"]:checked').value;
                formData.append('fileFormat', format);
                
                // Get data type
                const dataType = document.querySelector('input[name="dataType"]:checked').value;
                formData.append('dataType', dataType);
                
                // Get included columns
                const columns = [];
                if (document.getElementById('includeNISN').checked) columns.push('nisn');
                if (document.getElementById('includeName').checked) columns.push('name');
                if (document.getElementById('includeGrades').checked) {
                    columns.push('semester_1','semester_2','semester_3','semester_4','semester_5','semester_6','usp');
                }
                if (document.getElementById('includeNonAcademic').checked) {
                    columns.push('sikap','kerapian','kerajinan');
                }
                if (document.getElementById('includeStatus').checked) columns.push('status');
                columns.forEach(col => formData.append('columns[]', col));
                
                // Get title and school year
                formData.append('title', document.getElementById('judul').value);
                formData.append('tahunAngkatan', document.getElementById('tahunAngkatan').value);
                formData.append('tahunAngkatan', document.getElementById('tahunAngkatan').value || '');
                
                // Show loading state
                const button = this;
                const originalText = button.innerHTML;
                button.disabled = true;
                button.innerHTML = '<i class="material-icons me-2">hourglass_empty</i> Generating...';
                
                // Submit form
                fetch('{{ route("export.process") }}', {
                    method: 'POST',
                    body: formData,
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    }
                })
                .then(response => {
                    if (!response.ok) {
                        // Cek jika error 422 (data kosong atau kolom tidak dipilih)
                        return response.json().then(err => { throw err; });
                    }
                    return response.blob();
                })
                .then(blob => {
                    // Jika blob kosong (karena error), jangan lanjutkan
                    if (blob && blob.type && blob.type.indexOf('application/json') !== -1) {
                        return;
                    }
                    // Create download link
                    const url = window.URL.createObjectURL(blob);
                    const a = document.createElement('a');
                    a.href = url;
                    a.download = `Laporan_${new Date().toISOString().split('T')[0]}.${format}`;
                    document.body.appendChild(a);
                    a.click();
                    window.URL.revokeObjectURL(url);
                    a.remove();
                })
                .catch(error => {
                    let msg = 'Terjadi kesalahan saat mengekspor data. Silakan coba lagi.';
                    if (error && error.error) {
                        msg = error.error;
                    }
                    alert(msg);
                })
                .finally(() => {
                    // Reset button state
                    button.disabled = false;
                    button.innerHTML = originalText;
                });
            });
        });
    </script>

</x-layout> 