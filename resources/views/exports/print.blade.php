@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header text-center">
                    <h4>{{ $title }}</h4>
                    <p>Tahun Ajaran: {{ $schoolYear }}</p>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered">
                            <thead>
                                <tr>
                                    @foreach($columns as $column)
                                        <th>{{ ucwords(str_replace('_', ' ', $column)) }}</th>
                                    @endforeach
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($data as $row)
                                    <tr>
                                        @foreach($columns as $column)
                                            <td>{{ $row[$column] ?? '' }}</td>
                                        @endforeach
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="card-footer text-center">
                    <p>Dicetak pada: {{ date('d/m/Y H:i:s') }}</p>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    @media print {
        body * {
            visibility: hidden;
        }
        .print-content, .print-content * {
            visibility: visible;
        }
        .print-content {
            position: absolute;
            left: 0;
            top: 0;
            width: 100%;
        }
        .no-print {
            display: none !important;
        }
        .card {
            border: none !important;
        }
        .table {
            width: 100% !important;
        }
        .table td, .table th {
            padding: 8px !important;
        }
        @page {
            size: A4;
            margin: 1cm;
        }
    }
</style>

<div class="no-print text-center mt-3">
    <button onclick="printDocument()" class="btn btn-primary">
        <i class="material-icons">print</i> Cetak
    </button>
    <a href="{{ route('export') }}" class="btn btn-secondary">
        <i class="material-icons">arrow_back</i> Kembali
    </a>
</div>

<script>
    // Function to handle printing
    function printDocument() {
        window.print();
    }

    // Auto print when page loads
    window.onload = function() {
        // Small delay to ensure everything is loaded
        setTimeout(function() {
            window.print();
        }, 500);
    };

    // Handle print dialog close
    window.onafterprint = function() {
        // Optional: Redirect back to export page after printing
        // window.location.href = "{{ route('export') }}";
    };
</script>
@endsection 