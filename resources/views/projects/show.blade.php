@php /** @var \App\Project $project */ @endphp

@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">{{ $project->title }}</div>

                    <div class="card-body">
                        <p><strong>Has started: </strong> {{ $project->is_started ? 'Yes' : 'No' }}</p>
                        <p>{!! nl2br($project->description) !!}</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
