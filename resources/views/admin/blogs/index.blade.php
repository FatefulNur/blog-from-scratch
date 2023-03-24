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
                            <a href="{{ route('admin.blogs.trash') }}" class="btn btn-default btn-sm">Bin</a>
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
                                <th>category</th>
                                <th>tags</th>
                                <th>created by</th>
                                <th>has gallery</th>
                                <th>short description</th>
                                <th>description</th>
                                <th>comments</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @if ($blogs)
                                @foreach ($blogs as $blog)
                                    @php
                                        $path = (!is_null($blog->image->path)) ? $blog->image->path : $blog->defaultThumbnail();
                                    @endphp
                                    <tr>
                                        <td><img src="{{ asset($path) }}" alt="thumbnail" width="40" height="40"></td>
                                        <td>{{ $blog->title }}</td>
                                        <td>{{ ($blog->category) ? $blog->category->name : "uncategorized" }}</td>
                                        <td>{{ ($blog->tags->count()) ?: 0 }}</td>
                                        <td>{{ $blog->user->name }}</td>
                                        <td>{{ ($blog->gallery->blog_id > 0) ? "yes" : "no" }}</td>
                                        <td>{{ ($blog->short_desc) ?? "--" }}</td>
                                        <td>{{ $blog->excerpt(30) }}</td>
                                        <td>{{ $blog->comments->count() }}</td>
                                        <td>
                                            <form style="display: inline-block;" action="{{ route('admin.blogs.delete', $blog->slug) }}" method="post" onsubmit="return confirm('Wanna move to bin?');">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="text-danger fa fa-trash-o"></button>
                                            </form>
                                            <form style="display: inline-block;" action="{{ route('admin.blogs.edit', $blog->slug) }}" method="get">
                                                @csrf
                                                <button type="submit" class="text-primary fa fa-edit"></button>
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
