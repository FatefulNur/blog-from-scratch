@extends('layouts.admin.admin')
@section('title', 'Comment Settings')

@section('heading', 'Comment Settings')
@push('style')
    <style>
        .form-group {
            margin-bottom: 15px;
            display: flex;
            flex-wrap: nowrap;
        }

        .form-group :first-child {
            flex: 2;
        }

        .form-group :nth-child(2) {
            flex: 2;
        }

        label[for="membership"]+input {
            max-width: 100%;
            margin-right: calc(50% - 40px);
        }
    </style>
@endpush

@section('body')

    <div class="row">
        <div class="col-lg-12">
            <div class="panel panel-default">
                <div class="panel-heading">
                    Comment Settings
                </div>
                <div class="panel-body">

                    <div class="row">
                        <div class="col-md-7">
                            <form role="form" action="{{ route('admin.settings.comment.update') }}" method="POST">
                                @include('partials.admin.sessions', [
                                    'session' => 'action'
                                ])
                                @method('PUT')
                                @csrf

                                <div class="form-group">
                                    <label class="control-label" for="allow_comment">Allow Comments</label>
                                    <div>
                                        <input type="checkbox" name="allow_comment" class="" id="allow_comment"
                                        @checked(old('allow_comment', $comment->allow_comment))>
                                        Allow users to comment in post
                                    </div>

                                </div>
                                <div class="form-group">
                                    <label class="control-label" for="nested_comment">Nested Comment</label>
                                    <div>
                                        <input type="checkbox" name="nested_comment" class="" id="nested_comment"
                                        @checked(old('nested_comment', $comment->nested_comment))>
                                        Can a comment be on multi level?
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="control-label" for="max_depth_comment">Maximum Depth</label>
                                    <select class="form-control" name="max_depth_comment">
                                        <option value="1" @if (old('max_depth_comment', $comment->max_depth_comment) == 1) {{ 'selected' }} @endif>
                                            1</option>
                                        <option value="2" @if (old('max_depth_comment', $comment->max_depth_comment) == 2) {{ 'selected' }} @endif>
                                            2</option>
                                        <option value="3" @if (old('max_depth_comment', $comment->max_depth_comment) == 3) {{ 'selected' }} @endif>
                                            3</option>
                                        <option value="4" @if (old('max_depth_comment', $comment->max_depth_comment) == 4) {{ 'selected' }} @endif>
                                            4</option>
                                        <option value="5" @if (old('max_depth_comment', $comment->max_depth_comment) == 5) {{ 'selected' }} @endif>
                                            5</option>
                                        <option value="6" @if (old('max_depth_comment', $comment->max_depth_comment) == 6) {{ 'selected' }} @endif>
                                            6</option>
                                    </select>
                                </div>
                                <div class="form-group">
                                    <label class="control-label" for="comment_permission">Allow Admin Permission</label>
                                    <div>
                                        <input type="checkbox" name="comment_permission" class="" id="comment_permission"
                                        @checked(old('comment_permission', $comment->comment_permission))>
                                        Comments need for admin approval?
                                    </div>
                                </div>

                                <button type="submit" class="btn btn-primary">Save Changes</button>
                                <button type="reset" class="btn btn-success">Reset</button>
                            </form>
                        </div>
                    </div>

                </div>

            </div>

        </div>

    </div>
@endsection
