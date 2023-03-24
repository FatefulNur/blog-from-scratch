<x-home.template>

    @if ($latestBlogs)
        <x-home.top-slider>
            @foreach ($latestBlogs as $item)
                @php
                    $path = !is_null($item->image->path) ? $item->image->path : $item->defaultThumbnail();
                @endphp
                <div class="d-flex">
                    <img src="{{ asset($path) }}" style="width: 80px; height: 80px; object-fit: cover;">
                    <div class="d-flex align-items-center bg-light px-3 w-100" style="height: 80px;">
                        <a class="text-secondary font-weight-semi-bold" href="{{ route('single', ['category' => $item->category->ancestorsToRoute(), 'blog' => $item->slug]) }}">{{ $item->excerpt(30) }}</a>
                    </div>
                </div>
            @endforeach
        </x-home.top-slider>
    @endif

    @if ($oldestBlogs->count())
        <x-home.main-slider>
            @foreach ($oldestBlogs as $item)
                @php
                    $path = !is_null($item->image->path) ? $item->image->path : $item->defaultThumbnail();
                @endphp
                <div class="position-relative overflow-hidden" style="height: 435px;">
                    <img class="img-fluid h-100" src="{{ asset($path) }}" style="object-fit: cover;">
                    <div class="overlay">
                        <div class="mb-1">
                            <span class="text-white">Category</span>
                            <span class="px-2 text-white">/</span>
                            <a class="text-white" href="{{ route('category.category', $item->category->ancestorsToRoute()) }}">{{ $item->category->name }}</a>
                        </div>
                        <a class="h2 m-0 text-white font-weight-bold" href="{{ route('single', ['category' => $item->category->ancestorsToRoute(), 'blog' => $item->slug]) }}">{{ $item->title }}</a>
                    </div>
                </div>
            @endforeach

            <x-slot:all-category>
                @if ($categories->count())
                    <x-home.all-category>
                        @foreach ($categories as $category)
                            @if (is_null($category->parent_id))
                                <div class="position-relative overflow-hidden mb-3" style="height: 80px;">
                                    <img class="img-fluid w-100 h-100" src="{{ asset('uploads/default.svg') }}"
                                        style="object-fit: cover;">
                                    <a href="{{ route('category.category', $category->ancestorsToRoute()) }}"
                                        class="overlay align-items-center justify-content-center h4 m-0 text-white text-decoration-none">
                                        {{ $category->name }}
                                    </a>
                                </div>
                            @endif
                        @endforeach
                    </x-home.all-category>
                @endif
                </x-slot>
        </x-home.main-slider>
    @endif

    @if ($featured->count())
        <x-home.featured>
            @foreach ($featured as $item)
                @php
                    $path = !is_null($item->image->path) ? $item->image->path : $item->defaultThumbnail();
                @endphp
                <div class="position-relative overflow-hidden" style="height: 300px;">
                    <img class="img-fluid w-100 h-100" src="{{ asset($path) }}" style="object-fit: cover;">
                    <div class="overlay">
                        <div class="mb-1" style="font-size: 13px;">
                            <span class="text-white">Category</span>
                            <span class="px-1 text-white">/</span>
                            <a class="text-white" href="{{ route('category.category', $item->category->ancestorsToRoute()) }}">{{ $item->category->name }}</a>
                        </div>
                        <a class="h4 m-0 text-white" href="{{ route('single', ['category' => $item->category->ancestorsToRoute(), 'blog' => $item->slug]) }}">{{ $item->title }}</a>
                    </div>
                </div>
            @endforeach
        </x-home.featured>
    @endif


    <x-home.categories>
        @foreach ($postOfCategory as $category)
            @if ($category->posts)
                <div class="col-lg-6 py-3">
                    <div class="bg-light py-2 px-4 mb-3">
                        <h3 class="m-0">{{ $category->name }}</h3>
                    </div>
                    <div class="owl-carousel owl-carousel-3 carousel-item-2 position-relative">
                        @foreach ($category->posts as $post)
                            @php
                                $path = !is_null($post->image->path) ? $post->image->path : $post->defaultThumbnail();
                            @endphp
                            <div class="position-relative">
                                <img class="img-fluid w-100" src="{{ asset($path) }}"
                                    style="object-fit: cover; height: 150px;">
                                <div class="overlay position-relative bg-light">
                                    <div class="mb-2" style="font-size: 13px;">
                                        <span>Category</span>
                                        <span class="px-1">/</span>
                                        <a class="text-white" href="{{ route('category.category', $item->category->ancestorsToRoute()) }}">{{ $category->name }}</a>
                                    </div>
                                    <a class="h4 m-0" href="{{ route('single', ['category' => $category->ancestorsToRoute(), 'blog' => $post->slug]) }}">{{ $post->title }}</a>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif
        @endforeach
    </x-home.categories>

    <x-home.news>
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
                        <div class="w-100 d-flex flex-column justify-content-center bg-light px-3"
                            style="height: 100px;">
                            <div class="mb-1" style="font-size: 13px;">
                                <span>Category</span>
                                <span class="px-1">/</span>
                                <a href="{{ route('category.category', $item->category->ancestorsToRoute()) }}">{{ $item->category->name }}</a>
                            </div>
                            <a class="h6 m-0" href="{{ route('single', ['category' => $item->category->ancestorsToRoute(), 'blog' => $item->slug]) }}">{{ $item->excerpt(30) }}</a>
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
                    <a href="" class="btn btn-sm btn-outline-secondary m-1">{{ $tag->name }}</a>
                @empty
                    <div class="d-flex mb-3">
                        <div class="text-center text-muted">No Content Found</div>
                    </div>
                @endforelse
            </x-widgets.tags>
        </x-widgets.area.sidebar>
    </x-home.news>

</x-home.template>
