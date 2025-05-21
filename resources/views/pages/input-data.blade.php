<x-layout bodyClass="g-sidenav-show bg-gray-200">
    <x-navbars.sidebar activePage="input-data"></x-navbars.sidebar>
    <main class="main-content position-relative max-height-vh-100 h-100 border-radius-lg">
        <x-navbars.navs.auth titlePage="Input Data"></x-navbars.navs.auth>
        <div class="container-fluid py-4">
            <div class="row">
                <div class="col-12">
                    <div class="card my-4">
                        <div class="card-header p-0 position-relative mt-n4 mx-3 z-index-2">
                            <div class="bg-gradient-primary shadow-primary border-radius-lg pt-4 pb-3 d-flex justify-content-between align-items-center px-4">
                                <h6 class="text-white text-capitalize ps-3 mb-0">Upload & Preview Data Siswa</h6>
                                <a href="{{ route('download.template') }}" class="btn btn-sm btn-outline-light">
                                    <i class="fas fa-download me-1"></i>
                                    Download Template
                                </a>
                            </div>
                        </div>
                        <div class="card-body px-4 pb-4">
                            <!-- Loading Overlay -->
                            <div id="loading-overlay" class="position-absolute w-100 h-100 d-none" style="background: rgba(255,255,255,0.8); z-index: 1000; top: 0; left: 0;">
                                <div class="d-flex justify-content-center align-items-center h-100">
                                    <div class="spinner-border text-primary" role="status">
                                        <span class="visually-hidden">Loading...</span>
                                    </div>
                                </div>
                            </div>

                            <!-- Alert Container -->
                            <div id="alert-container" class="mb-4"></div>

                            <!-- Upload Section -->
                            <div class="upload-section mb-4">
                                <div class="card bg-gradient-light border-0">
                                    <div class="card-body p-4">
                                        <form id="upload-form" enctype="multipart/form-data">
                                            @csrf
                                            <div class="d-flex align-items-center gap-3">
                                                <div class="flex-grow-1">
                                                    <label for="excel_file" class="form-label text-sm mb-2">Pilih File Excel</label>
                                                    <input type="file" name="excel_file" id="excel_file" class="form-control"
                                                        accept=".xls,.xlsx">
                                                    <div class="form-text text-sm">Format yang didukung: .xls, .xlsx</div>
                                                </div>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>

                            <!-- Preview Section -->
                            <div class="preview-section">
                                <div class="card border-0">
                                    <div class="card-header bg-transparent border-0 p-0 mb-3">
                                        <h6 class="text-uppercase text-dark font-weight-bolder mb-0">Preview Data</h6>
                                    </div>
                                    <div class="table-responsive">
                                        <table id="preview-table" class="table table-bordered text-center align-middle mb-0">
                                            <thead id="table-head" class="bg-light"></thead>
                                            <tbody id="table-body"></tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>

                            <!-- Action Buttons -->
                            <div class="d-flex justify-content-end mt-4 gap-2" id="preview-buttons" style="display: none;">
                                <button id="cancelPreview" class="btn btn-outline-danger">
                                    <i class="fas fa-times me-1"></i>
                                    Cancel Preview
                                </button>
                                <button id="simpanData" class="btn btn-success">
                                    <span class="spinner-border spinner-border-sm d-none" role="status" aria-hidden="true"></span>
                                    <i class="fas fa-save me-1"></i>
                                    <span class="btn-text">Simpan ke Database</span>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <x-footers.auth></x-footers.auth>
        </div>
    </main>
    <x-plugins></x-plugins>

    <script>
        let previewData = []; // Variabel global untuk menyimpan data preview

        // Fungsi untuk menampilkan alert
        function showAlert(message, type = 'success') {
            const alertContainer = document.getElementById('alert-container');
            const alert = document.createElement('div');
            alert.className = `alert alert-${type} alert-dismissible fade show shadow-sm`;
            alert.innerHTML = `
                <div class="d-flex align-items-center">
                    <i class="fas fa-${type === 'success' ? 'check-circle' : 'exclamation-circle'} me-2"></i>
                    ${message}
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            `;
            alertContainer.appendChild(alert);
            
            // Auto dismiss after 5 seconds
            setTimeout(() => {
                alert.remove();
            }, 5000);
        }

        // Fungsi untuk mengatur loading state
        function setLoading(button, isLoading) {
            const spinner = button.querySelector('.spinner-border');
            const text = button.querySelector('.btn-text');
            
            if (isLoading) {
                spinner.classList.remove('d-none');
                text.textContent = 'Loading...';
                button.disabled = true;
            } else {
                spinner.classList.add('d-none');
                text.textContent = button.dataset.originalText || 'Simpan ke Database';
                button.disabled = false;
            }
        }

        // Fungsi untuk mereset form
        function resetForm() {
            const tableHead = document.getElementById('table-head');
            const tableBody = document.getElementById('table-body');
            const previewButtons = document.getElementById('preview-buttons');
            const fileInput = document.getElementById('excel_file');

            // Reset tabel
            tableHead.innerHTML = '';
            tableBody.innerHTML = '';

            // Sembunyikan tombol preview
            previewButtons.style.display = 'none';

            // Reset data preview
            previewData = [];

            // Reset file input
            fileInput.value = '';
        }

        // Event listener untuk file input
        document.getElementById('excel_file').addEventListener('change', function(e) {
            if (!this.files.length) {
                return;
            }

            const fileInput = this;
            const loadingOverlay = document.getElementById('loading-overlay');
            loadingOverlay.classList.remove('d-none');

            let formData = new FormData();
            formData.append('excel_file', this.files[0]);
            formData.append('_token', '{{ csrf_token() }}');

            fetch("{{ route('preview.excel') }}", {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': "{{ csrf_token() }}"
                },
                body: formData
            })
            .then(res => res.json())
            .then(data => {
                if (data.status === 'success') {
                    const headers = data.headers;
                    const rows = data.rows;

                    const thead = document.getElementById('table-head');
                    const tbody = document.getElementById('table-body');
                    const previewButtons = document.getElementById('preview-buttons');

                    thead.innerHTML = '';
                    tbody.innerHTML = '';

                    // Buat Header
                    let headerRow = '<tr>';
                    headers.forEach(header => {
                        headerRow += `<th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 text-center">${header}</th>`;
                    });
                    headerRow += '</tr>';
                    thead.innerHTML = headerRow;

                    // Buat Baris Data
                    rows.forEach(row => {
                        let rowHtml = '<tr>';
                        headers.forEach(header => {
                            rowHtml += `<td class="text-sm text-center">${row[header] ?? ''}</td>`;
                        });
                        rowHtml += '</tr>';
                        tbody.innerHTML += rowHtml;
                    });

                    previewData = rows;
                    previewButtons.style.display = 'flex';
                    showAlert('Data berhasil di-preview!');
                } else {
                    showAlert('Gagal memproses file Excel!', 'danger');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                showAlert('Terjadi kesalahan saat mengunggah file.', 'danger');
            })
            .finally(() => {
                loadingOverlay.classList.add('d-none');
            });
        });

        // Event listener untuk tombol Cancel
        document.getElementById('cancelPreview').addEventListener('click', resetForm);

        // Event listener untuk tombol Simpan
        document.getElementById('simpanData').addEventListener('click', function () {
            const saveBtn = this;
            saveBtn.dataset.originalText = saveBtn.querySelector('.btn-text').textContent;
            setLoading(saveBtn, true);

            fetch("{{ route('simpan.data') }}", {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': "{{ csrf_token() }}"
                },
                body: JSON.stringify({ data: previewData })
            })
            .then(res => res.json())
            .then(result => {
                if (result.status === 'success') {
                    showAlert('Data berhasil disimpan ke database!');
                    resetForm();
                } else {
                    showAlert('Gagal menyimpan data: ' + result.message, 'danger');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                showAlert('Terjadi kesalahan saat menyimpan data.', 'danger');
            })
            .finally(() => {
                setLoading(saveBtn, false);
            });
        });
    </script>

</x-layout>