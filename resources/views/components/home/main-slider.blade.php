 <!-- Main News Slider Start -->
 <div class="container-fluid py-3">
    <div class="container">
        <div class="row">
            <div class="col-lg-8">
                <div class="owl-carousel owl-carousel-2 carousel-item-1 position-relative mb-3 mb-lg-0">
                    {{ $slot }}
                </div>
            </div>

            <div class="col-lg-4">
                {{ $allCategory }}
            </div>
        </div>
    </div>
</div>
<!-- Main News Slider End -->
