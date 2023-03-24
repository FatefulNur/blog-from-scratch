@php
    // if its in blog page should return the actual value and if category page should return parent value
    $category = empty($blog) ? optional($category)->parent_id : $blog->category_id;
@endphp
@foreach ($childrens as $child)
    <option value="{{ $child->id }}" @selected(old('parent_id', $category) == $child->id)> @php echo $prefix; @endphp {{ $child->name }}</option>
    @if ($child->children->count())
        @include('partials.admin.category-options', [
            'childrens' => $child->children,
            'prefix' => str_repeat("-", $loop->depth),
        ])
    @endif
@endforeach
