@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">Messages</div>

                    <div class="card-body">

                        <table class="table">
                            <thead>
                                <tr>
                                    <th style="width: 10%">SN</th>
                                    <th>Name</th>
                                    <th>Received on</th>
                                </tr>
                            </thead>

                            <tbody>
                                @forelse($messages as $message)
                                    @php /** @var \App\Message $message */ @endphp
                                    <tr>
                                        <td>{{ $loop->iteration }}</td>
                                        <td>
                                            <a href="{{ route('messages.show', $message->id) }}">{{ $message->name }}</a>
                                        </td>
                                        <td>{{ $message->created_at->toDateTimeString() }}</td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="3">No messages found.</td>
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
