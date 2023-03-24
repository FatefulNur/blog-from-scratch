@extends('layouts.admin.admin')
@section('title', 'Generals')

@section('heading', 'Add Generals')
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
            flex: 3;
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
                    Generals Settings
                </div>
                <div class="panel-body">

                    <div class="row">
                        <div class="col-md-7">
                            <form role="form" action="{{ route('admin.settings.general.update') }}" method="POST">
                                @include('partials.admin.sessions', [
                                    'session' => 'action'
                                ])
                                @method('PUT')
                                @csrf
                                <div class="form-group">
                                    <label class="control-label" for="site_name">Site Name</label>
                                    <input type="text" name="site_name" class="form-control" id="site_name"
                                        value="{{ old('site_name', $generals->site_name) }}">
                                </div>
                                <div class="form-group">
                                    <label class="control-label" for="tagline">Tagline</label>
                                    <input type="text" name="tagline" class="form-control" id="tagline"
                                        value="{{ old('tagline', $generals->tagline) }}">
                                </div>
                                <div class="form-group">
                                    <label class="control-label" for="membership">Membership</label>
                                    <div>
                                        <input type="checkbox" name="membership" class="" id="membership"
                                        @checked(old('membership', $generals->membership))>
                                        Can anyone register?
                                    </div>

                                </div>
                                <div class="form-group">
                                    <label class="control-label" for="default_role">Default User Role</label>
                                    <select class="form-control" name="default_role">
                                        <option value="1" @if (old('default_role', $generals->default_role) == 1) {{ 'selected' }} @endif>
                                            Admin</option>
                                        <option value="2" @if (old('default_role', $generals->default_role) == 2) {{ 'selected' }} @endif>
                                            User</option>

                                    </select>
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
