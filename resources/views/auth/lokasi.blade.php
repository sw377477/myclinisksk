@extends('layouts.auth')

@section('content')
<div class="card shadow-lg border-0 rounded-3 p-4" style="max-width: 450px; margin:auto;">
    <div class="card-body">
        <h2 class="text-center mb-4 fw-bold">📍 Pilih Lokasi</h2>

        <form method="POST" action="{{ url('/lokasi') }}">
            @csrf
            <div class="mb-4">
                <label class="form-label fs-5">Lokasi</label>
                <select name="lokasi" class="form-select form-select-lg mb-4" required>
                    <option value="">-- Pilih Lokasi --</option>
                    @foreach($lokasi as $l)
                        <option value="{{ $l->lokasi }}">{{ $l->lokasi }}</option>
                    @endforeach
                </select>
            </div>

            <div class="d-flex">
                <button type="submit" class="btn btn-primary flex-fill me-2 py-3 fs-5 rounded-pill">
                    Pilih Lokasi
                </button>
                <a href="{{ url('/login') }}" class="btn btn-secondary flex-fill py-3 fs-5 rounded-pill">
                    Cancel
                </a>
            </div>
        </form>
    </div>
</div>
@endsection
