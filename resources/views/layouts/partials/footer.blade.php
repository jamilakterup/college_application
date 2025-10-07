<!-- Footer -->
<footer class="bg-dark text-white py-5 mt-5">
    <div class="container">
        <div class="row">
            <!-- Logo and About -->
            <div class="col-md-4 mb-4 mb-md-0">
                <h5 class="text-uppercase mb-4">{{ config('app.name', 'Student Document Portal') }}</h5>
                <p>
                    Our portal provides easy access to official academic documents for students. 
                    Pay online and download your certificates, transcripts, and testimonials instantly.
                </p>
                <div class="mt-4">
                    <a href="#" class="text-white me-3"><i class="bi bi-facebook fs-5"></i></a>
                    <a href="#" class="text-white me-3"><i class="bi bi-twitter fs-5"></i></a>
                    <a href="#" class="text-white me-3"><i class="bi bi-instagram fs-5"></i></a>
                    <a href="#" class="text-white"><i class="bi bi-linkedin fs-5"></i></a>
                </div>
            </div>
            
            <!-- Quick Links -->
            <div class="col-md-4 mb-4 mb-md-0">
                <h5 class="text-uppercase mb-4">Quick Links</h5>
                <ul class="list-unstyled">
                    <li class="mb-2">
                        <a href="{{ url('home') }}" class="text-white text-decoration-none">
                            <i class="bi bi-chevron-right me-2"></i>Home
                        </a>
                    </li>
                    <li class="mb-2">
                        <a href="{{ url('about') }}" class="text-white text-decoration-none">
                            <i class="bi bi-chevron-right me-2"></i>About Us
                        </a>
                    </li>
                </ul>
            </div>
            
            <!-- Contact Info -->
            <div class="col-md-4">
                <h5 class="text-uppercase mb-4">Contact Us</h5>
                <ul class="list-unstyled">
                    <li class="mb-3">
                        <i class="bi bi-geo-alt-fill me-2"></i>
                        raj IT Solutions Ltd
                    </li>
                    <li class="mb-3">
                        <i class="bi bi-envelope-fill me-2"></i>
                        <a href="mailto:support@rajit.net" class="text-white text-decoration-none">
                            support@rajit.net
                        </a>
                    </li>
                    <li class="mb-3">
                        <i class="bi bi-telephone-fill me-2"></i>
                        <a href="tel:+8801762623193" class="text-white text-decoration-none">
                            +8801762623193
                        </a>
                    </li>
                    <li>
                        <i class="bi bi-clock-fill me-2"></i>
                        Saturday - Thursday: 9:00 AM - 5:00 PM
                    </li>
                </ul>
            </div>
        </div>
    </div>
</footer>

<!-- Copyright -->
<div class="bg-secondary text-white text-center py-3">
    <div class="container">
        <div class="row">
            <div class="col-md-6 text-md-start">
                © {{date('Y')}}. All RIGHT RESERVED.
            </div>
            <div class="col-md-6 text-md-end">
                <a href="{{ url('terms') }}" class="text-white text-decoration-none me-3">Terms of Service</a>
                <a href="{{ url('privacy') }}" class="text-white text-decoration-none">Privacy Policy</a>
            </div>
        </div>
    </div>
</div>