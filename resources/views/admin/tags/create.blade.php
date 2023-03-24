@extends('layouts.admin.admin')
@section('title', 'Work with Tags')

@push('style')
    <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.13.1/css/jquery.dataTables.css">

@endpush

@section('heading', 'Work with Tags')

@section('body')

    <div class="row">
        @include('partials.admin.errors', [
            'errors' => $errors,
        ])
        <div class="col-lg-3">
            <div class="panel">
                <div class="panel-heading">
                    Add a new tag
                </div>
                <div class="panel-body">

                    <form role="form" action="{{ route('admin.tags.store') }}" method="POST">
                        @csrf
                        <div class="form-group @error('name') has-error @enderror">
                            <label class="control-label" for="name">Name</label>
                            <input type="text" name="name" class="form-control" id="name"
                                value="{{ old('name') }}">
                        </div>
                        <button type="submit" class="btn btn-primary">Add</button>
                    </form>
                </div>

            </div>

        </div>

        <div class="col-lg-9">
            <div class="panel panel-primary">
                <div class="panel-heading">
                    All Tags Data
                </div>

                <div class="panel-body">

                    @include('partials.admin.sessions', ['session' => 'action'])

                    <table id="tags_table" class="display">
                        <thead>
                            <tr>
                                <th></th>
                                <th>Name</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($tags as $tag)
                                <tr>
                                    <td></td>
                                    <td>{{ $tag->name }}</td>
                                    <td>
                                        <form style="display: inline-block;" action="{{ route('admin.tags.delete', $tag->name) }}" method="post" onsubmit="return confirm('Wanna delete {{$tag->name}}')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="text-danger fa fa-trash-o"></button>
                                        </form>
                                        <a style="display: inline-block;" href="{{ route('admin.tags.edit', $tag->name) }}">
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
            $('#tags_table').DataTable();
        });
    </script>


@endpush
