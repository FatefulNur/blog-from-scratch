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
                {{ $slot }}
            </div>
        </div>
    </div>
</x-home.template>
