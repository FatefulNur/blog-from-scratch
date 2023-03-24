@php
    $usr_title = 'User ' . $user->name;
@endphp

@extends('layouts.admin.admin')
@section('title', $usr_title)

@section('heading', $usr_title)

@section('body')
    @include('partials.admin.usercard', ['user' => $user, 'url' => URL::previous()])
@endsection
