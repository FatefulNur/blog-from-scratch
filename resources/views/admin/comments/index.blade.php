@extends('layouts.admin.admin')
@section('title', 'Comments')

@push('style')
    <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.13.1/css/jquery.dataTables.css">
@endpush

@section('heading', 'Comments')

@section('body')
    <div class="row">
        <div class="col-lg-12">
            <div class="panel panel-primary">
                <div class="panel-heading">
                    All Comments Data
                </div>

                <div class="panel-body">

                    @include('partials.admin.sessions', ['session' => 'action'])

                    <table id="users_table" class="display">
                        <thead>
                            <tr>
                                <th>body</th>
                                <th>Comment by</th>
                                <th>Comment of</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($comments as $comment)
                                <tr>
                                    <td>{{ $comment->body }}</td>
                                    <td>{{ $comment->user->name }}</td>
                                    <td>{{ $comment->blog->title }}</td>
                                    <td>
                                        <form style="display: inline-block;"
                                            action="{{ route('admin.comments.delete', $comment->id) }}" method="post"
                                            onsubmit="return confirm('Wanna delete');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="text-danger fa fa-trash-o"></button>
                                        </form>
                                        <a style="display: inline-block;"
                                            href="{{ route('admin.comments.edit', $comment->id) }}">
                                            <button type="submit" class="text-primary fa fa-edit"></button>
                                        </a>
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
