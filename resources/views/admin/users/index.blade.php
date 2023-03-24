@extends('layouts.admin.admin')
@section('title', 'Users Setting')

@push('style')
    <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.13.1/css/jquery.dataTables.css">
@endpush

@section('heading', 'Users')

@section('body')
    <div class="row">
        <div class="col-lg-12">
            <div class="panel panel-primary">
                <div class="panel-heading">
                    All Users Data
                </div>

                <div class="panel-body">

                    @include('partials.admin.sessions', ['session' => 'action'])

                    <table id="users_table" class="display">
                        <thead>
                            <tr>
                                <th></th>
                                <th>#</th>
                                <th>Name</th>
                                <th>Email</th>
                                <th>Role</th>
                                <th>Change Role</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($users as $user)
                                @php
                                    $url = $user->isAdmin() ? route('admin.users.make-user', $user->id) : route('admin.users.make-admin', $user->id);
                                    $text = $user->isAdmin() ? 'Change to user' : 'Make Admin';
                                @endphp

                                <tr>
                                    <td>{{ $loop->iteration }}</td>
                                    <td>{{ $user->id }}</td>
                                    <td><a href="{{ route('admin.users.show', $user->id) }}">{{ $user->name }}</a></td>
                                    <td>{{ $user->email }}</td>
                                    <td>{{ $user->isAdmin() ? 'Admin' : 'User' }}</td>
                                    <td>
                                        @if (auth()->user()->isOwner($user->id))
                                            you
                                        @else
                                            <form action="{{ $url }}" method="POST">
                                                @csrf
                                                @method('PATCH')
                                                <button class="btn btn-sm btn-link" type="submit"
                                                    href="{{ $url }}">{{ $text }}</button>
                                            </form>
                                        @endif
                                    </td>
                                    <td>
                                        @include('partials.admin.actions', ['user' => $user])
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

            </div>

        </div>

    </div>
@endsection

@push('script')
    <script type="text/javascript" charset="utf8" src="https://cdn.datatables.net/1.13.1/js/jquery.dataTables.js"></script>

    <script>
        $(document).ready(function() {
            $('#users_table').DataTable();
        });
    </script>
@endpush
