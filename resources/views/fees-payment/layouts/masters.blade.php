<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $config->home_page_title ?? 'Online Fees Payment Portal' }}</title>

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.5/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-SgOJa3DmI69IUzQ2PVdRZhwQ+dy64/BUtbMJw1MZ8t5HZApcHrRKUc4W0kG879m7" crossorigin="anonymous">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">

    <link rel="stylesheet" href="{{asset('global/vendor/select2/select2.min.css')}}">

    @stack('styles')
    
    <style>
        :root {
            --primary-color: #2563eb;
            --primary-dark: #1d4ed8;
            --secondary-color: #f3f4f6;
            --accent-color: #f59e0b;
            --text-dark: #1f2937;
            --text-light: #6b7280;
            --success-color: #10b981;
            --danger-color: #ef4444;
            --border-radius: 10px;
            --box-shadow: 0 10px 25px rgba(0, 0, 0, 0.1);
        }
        
        body {
            font-family: 'Poppins', sans-serif;
            background: linear-gradient(135deg, #f5f7fa 0%, #e4e9f2 100%);
            color: var(--text-dark);
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            padding-top: 0;
            padding-bottom: 0;
        }
        
        .page-header {
            background: linear-gradient(135deg, var(--primary-color) 0%, var(--primary-dark) 100%);
            padding: 15px 0;
            margin-bottom: 50px;
            color: white;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
        }
        
        .header-logo {
            max-height: 90px;
            margin-bottom: 15px;
            filter: drop-shadow(0 4px 6px rgba(0, 0, 0, 0.1));
        }
        
        .form-container {
            max-width: 800px;
            margin: 0 auto 50px;
            padding: 40px;
            border-radius: var(--border-radius);
            box-shadow: var(--box-shadow);
            background-color: #fff;
            position: relative;
            overflow: hidden;
        }
        
        .form-container::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 5px;
            background: linear-gradient(90deg, var(--primary-color), var(--accent-color));
        }
        
        .form-title {
            font-weight: 600;
            color: var(--primary-color);
            margin-bottom: 30px;
            padding-bottom: 15px;
            border-bottom: 1px solid #e5e7eb;
        }
        
        .form-control, .form-select {
            border-radius: var(--border-radius);
            padding: 12px 15px;
            border: 1px solid #e5e7eb;
            transition: all 0.3s ease;
        }
        
        .form-control:focus, .form-select:focus {
            border-color: var(--primary-color);
            box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.2);
        }
        
        .form-label {
            font-weight: 500;
            margin-bottom: 8px;
            color: var(--text-dark);
        }
        
        .btn-primary {
            background-color: var(--primary-color);
            border-color: var(--primary-color);
            border-radius: var(--border-radius);
            padding: 12px 20px;
            font-weight: 500;
            transition: all 0.3s ease;
        }
        
        .btn-primary:hover, .btn-primary:focus {
            background-color: var(--primary-dark);
            border-color: var(--primary-dark);
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(37, 99, 235, 0.3);
        }
        
        .btn-secondary {
            background-color: #e5e7eb;
            border-color: #e5e7eb;
            color: var(--text-dark);
            border-radius: var(--border-radius);
            padding: 12px 20px;
            font-weight: 500;
            transition: all 0.3s ease;
        }
        
        .btn-secondary:hover {
            background-color: #d1d5db;
            border-color: #d1d5db;
            transform: translateY(-2px);
        }
        
        .alert {
            border-radius: var(--border-radius);
            border: none;
            padding: 15px;
            margin-bottom: 25px;
        }
        
        .alert-success {
            background-color: rgba(16, 185, 129, 0.1);
            color: var(--success-color);
        }
        
        .alert-danger {
            background-color: rgba(239, 68, 68, 0.1);
            color: var(--danger-color);
        }
        
        .form-text {
            color: var(--text-light);
            font-size: 0.875rem;
            margin-top: 5px;
        }
        
        .required-field::after {
            content: '*';
            color: var(--danger-color);
            margin-left: 4px;
        }
        
        .form-group {
            margin-bottom: 25px;
        }
        
        .form-section {
            background-color: #f9fafb;
            border-radius: var(--border-radius);
            padding: 20px;
            margin-bottom: 30px;
            border-left: 4px solid var(--primary-color);
        }
        
        .form-section-title {
            font-weight: 600;
            color: var(--primary-color);
            margin-bottom: 15px;
        }
        
        footer {
            background-color: #1f2937;
            color: #e5e7eb;
            padding: 30px 0;
            margin-top: auto;
        }
        
        .footer-links a {
            color: #e5e7eb;
            margin: 0 10px;
            text-decoration: none;
            transition: color 0.3s ease;
        }
        
        .footer-links a:hover {
            color: white;
        }
        
        .invalid-feedback {
            color: var(--danger-color);
            font-size: 0.875rem;
            margin-top: 5px;
        }
        
        /* Card styles for student info */
        .student-info-card {
            background-color: #f9fafb;
            border-radius: var(--border-radius);
            padding: 20px;
            margin-bottom: 30px;
            border-left: 4px solid var(--accent-color);
        }
        
        .student-info-title {
            font-weight: 600;
            color: var(--text-dark);
            margin-bottom: 15px;
            display: flex;
            align-items: center;
        }
        
        .student-info-title i {
            margin-right: 10px;
            color: var(--accent-color);
        }
        
        .info-item {
            display: flex;
            margin-bottom: 10px;
        }
        
        .info-label {
            font-weight: 500;
            width: 150px;
            color: var(--text-light);
        }
        
        .info-value {
            font-weight: 500;
            color: var(--text-dark);
        }
    </style>
</head>
<body>
    <div class="page-header text-center">
        <div class="container">
            <img src="{{asset('upload/sites/'.config('settings.site_logo'))}}" alt="Institution Logo" class="header-logo">
            <h3 class="fw-bold">{{$config->home_page_title ?? 'Online Fees Payment Portal'}}</h3>
            <p class="lead text-white-50">@yield('section-title', 'Complete your payment information securely')</p>
        </div>
    </div>

    <div class="container">
        <div class="row">
            <div class="col-md-6 m-auto">
                @if(session('success'))
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        <i class="fas fa-check-circle me-2"></i> {{ session('success') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                @endif
                @if(session('warning'))
                    <div class="alert alert-warning alert-dismissible fade show" role="alert">
                        <i class="fas fa-check-circle me-2"></i> {{ session('warning') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                @endif
                
                @if(session('error'))
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        <i class="fas fa-exclamation-circle me-2"></i> {{ session('error') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                @endif
            </div>
        </div>
        
        @yield('content')
    </div>
    
    <footer>
        <div class="container">
            <div class="row">
                <div class="col-md-6 text-center text-md-start">
                    <p>&copy; {{ date('Y') }} {{config('settings.college_name')}}. All rights reserved.</p>
                </div>
                <div class="col-md-6 text-center text-md-end">
                    <div class="footer-links">
                        <a href="#"><i class="fas fa-shield-alt me-1"></i> Privacy Policy</a>
                        <a href="#"><i class="fas fa-question-circle me-1"></i> Help</a>
                        <a href="mailto:support@rajit.net"><i class="fas fa-envelope me-1"></i> Contact</a>
                    </div>
                </div>
            </div>
        </div>
    </footer>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.5/dist/js/bootstrap.bundle.min.js" integrity="sha384-k6d4wzSIapyDyv1kpU366/PK5hCdSbCRGRCMv+eplOQJWyd1fbcAu9OCUj5zNLiq" crossorigin="anonymous"></script>

    <script src="{{asset('global/vendor/select2/select2.full.min.js')}}"></script>

    <script>
        $(document).ready(function() {
            $('.select2').select2({
                width: '100%',
                placeholder: true,
                allowClear: true
            });
        });
    </script>
    @stack('scripts')
</body>
</html>