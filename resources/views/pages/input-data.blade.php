<x-layout bodyClass="g-sidenav-show bg-gray-200">
    <x-navbars.sidebar activePage="input-data"></x-navbars.sidebar>
    <main class="main-content position-relative max-height-vh-100 h-100 border-radius-lg">
        <x-navbars.navs.auth titlePage="Input Data"></x-navbars.navs.auth>
        <div class="container-fluid py-4">
            <div class="row">
                <div class="col-12">
                    <div class="card my-4">
                        <div class="card-header p-0 position-relative mt-n4 mx-3 z-index-2">
                            <div class="bg-gradient-primary shadow-primary border-radius-lg pt-4 pb-3">
                                <h6 class="text-white text-capitalize ps-3">Upload & Preview Data Siswa</h6>
                            </div>
                        </div>
                        <div class="card-body px-4 pb-4">
                            <form id="upload-form" enctype="multipart/form-data" class="mb-4">
                                @csrf
                                <div class="d-flex justify-content-between align-items-center gap-3">
                                    <input type="file" name="excel_file" id="excel_file" class="form-control w-50"
                                        accept=".xls,.xlsx">
                                    <button type="submit" class="btn btn-primary">Upload & Preview</button>
                                </div>
                            </form>

                            <div class="table-responsive">
                                <table id="preview-table" class="table table-bordered text-center align-middle mb-0">
                                    <thead id="table-head" class="bg-light"></thead>
                                    <tbody id="table-body"></tbody>
                                </table>
                            </div>

                            <div class="d-flex justify-content-end mt-4 gap-2" id="preview-buttons"
                                style="display: none;">
                                <button id="simpanData" class="btn btn-success">Simpan ke Database</button>
                                <button id="cancelPreview" class="btn btn-danger">Cancel Preview</button>
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

        document.getElementById('upload-form').addEventListener('submit', function (e) {
            e.preventDefault();

            let formData = new FormData(this);

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

                        previewData = rows; // Simpan ke variabel global
                        previewButtons.style.display = 'flex';
                    } else {
                        alert('Gagal memproses file Excel!');
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('Terjadi kesalahan saat mengunggah file.');
                });
        });

        // Fungsi untuk mereset input file
        function resetFileInput(inputElement) {
            inputElement.value = '';
            // Untuk beberapa browser (seperti Firefox) perlu trik berikut:
            if (inputElement.value) {
                inputElement.type = "text";
                inputElement.type = "file";
            }
        }

        document.getElementById('cancelPreview').addEventListener('click', function () {
    const tableHead = document.getElementById('table-head');
    const tableBody = document.getElementById('table-body');
    const previewButtons = document.getElementById('preview-buttons');
    const fileInput = document.getElementById('excel_file');

    // Kosongkan tabel secara aman
    tableHead.replaceChildren();
    tableBody.replaceChildren();

    // Sembunyikan tombol preview
    previewButtons.style.display = 'none';

    // Kosongkan data preview
    previewData = [];

    // Reset file input dengan delay agar lebih andal di semua browser
    fileInput.value = '';
    fileInput.type = 'text';
    fileInput.type = 'file';
    setTimeout(() => fileInput.value = '', 10);
});

        // Event listener tombol Simpan ke Database
        document.getElementById('simpanData').addEventListener('click', function () {
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
                        alert('Data berhasil disimpan ke database!');
                        document.getElementById('cancelPreview').click(); // Trigger reset
                    } else {
                        alert('Gagal menyimpan data: ' + result.message);
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('Terjadi kesalahan saat menyimpan data.');
                });
        });
    </script>

</x-layout>