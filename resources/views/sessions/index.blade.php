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
                            ]) }}">
                                <span>{{ $session->name }}</span>
                                @if ($session->gameover)
                                    @if ($session->currentSubscription->user_id == \Auth::id())
                                        <span class="float-right badge badge-success">Win</span>
                                    @else
                                        <span class="float-right badge badge-warning">Lose</span>
                                    @endif
                                @elseif ($session->status === 0)
                                    <span class="float-right badge badge-info">Waiting</span>
                                @else
                                    <span class="float-right badge badge-primary">Playing</span>
                                @endif
                           </a>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection