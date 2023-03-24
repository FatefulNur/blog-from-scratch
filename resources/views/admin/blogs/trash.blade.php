@extends('layouts.admin.admin')
@section('title', 'Blogs Setting')

@push('style')
    <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.13.1/css/jquery.dataTables.css">
@endpush

@section('heading', 'Blogs')

@section('body')
    <div class="row">
        <div class="col-lg-12">
            <div class="panel panel-primary">
                <div class="panel-heading">
                    <div class="row justify-cotent-between">
                        <div class="col-md-6">All Blogs Data</div>
                        <div class="col-md-6 text-right">
                            @if ($trashes && $trashes->isNotEmpty())
                                <form style="display: inline-block;" action="{{ route('admin.blogs.empty-trash') }}" method="post" onsubmit="return confirm('Wanna delete all storage?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-white fa fa-trash-o" style="background: darkred;
                                    border: none;
                                    padding: 2px 5px;
                                    display: inline-block;"></button>
                                </form>
                                <form style="display: inline-block;" action="{{ route('admin.blogs.restore-all') }}" method="post">
                                    @csrf
                                    @method('PATCH')
                                    <button title="Restore" type="submit" class="text-white fa fa-repeat" style="background: darkgreen;
                                    border: none;
                                    padding: 2px 5px;
                                    display: inline-block;"></button>
                                </form>
                            @else
                                <a href="{{ route('admin.blogs.index') }}" class="btn btn-small btn-default">Go to blogs</a>
                            @endif
                        </div>
                    </div>
                </div>

                <div class="panel-body">

                    @include('partials.admin.sessions', [
                        'session' => 'action'
                    ])

                    <table id="blogs_table" class="display">
                        <thead>
                            <tr>
                                <th>image</th>
                                <th>title</th>
                                <th>created by</th>
                                <th>has gallery</th>
                                <th>short description</th>
                                <th>description</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @if ($trashes)
                                @foreach ($trashes as $trash)
                                    @php
                                        $path = (!is_null($trash->image->path)) ? $trash->image->path : $trash->defaultThumbnail();
                                    @endphp
                                    <tr>
                                        <td><img src="{{ asset($path) }}" alt="thumbnail" width="40" height="40"></td>
                                        <td>{{ $trash->title }}</td>
                                        <td>{{ $trash->user->name }}</td>
                                        <td>{{ ($trash->gallery->blog_id > 0) ? "yes" : "no" }}</td>
                                        <td>{{ ($trash->short_desc) ?? "--" }}</td>
                                        <td>{{ $trash->excerpt(30) }}</td>
                                        <td>
                                            <form style="display: inline-block;" action="{{ route('admin.blogs.force-delete', $trash->slug) }}" method="post" onsubmit="return confirm('Wanna remove it?');">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="text-danger fa fa-trash-o"></button>
                                            </form>
                                            <form style="display: inline-block;" action="{{ route('admin.blogs.restore', $trash->slug) }}" method="post">
                                                @csrf
                                                @method('PATCH')
                                                <button title="Restore" type="submit" class="text-success fa fa-repeat"></button>
                                            </form>
                                        </td>
                                    </tr>
                                @endforeach
                            @endif
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
            $('#blogs_table').DataTable();
        });
    </script>
@endpush
