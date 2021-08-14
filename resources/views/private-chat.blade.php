@extends('layouts.app')
@section('content')
    <privatechat :user="{{Auth::user()}}"></privatechat>
@endsection