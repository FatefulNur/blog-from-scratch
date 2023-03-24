@if (session()->has($session))
    <div class="alert alert-success">
        <ul>
            <li>{{ session()->get($session) }}</li>
            @if (session()->has('image'))
                <li>{{ session()->get('image') }}</li>
            @endif
            @if (session()->has('gallery'))
                <li>{{ session()->get('gallery') }}</li>
            @endif
        </ul>
    </div>
@endif
