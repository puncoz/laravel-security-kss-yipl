@php /** @var \App\Message $message */ @endphp

@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">
                        {{ $message->name }}

                        <a href="{{ route('messages') }}">Back to list</a>
                    </div>

                    <div class="card-body">
                        <p><strong>Received On:</strong> {!! $message->created_at->toDateTimeString() !!}</p>
                        <p><strong>Message:</strong> {!! $message->message !!}</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
