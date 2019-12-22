@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">Projects</div>

                    <div class="card-body">

                        <table class="table">
                            <thead>
                                <tr>
                                    <th>SN</th>
                                    <th>Title <a href="{{ route('projects') }}?sort=title" title="Sort by title">Sort</a></th>
                                    <th>Has Started? <a href="{{ route('projects') }}?sort=is_started" title="Sort by Status">Sort</a></th>
                                </tr>
                            </thead>

                            <tbody>
                                @forelse($projects as $project)
                                    @php /** @var \App\Project $project */ @endphp
                                    <tr>
                                        <td>{{ $loop->iteration }}</td>
                                        <td>
                                            <a href="{{ route('projects.show', $project->id) }}">{{ $project->title }}</a>
                                        </td>
                                        <td>{{ $project->is_started ? 'Yes' : 'No' }}</td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="3">No projects found.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>

                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
