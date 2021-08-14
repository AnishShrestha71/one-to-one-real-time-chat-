@extends('layouts.app')
@section('content')
    <div class="">
            
            <chatbox :user="{{Auth::user()}}"></chatbox>
    </div>
    
@endsection