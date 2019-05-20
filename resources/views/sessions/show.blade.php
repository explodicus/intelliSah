@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">
                        {{ $session->name }}
                        <subscribers
                                subscribers-uri="{{ route('session.subscribers', ['session' => $session]) }}"
                                :game-session="{{ $session->id }}"
                        >
                        </subscribers>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            @can('subscribe', $session)
                                <div class="form-inline ml-3">
                                    <form method="get" action="{{ route('session.subscribe', [
                                            'session' => $session,
                                        ]) }}" class="form-inline">
                                        <button class="btn btn-success">Play with</button>
                                    </form>
                                </div>
                            @endcan
                            <div class="col-3 form-inline">
                                <label class="custom-control-inline">Initiator:</label>
                                <input class="form-control" disabled value="{{ $sessionUser->name }}">
                            </div>
                            <div class="col-4 form-inline">
                                @if ($session->subscriptions->count() && $session->status == 0 && $sessionUser->id === $currentUser->id)
                                    <form class="form-inline" method="POST" action="{{ route('session.subscribe.bots', $session) }}">
                                        @csrf
                                        <button class="btn btn-primary mr-3">Start game with bots</button>
                                        <select name="bot_level" class="form-control">
                                            <option value="1">Bots level 1</option>
                                            <option value="2">Bots level 2</option>
                                            <option value="3">Bots level 3</option>
                                        </select>
                                    </form>
                                @endif
                            </div>
                        </div>
                        <div class="row">
                            <game-table
                                    :current-subscription="{{ json_encode($currentSubscription) }}"
                                    :session="{{ json_encode($session) }}"
                                    handle-uri="{{ route('game.handle', ['session' => $session]) }}"
                            >
                            </game-table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('styles')
    <link href="{{ asset('css/styles.css') }}" rel="stylesheet">
@endsection

@section('scripts')
    <script src="{{ asset('js/main.js') }}"></script>
@endsection
