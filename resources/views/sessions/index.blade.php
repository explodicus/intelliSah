@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card">
                    <div class="card-header">Game sessions</div>
                    <div class="card-body">
                        <a href="{{ route('session.create') }}" class="btn btn-success">New game</a>
                    </div>
                    <div class="list-group">
                        @foreach($sessions as $session)
                            <a class="list-group-item" href="{{ route('session.show', [
                                'session' => $session,
                            ]) }}">{{ $session->name }}</a>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection