<x-archive>

    <div class="col-lg-8">
        <!-- News Detail Start -->
        <div class="position-relative mb-3">
            <img class="img-fluid w-100" src="{{ asset($blog->defaultThumbnail()) }}" style="object-fit: cover;">
            <div class="overlay position-relative bg-light">
                <div class="mb-3">
                    <a href="{{ route('category.category', $blog->category->ancestorsToRoute()) }}">{{ $blog->category->name }}</a>
                    <span class="px-1">/</span>
                    <span>{{ $blog->created_at->diffForHumans() }}</span>
                </div>
                <div>
                    <h1>{{ $blog->title }}</h1>
                    {{ $blog->description }}
                </div>
            </div>
        </div>
        <!-- News Detail End -->
    </div>

    <x-widgets.area.sidebar>

        <x-widgets.socials />

        <x-widgets.newsletter />

        <x-widgets.posts>
            @forelse ($latestBlogs as $item)
                @break($loop->index + 1 > 4)
                @php
                    $path = !is_null($item['image']['path']) ? $item['image']['path'] : $item->defaultThumbnail();
                @endphp

                <div class="d-flex mb-3">
                    <img src="{{ asset($path) }}" style="width: 100px; height: 100px; object-fit: cover;">
                    <div class="w-100 d-flex flex-column justify-content-center bg-light px-3" style="height: 100px;">
                        <div class="mb-1" style="font-size: 13px;">
                            <span>Category</span>
                            <span class="px-1">/</span>
                            <a
                                href="{{ route('category.category', $item->category->ancestorsToRoute()) }}">{{ $item->category->name }}</a>
                        </div>
                        <a class="h6 m-0"
                            href="{{ route('single', ['category' => $item->category->ancestorsToRoute(), 'blog' => $item->slug]) }}">{{ $item->excerpt(30) }}</a>
                    </div>
                </div>
            @empty
                <div class="d-flex mb-3">
                    <div class="text-center text-muted">No Content Found</div>
                </div>
            @endforelse
        </x-widgets.posts>

        <x-widgets.tags class="pb-3">
            @forelse ($tags as $tag)
                <a href="#" class="btn btn-sm btn-outline-secondary m-1">{{ $tag->name }}</a>
            @empty
                <div class="d-flex mb-3">
                    <div class="text-center text-muted">No Content Found</div>
                </div>
            @endforelse
        </x-widgets.tags>
    </x-widgets.area.sidebar>
</x-archive>
