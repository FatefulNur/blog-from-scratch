@php
    $home = (! request()->routeIs('admin.dashboard')) ? route('admin.dashboard') : "#";
@endphp
<div class="navbar-default sidebar" role="navigation">
    <div class="sidebar-nav navbar-collapse">

        <ul class="nav" id="side-menu">
            <li class="sidebar-search">
                <div class="input-group custom-search-form">
                    <input type="text" class="form-control" placeholder="Search...">
                    <span class="input-group-btn">
                        <button class="btn btn-primary" type="button">
                            <i class="fa fa-search"></i>
                        </button>
                    </span>
                </div>
            </li>
            <li>
                <a href="{{ $home }}" class="@if (request()->routeIs('admin.dashboard')) active @endif">
                    <i class="fa fa-dashboard fa-fw"></i> Dashboard</a>
            </li>
            <li>
                <a href="{{ route('admin.media.index') }}">
                    <i class="fa fa-video-camera"></i> Media</a>
            </li>
            <li>
                <a href="{{ route('admin.comments.index') }}">
                    <i class="fa fa-commenting-o"></i> Comments</a>
            </li>
            <li>
                <a href="#"><i class="fa fa-bookmark"></i> Blogs<span
                        class="fa arrow"></span></a>
                <ul class="nav nav-second-level">
                    <li>
                        <a href="{{ route('admin.blogs.index') }}">All Blogs</a>
                    </li>
                    <li>
                        <a href="{{ route('admin.blogs.create') }}">Add New Blog</a>
                    </li>
                    <li>
                        <a href="{{ route('admin.categories.create') }}">Categories</a>
                    </li>
                    <li>
                        <a href="{{ route('admin.tags.create') }}">Tags</a>
                    </li>
                </ul>
            </li>
            <li>
                <a href="#"><i class="fa fa-users"></i> Users<span
                        class="fa arrow"></span></a>
                <ul class="nav nav-second-level">
                    <li>
                        <a href="{{ route('admin.users.index') }}">All Users</a>
                    </li>
                    <li>
                        <a href="{{ route('admin.users.create') }}">Add New User</a>
                    </li>
                    {{-- <li>
                        <a href="#">Third Level <span class="fa arrow"></span></a>
                        <ul class="nav nav-third-level">
                            <li>
                                <a href="#">Third Level Item</a>
                            </li>
                        </ul>
                    </li> --}}
                </ul>
            </li>
            <li>
                <a href="#"><i class="fa fa-gears"></i> Settings<span
                        class="fa arrow"></span></a>
                <ul class="nav nav-second-level">
                    <li>
                        <a href="{{ route('admin.settings.general') }}">General</a>
                    </li>
                    <li>
                        <a href="{{ route('admin.settings.comment') }}">Comment</a>
                    </li>
                </ul>
            </li>
        </ul>

    </div>
</div>
