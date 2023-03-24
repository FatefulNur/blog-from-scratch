@extends('layouts.admin.admin')
@section('title', 'Edit comment')

@push('style')
    <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.13.1/css/jquery.dataTables.css">
@endpush

@section('heading', 'Edit Comments')

@section('body')

    <form role="form" action="{{ route('admin.comments.update', $comment->id) }}" method="POST" enctype="multipart/form-data">
        <div class="row">
            @include('partials.admin.errors', [
                'errors' => $errors,
            ])
            <div class="col-lg-8">
                <div class="panel">
                    <div class="panel-heading">
                        edit a comment
                    </div>
                    <div class="panel-body">

                        @csrf
                        @method('PUT')
                        <input type="hidden" name="blog_id" value="{{ $comment->blog_id }}">
                        <input type="hidden" name="user_id" value="{{ $comment->user_id }}">
                        <input type="hidden" name="status" value="published">
                        @if ($comment->image)
                            <div class="image-viewer">
                                <img src="{{ asset($comment->image->path) }}" alt="none">
                            </div>
                        @endif
                        <div class="form-group @error('photo') has-error @enderror">
                            <label class="control-label" for="photo">Body</label>
                            <input type="file" name="photo" class="form-control" id="photo">
                        </div>
                        <div class="form-group @error('body') has-error @enderror">
                            <label class="control-label" for="body">Body</label>
                            <textarea name="body" class="form-control" id="body">{{ old('body', $comment->body) }}</textarea>
                        </div>
                        <button type="submit" class="btn btn-primary btn-sm">Update Comment</button>
                        <button type="reset" class="btn btn-success btn-sm">Reset Comment</button>
                    </div>

                </div>

            </div>
            <div class="col-lg-4">
                <div class="panel">
                    <div class="panel-heading">
                        Status
                    </div>
                    <div class="panel-body">

                        @csrf
                        @method('PUT')
                        <input type="hidden" name="blog_id" value="{{ $comment->blog_id }}">
                        <input type="hidden" name="user_id" value="{{ $comment->user_id }}">
                        <input type="hidden" name="status" value="published">
                        <div class="form-group">
                            <div>
                                <input type="radio" name="status" id="status1" value="{{ \App\Enums\CommentStatus::PENDING }}" @checked(old('status', $comment->status) == \App\Enums\CommentStatus::PENDING)>
                                <label class="control-label" for="status1">Pending</label>
                            </div>
                            <div>
                                <input type="radio" name="status" id="status2" value="{{ \App\Enums\CommentStatus::APPROVED }}" @checked(old('status', $comment->status) == \App\Enums\CommentStatus::APPROVED)>
                                <label class="control-label" for="status2">Approved</label>
                            </div>
                            <div>
                                <input type="radio" name="status" id="status3" value="{{ \App\Enums\CommentStatus::PUBLISHED}}"  @checked(old('status', $comment->status) == \App\Enums\CommentStatus::PUBLISHED)>
                                <label class="control-label" for="status3">Published</label>
                            </div>
                        </div>
                    </div>
                    <div class="panel-footer">
                        in response to <a href="{{ route('admin.blogs.edit', $comment->blog->slug) }}">{{ $comment->blog->title }}</a>
                    </div>

                </div>

            </div>
        </div>
    </form>
@endsection


@push('script')
    <script type="text/javascript" charset="utf8" src="https://cdn.datatables.net/1.13.1/js/jquery.dataTables.js"></script>

    <script>
        $(document).ready(function() {
            $('#tags_table').DataTable();
        });
    </script>
@endpush
