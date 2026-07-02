@extends('layouts.app')

@section('content')
    <h1>{{ $title }}</h1>
    <p>Username: {{ $username }}</p>
    <p>Perusahaan: {{ $klien->nama }}</p>
    <div class="records">
        {{-- @foreach($records as $record)
            <div class="record">
                <p>{{ $record->nama }}</p>
                <p>{{ $record->harga }}</p>
            </div>
        @endforeach --}}
    </div>
@endsection