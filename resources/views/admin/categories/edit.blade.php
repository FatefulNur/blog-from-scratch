@php
    $categoriesDesc = $categories->sortByDesc('id');
@endphp
@extends('layouts.admin.admin')
@section('title', 'Updating Category')

@push('style')
    <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.13.1/css/jquery.dataTables.css">
@endpush

@section('heading', 'Updating category')

@section('body')

    <div class="row">
        @include('partials.admin.errors', [
            'errors' => $errors,
        ])
        <div class="col-lg-3">
            <div class="panel">
                <div class="panel-heading">
                    update a category
                </div>
                <div class="panel-body">

                    <form role="form" action="{{ route('admin.categories.update', $category->name) }}" method="POST">
                        @csrf
                        @method('PUT')
                        <div class="form-group @error('name') has-error @enderror">
                            <label class="control-label" for="name">Name</label>
                            <input type="text" name="name" class="form-control" id="name"
                                value="{{ old('name', $category->name) }}">
                        </div>
                        <div class="form-group @error('parent_id') has-error @enderror">
                            <label class="control-label" for="parent_id">Parent</label>
                            <select name="parent_id" id="parent_id" class="form-control">
                                <option value="">---------</option>

                                @foreach ($categories as $cat)
                                    @if (!$cat->parent)
                                        <option value="{{ $cat->id }}" @selected(old('parent_id', $category->parent_id) == $cat->id)>{{ $cat->name }}</option>
                                        @if ($cat->children->count())
                                            @include('partials.admin.category-options', [
                                                'childrens' => $cat->children,
                                                'prefix' => str_repeat("-", $loop->depth),
                                            ])
                                        @endif
                                    @endif
                                @endforeach
                            </select>

                        </div>
                        <div class="form-group @error('icon') has-error @enderror">
                            <label class="control-label" for="icon">Set an Icon</label>
                            <input type="text" name="icon" class="form-control" id="icon"
                                value="{{ old('icon', $category->icon) }}">
                            <a href="https://fontawesome.com/v4/icons/" target="_blank" style="text-decoration: none"
                                class="text-muted small">Check here for icons</a>
                        </div>
                        <button type="submit" class="btn btn-primary">Update</button>
                        <button type="reset" class="btn btn-success">Reset</button>
                    </form>
                </div>

            </div>

        </div>

        <div class="col-lg-9">
            <div class="panel panel-primary">
                <div class="panel-heading">
                    All Categories Data
                </div>

                <div class="panel-body">

                    @include('partials.admin.sessions', ['session' => 'action'])

                    <table id="categories_table" class="display">
                        <thead>
                            <tr>
                                <th></th>
                                <th>Name</th>
                                <th>parent</th>
                                <th>child total</th>
                                <th>level</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($categoriesDesc as $category)
                                <tr>
                                    <td><i class="{{ $category->icon }}"></i></td>
                                    <td>{{ $category->name }}</td>
                                    <td>{{ $category->parent ? $category->parent->name : 'None' }}</td>
                                    <td>{{ $category->countDescendants() }}</td>
                                    <td>{{ $category->depth }}</td>
                                    <td>
                                        <form style="display: inline-block;" action="{{ route('admin.categories.delete', $category->name) }}" method="post" onsubmit="return confirm('Wanna delete {{$category->name}}')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="text-danger fa fa-trash-o"></button>
                                        </form>
                                        <a style="display: inline-block;" href="{{ route('admin.categories.edit', $category->name) }}">
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
            $('#categories_table').DataTable();
        });
    </script>
@endpush
