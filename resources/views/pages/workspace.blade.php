@extends('layout.main')
@section('title')
    Workspace - {{$workspace->title}}
@endsection

@section('content')
    <div class="container w-container">
        <h2 class="h2 my-4">Workspace - {{$workspace->title}}</h2>
        @if($all_tokens->count() != 0)
        <h3 class="h3 center my-4">Workspace Tokens</h3>
        <table class="table border shadow-sm">
            <tr>
                <th>ID</th>
                <th>Name</th>
                <th>Token</th>
                <th>Deactivated at</th>
                <th>Created at</th>
            </tr>
            @foreach($all_tokens as $token)
                <tr>
                    <td>{{$token->id}}</td>
                    <td>{{$token->name}}</td>
                    <td>
                        @if($token_id == $token->id)
                            {{$token->token}}
                        @else
                            Hidden
                        @endif
                    </td>
                    <td>
                        @if($token->deactivated)
                            {{\Carbon\Carbon::parse($token->deactivated_at)->format('Y M d h:m')}}
                        @else
                            <form method="post" action="/workspace/token/{{$token->id}}/deactivate">
                                @csrf
                                <button class="btn btn-danger">Deactivate</button>
                            </form>
                        @endif
                    </td>
                    <td>{{\Carbon\Carbon::parse($token->created_at)->format('Y M d h:m')}}</td>
                </tr>
            @endforeach
        </table>
        @endif
        <div class="card shadow-sm p-4 mt-4">
            <h3 class="h3 mb-3">Creating new token:</h3>
            <form action="/workspace/{{$workspace->id}}/token/create" method="post">
                @csrf
                <div class="form-floating">
                    <input id="tokenId" type="text" name="name" class="form-control">
                    <label for="tokenId">Token name</label>
                </div>
                <button class="btn btn-primary mt-3">Create</button>
            </form>
        </div>
        <div class="card shadow-sm p-4 mt-4">
            @if($quota)
                <h3 class="h3 mb-3">Your billing quota is: {{$quota->limit}}$</h3>
                <form method="post" action="/workspace/{{$workspace->id}}/delete-quota" class="d-flex">
                    @csrf
                    <button class="btn btn-success">Delete quota</button>
                </form>
            @else
                <h3 class="h3 mb-3">Create quota</h3>
                <form action="/workspace/{{$workspace->id}}/create-quota" method="post">
                    @csrf
                    <div class="form-floating">
                        <input id="limitId" type="text" name="limit" class="form-control">
                        <label for="limitId">Limit</label>
                    </div>
                    <button class="btn btn-success mt-3">Create</button>
                </form>
            @endif
        </div>
        <h3 class="h3 center my-4">Bills</h3>
        <a href="/workspace/{{$workspace->id}}/bills" class="btn btn-primary">Show bills details</a>
        <div class="py-4"></div>
    </div>
@endsection
