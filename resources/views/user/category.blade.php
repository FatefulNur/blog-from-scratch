<x-archive>
    <div class="col-lg-8">
        @if ($category->children->count())
            <div class="row">
                <div class="col-12">
                    <div class="d-flex align-items-center justify-content-between bg-light py-2 px-4 mb-3">
                        <h3 class="m-0">Other Categories</h3>
                    </div>
                </div>
            </div>

            <div class="row mb-3">
                @foreach ($category->children as $child)
                    <div class="col-6">
                        <div class="d-flex mb-3">
                            <img src="{{ asset('uploads/default.svg') }}"
                                style="width: 100px; height: 100px; object-fit: cover;">
                            <div class="w-100 d-flex flex-column justify-content-center bg-light px-3"
                                style="height: 100px;">
                                <div class="mb-1" style="font-size: 13px;">
                                    <span>{{ $category->name }}</span>
                                    <span class="px-1">/</span>
                                    <span>{{ $child->name }}</span>
                                </div>
                                <a class="h6 m-0" href="{{ $child->posts->count() ? route('single', ['category' => $child->ancestorsToRoute(), 'blog' => $child->posts->firstOrFail()->slug]) : "#" }}">{{ $child->posts()->firstOrNew()->title ?? "Total posts of the category {$category->posts()->count()}" }}</a>
                            </div>
                        </div>
                    </div>
                @endforeach

            </div>
        @endif

        <div class="row">
            @forelse ($category->posts()->paginate($postsTotal) as $item)
                @php
                    $path = !is_null($item->image->path) ? $item->image->path : $item->defaultThumbnail();
                @endphp
                <div class="col-lg-4 col-md-6 col-sm-12">
                    <div class="position-relative mb-3">
                        <img class="img-fluid w-100"
                            src="{{ asset($path) }}"
                            style="object-fit: cover;height:150px">
                        <div class="overlay position-relative bg-light p-3">
                            <div class="mb-2" style="font-size: 14px;">
                                <span>Category</span>
                                <span class="px-1">/</span>
                                <span>{{ $category->name }}</span>
                            </div>
                            <a class="h6" href="{{ route('single', ['category' => $category->ancestorsToRoute(), 'blog' => $item->slug]) }}">{{ Str::of($item->title)->limit(15) }}</a>
                            <p class="m-0">{{ $item->excerpt(20) }}</p>
                        </div>
                    </div>
                </div>
            @empty
                <div class="col-12">
                    <div class="d-flex align-items-center justify-content-center bg-warning p-4 mb-3">
                        <div class="h-20 text-white">No Content Available</div>
                    </div>
                </div>
            @endforelse

        </div>
        {{ $category->posts()->paginate($postsTotal)->links('vendor.pagination') }}

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
</x-archive>
