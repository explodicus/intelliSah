@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header">Game sessions</div>
                    <div class="card-body">
                        <form method="post" action="{{ route('session.store') }}">
                            @csrf
                            <div class="form-group">
                                <label class="control-label">Session name</label>
                                <input class="form-control" name="name" required
                                       value="Game {{ \Illuminate\Support\Facades\Auth::user()->name }}">
                            </div>
                            <button class="btn btn-success">Save</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
