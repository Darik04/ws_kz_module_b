@extends('layout.main')
@section('title')
    Workspaces
@endsection

@section('content')
    <div class="container m-container">
        <h2 class="h2 my-4">Workspaces</h2>
        <div class="row">
            @foreach($workspaces as $item)
                <a href="/workspace/{{$item->id}}" class="card shadow-sm p-3 m-card">
                    <h5 class="m-h5">
                        {{$item->title}}
                    </h5>
                    <p class="p">{{$item->description}}</p>
{{--                    <p class="p">limit: 5$</p>--}}
                </a>
            @endforeach
        </div>


        <div class="card shadow-sm p-4 mt-4">
            <h3 class="h3 mb-3">Creating new workspace:</h3>
            <form action="/workspace/create" method="post">
                @csrf
                <div class="form-floating mb-3">
                    <input id="workspaceId" type="text" name="name" class="form-control">
                    <label for="workspaceId">Workspace</label>
                </div>
                <div class="form-floating">
                    <input id="descId" type="text" name="description" class="form-control">
                    <label for="descId">Description</label>
                </div>
                <button class="btn btn-primary mt-3">Create</button>
            </form>
        </div>
    </div>
@endsection
