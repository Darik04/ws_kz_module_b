@extends('layout.main')
@section('title')
Registration
@endsection
    @section('content')
    <div class="container b-container">
        <div class="card shadow-sm p-4 my-5">
            <h1 class="mb-4">Registration</h1>
            <form action="/register" method="post">
                @csrf
                <div class="form-floating">
                    <input id="usernameId" class="form-control" name="username" required="Username required">
                    <label for="usernameId">Username</label>
                </div>
                <div class="form-floating my-3">
                    <input id="passwordId" class="form-control" type="password" name="password" required="Password required">
                    <label for="passwordId">Password</label>
                </div>
                <button class="btn btn-primary">Registration</button>
            </form>
        </div>
    </div>
@endsection
