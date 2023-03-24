@inject('breadcrumbs', App\Services\Breadcrumbs::class)
<x-home.template>
    <div class="container-fluid">
        <div class="container">
            <nav class="breadcrumb bg-white m-0 px-4 border border-info shadow-sm">
                {!! $breadcrumbs->render('Home') !!}
            </nav>
        </div>
    </div>

    <div class="container-fluid py-3">
        <div class="container">
            <div class="row">
                <div class="col-lg-8">
                    @forelse ($categories as $category)
                        @if ($category->depth == 0 && is_null($category->parent_id))
                            <div class="d-flex mb-3 shadow-sm">
                                <img src="{{ asset('uploads/default.svg') }}"
                                    style="width: 100px; height: 100px; object-fit: cover;">
                                <div class="w-100 d-flex flex-column justify-content-center bg-light px-3"
                                    style="height: 100px;">
                                    <div class="mb-1" style="font-size: 13px;">
                                        <span>Category</span>
                                        <span class="px-1">/</span>
                                        <a href="{{ route('category.category', $category->ancestorsToRoute()) }}">{{ $category->name }}</a>
                                    </div>
                                    <a class="h6 m-0" href="{{ $category->posts->count() ? route('single', ['category' => $category->ancestorsToRoute(), 'blog' => $category->posts->firstOrFail()->slug]) : "#" }}">{{ $category->posts()->firstOrNew()->title ?? "Total posts of the category {$category->posts()->count()}" }}</a>
                                </div>
                            </div>

                            @if ($category->children->count())
                                <div class="row justify-content-between">
                                    @foreach ($category->children as $child)
                                        <div class="col-6">
                                            <div class="d-flex mb-3 shadow-sm">
                                                <img src="{{ asset('uploads/default.svg') }}"
                                                    style="width: 100px; height: 100px; object-fit: cover;">
                                                <div class="w-100 d-flex flex-column justify-content-center bg-light px-3"
                                                    style="height: 100px;">
                                                    <div class="mb-1" style="font-size: 13px;">
                                                        <span>Category</span>
                                                        <span class="px-1">/</span>
                                                        <a href="{{ route('category.category', $child->ancestorsToRoute()) }}">{{ $child->name }}</a>
                                                    </div>
                                                    <a class="h6 m-0" href="{{ $child->posts->count() ? route('single', ['category' => $child->ancestorsToRoute(), 'blog' => $child->posts->firstOrFail()->slug]) : "#" }}">{{ $child->posts()->firstOrNew()->title ?? "Total posts of the category {$child->posts()->count()}" }}</a>
                                                </div>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            @endif
                        @endif

                    @empty
                        bye
                    @endforelse

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
                            <a href="#" class="btn btn-sm btn-outline-secondary m-1">{{ $tag->name }}</a>
                        @empty
                            <div class="d-flex mb-3">
                                <div class="text-center text-muted">No Content Found</div>
                            </div>
                        @endforelse
                    </x-widgets.tags>
                </x-widgets.area.sidebar>
            </div>
        </div>
    </div>
</x-home.template>
