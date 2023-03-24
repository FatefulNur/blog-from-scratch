<!-- Footer Start -->
<x-widgets.area.footer>
    <div class="col-lg-3 col-md-6 mb-5">
        <a href="index.html" class="navbar-brand">
            <h1 class="mb-2 mt-n2 display-5 text-uppercase"><span class="text-primary">News</span>Room</h1>
        </a>
        <p>Volup amet magna clita tempor. Tempor sea eos vero ipsum. Lorem lorem sit sed elitr sed kasd et</p>
        <div class="d-flex justify-content-start mt-4">
            <a class="btn btn-outline-secondary text-center mr-2 px-0" style="width: 38px; height: 38px;" href="#"><i
                    class="fab fa-twitter"></i></a>
            <a class="btn btn-outline-secondary text-center mr-2 px-0" style="width: 38px; height: 38px;"
                href="#"><i class="fab fa-facebook-f"></i></a>
            <a class="btn btn-outline-secondary text-center mr-2 px-0" style="width: 38px; height: 38px;"
                href="#"><i class="fab fa-linkedin-in"></i></a>
            <a class="btn btn-outline-secondary text-center mr-2 px-0" style="width: 38px; height: 38px;"
                href="#"><i class="fab fa-instagram"></i></a>
            <a class="btn btn-outline-secondary text-center mr-2 px-0" style="width: 38px; height: 38px;"
                href="#"><i class="fab fa-youtube"></i></a>
        </div>
    </div>

    <x-widgets.categories>
        @foreach ($categories as $category)
            <a href="{{ route('category.category', $category->ancestorsToRoute()) }}" class="btn btn-sm btn-outline-secondary m-1">{{ $category->name }}</a>
        @endforeach
    </x-widgets.categories>

    <x-widgets.tags :heading="false" class="col-lg-3 col-md-6 mb-5">
        <h4 class="font-weight-bold mb-4">Tags</h4>
        @foreach ($tags as $tag)
            <a href="" class="btn btn-sm btn-outline-secondary m-1">{{ $tag->name }}</a>
        @endforeach
    </x-widgets.tags>

    <div class="col-lg-3 col-md-6 mb-5">
        <h4 class="font-weight-bold mb-4">Quick Links</h4>
        <div class="d-flex flex-column justify-content-start">
            <a class="text-secondary mb-2" href="#"><i class="fa fa-angle-right text-dark mr-2"></i>About</a>
            <a class="text-secondary mb-2" href="#"><i class="fa fa-angle-right text-dark mr-2"></i>Advertise</a>
            <a class="text-secondary mb-2" href="#"><i class="fa fa-angle-right text-dark mr-2"></i>Privacy &
                policy</a>
            <a class="text-secondary mb-2" href="#"><i class="fa fa-angle-right text-dark mr-2"></i>Terms &
                conditions</a>
            <a class="text-secondary" href="#"><i class="fa fa-angle-right text-dark mr-2"></i>Contact</a>
        </div>
    </div>
</x-widgets.area.footer>

<div class="container-fluid py-4 px-sm-3 px-md-5">
    <p class="m-0 text-center">
        &copy; <a class="font-weight-bold text-uppercase" href="#">{!! $siteName !!}</a>. All Rights Reserved.

        <!--/*** This template is free as long as you keep the footer author’s credit link/attribution link/backlink. If you'd like to use the template without the footer author’s credit link/attribution link/backlink, you can purchase the Credit Removal License from "https://htmlcodex.com/credit-removal". Thank you for your support. ***/-->
        Designed by <a class="font-weight-bold" href="https://htmlcodex.com">HTML Codex</a>
    </p>
</div>
<!-- Footer End -->


<!-- Back to Top -->
<a href="#" class="btn btn-dark back-to-top"><i class="fa fa-angle-up"></i></a>


<!-- JavaScript Libraries -->
<script src="https://code.jquery.com/jquery-3.4.1.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.4.1/js/bootstrap.bundle.min.js"></script>
<script src="{{ asset('user/js/easing.min.js') }}"></script>
<script src="{{ asset('user/js/owl.carousel.min.js') }}"></script>

<!-- Contact Javascript File -->
<script src="{{ asset('user/js/jqBootstrapValidation.min.js') }}"></script>
<script src="{{ asset('user/js/contact.js') }}"></script>

<!-- Template Javascript -->
<script src="{{ asset('user/js/main.js') }}"></script>
</body>

</html>
