@extends('layouts.admin.admin')
@section('title', 'Media')

@section('heading', 'Media Details')
@push('style')
    <style>
        .image {
            width: 300px;
            height: auto;
        }
        .image img {
            width: 100%;
            display: block;
            background-size: cover;
        }
    </style>
@endpush

@section('body')
    <div class="row">
        <div class="col-lg-6">
            <div class="image">
                <img src="{{ asset($image->path) }}" alt="nothing">
            </div>
        </div>
        <div class="col-lg-6">
            <h2>Image Name</h2>
            <p class="text-muted">{{ $image->name ?? "my Image" }}</p>
            <h3>Image Caption</h3>
            <p class="text-muted">{{ $image->caption ?? "images are best" }}</p>
            <h4>Image Details</h4>
            <p class="text-muted">{{ $image->details ?? "loving captured for you" }}</p>
            <h4>Image Type</h4>
            <p class="text-muted">{{ Str::after($image->imagable_type, 'App\Models\\') }}</p>
        </div>
        <div class="col-md-6">
            <div class="panel">
                <div class="panel-heading">
                    Update Image
                </div>
                <div class="panel-body">
                    @include('partials.admin.sessions', ['session' => 'action'])

                    <form role="form" action="{{ route('admin.media.update', $image->id) }}" method="POST">
                        @method('PATCH')
                        @csrf
                        <div class="form-group">
                            <label class="control-label" for="name">Name</label>
                            <input type="text" name="name" class="form-control" id="name"
                                value="{{ old('name', $image->name) }}">
                        </div>
                        <div class="form-group">
                            <label class="control-label" for="Caption">Caption</label>
                            <input type="text" name="caption" class="form-control" id="caption"
                                value="{{ old('caption', $image->caption) }}">
                        </div>
                        <div class="form-group">
                            <label class="control-label" for="details">Details</label>
                            <input type="text" name="details" class="form-control" id="details"
                                value="{{ old('details', $image->details) }}">
                        </div>
                        <button type="submit" class="btn btn-primary">Update</button>
                    </form>
                </div>

            </div>
        </div>

    </div>
@endsection
