@extends('layouts.admin.admin')
@section('title', 'Media')

@push('style')
    <style>
        .galleries {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            grid-auto-rows: 200px;
            gap: 10px;
        }
        .image {
            position: relative;
            display: grid;
            grid-template-columns: 100%;
            grid-template-rows: 100%;
            border: 10px solid #ddd;
            box-shadow: 3px 4px 5px #00000015;
        }
        .image img {
            width: 100%;
            height: 100%;
            display: block;
            object-fit: cover;
        }
        .details {
            position: absolute;
            top: 0;
            right: 0;
        }
    </style>
@endpush

@section('heading', 'Media Lists')

@section('body')
    <div class="row">
        <div class="col-lg-12">

            <div class="galleries">
                @foreach ($images as $image)
                    <div class="image">
                        <img src="{{ asset($image->path) }}" alt="image">
                        <div class="details">
                            <a style="display: inline-block;" href="{{ route('admin.media.show', $image->id) }}">
                                <button type="submit" class="text-success fa fa-list"></button>
                            </a>
                            <a style="display: inline-block;" href="{{ route('admin.media.edit', $image->id) }}">
                                <button type="submit" class="text-primary fa fa-edit"></button>
                            </a>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>
@endsection
