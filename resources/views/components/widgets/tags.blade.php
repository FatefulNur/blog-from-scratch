@props(['heading' => true])
<!-- Tags Start -->
<div {{ $attributes }}>
    <div class="bg-light py-2 px-4 mb-3 {{ $heading ? '' : 'd-none' }}">
        <h3 class="m-0">Tags</h3>
    </div>
    {{ $slot }}
</div>
<!-- Tags End -->
