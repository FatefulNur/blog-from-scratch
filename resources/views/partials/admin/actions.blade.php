@if (auth()->user()->isOwner($user->id))
    <span></span>
@else
    <form action="{{ route('admin.users.destroy', $user->id) }}" method="post"
        onsubmit="return confirm('Make sure wanna delete {{ $user->name }}')" style="display: inline">
        @csrf
        @method('DELETE')
        <button type="submit" class="text-danger fa fa-trash-o"></button>
    </form>
@endif
@if(!request()->routeIs('admin.users.edit'))
    <a href="{{ route('admin.users.edit', $user->id) }}">
        <button class="fa fa-edit"></button>
    </a>
@endif
