@extends('layouts.auth')

@section('content')
<div class="card shadow-lg border-0 rounded-3 p-4" style="max-width: 450px; margin:auto;">
    <div class="card-body">
        <h2 class="text-center mb-4 fw-bold">🔐 Login MyClinis</h2>

        @if($errors->any())
            <div class="alert alert-danger">{{ $errors->first() }}</div>
        @endif

        <form method="POST" action="/login">
            @csrf
            <div class="mb-4">
                <label class="form-label fs-5">Username</label>
                <input type="text" name="username" class="form-control form-control-lg" required>
            </div>

            <div class="mb-4">
                <label class="form-label fs-5">Password</label>
                <input type="password" name="password" class="form-control form-control-lg mb-3" required>
            </div>

            <div class="d-flex">
                <button type="submit" class="btn btn-primary flex-fill me-2 py-3 fs-5 rounded-pill">Login</button>
                <a href="{{ route('login') }}" class="btn btn-secondary flex-fill py-3 fs-5 rounded-pill">Cancel</a>
            </div>
        </form>
    </div>
</div>
@endsection
