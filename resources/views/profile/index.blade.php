@php /** @var \App\User $user */ @endphp
@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header">
                        Profile
                        <a href="{{ route("profile.edit") }}">Edit</a>
                    </div>

                    <div class="card-body">

                        <div class="text-center">
                            <img class="img-thumbnail rounded-circle" src="{{ $user->display_picture_url }}" alt="{{ $user->name }}" style="height: 100px"/>
                        </div>

                        <p><strong>Name: </strong> {{ $user->name }}</p>
                        <p><strong>Email: </strong> {{ $user->email }}</p>
                        <p><strong>Registered on: </strong> {{ $user->created_at->toDateTimeString() }}</p>

                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
