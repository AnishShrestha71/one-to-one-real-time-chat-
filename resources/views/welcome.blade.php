@extends('layouts.app')
@section('content')
    <div class="">
        @guest
            Group chat is available only for logged in user
        @else
            <chatbox :user="{{ Auth::user() }}"></chatbox>
        @endguest

    </div>

@endsection
