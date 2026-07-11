@extends('layouts.app')
@section('title', '403 - Akses Ditolak ')
@section('content')
    {{-- <style>
        body {
            font-family: sans-serif;
            text-align: center;
            padding: 50px;
            background: #f8f9fa;
        }

        .error-container {
            max-width: 500px;
            margin: auto;
            background: white;
            padding: 30px;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        }

        h1 {
            color: #dc3545;
        }

        p {
            color: #6c757d;
            font-size: 16px;
        }

        .btn-kembali {
            display: inline-block;
            margin-top: 20px;
            padding: 10px 20px;
            background: #007bff;
            color: white;
            text-decoration: none;
            border-radius: 5px;
            font-weight: bold;
        }

        .btn-kembali:hover {
            background: #0056b3;
        }
    </style> --}}
    <div class="row g-3">
        <div class="col-12">
            <div class="card bg-light mb-3">
                <div class="card-body text-center" style="padding:14px 20px">
                    <h3>403 - Akses Ditolak</h3>

                    <p>{{ $exception->getMessage() ?: 'Anda tidak memiliki izin untuk mengakses halaman ini.' }}</p>

                    <a href="javascript:history.back()" class="btn btn-primary">
                        <i class="fas fa-arrow-left"></i> Kembali
                    </a>
                </div>
            </div>
        </div>
    </div>

@endsection
