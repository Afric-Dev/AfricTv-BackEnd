<!DOCTYPE html>
<!--=== Coding by CodingLab | www.codinglabweb.com === -->
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    
    <!----======== CSS ======== -->
    <link rel="stylesheet" href="{{ asset('style.css') }}">
    <link rel="stylesheet" href="{{ asset('table/css/style.css') }}">
     
    <!----===== Iconscout CSS ===== -->
    <link rel="stylesheet" href="https://unicons.iconscout.com/release/v4.0.0/css/line.css">

    <title>Admin Dashboard</title> 
</head>
<body>

    @include('components.nav')
    <section class="dashboard">
        <div class="top">
            <i class="uil uil-bars sidebar-toggle"></i>

            <div class="search-box">
                <i class="uil uil-search"></i>
                <input type="text" placeholder="Search here...">
            </div>
            
            <img src="images/profile.jpg" alt="">
        </div>

        <div class="dash-content">
            <div class="overview">
            <div class="activity">
                <div class="title">
                    <i class="uil uil-clock-three"></i>
                    <span class="text">Add Admin</span>
                </div>
                    @if (session('error'))
                        <div style="padding: 0.75rem 1.25rem; margin-bottom: 1rem; border: 1px solid #f5c2c7; border-radius: 0.25rem; color: #842029; background-color: #f8d7da;" role="alert">
                            {{ session('error') }}
                        </div>
                    @endif

                    @if (session('message'))
                        <div style="padding: 0.75rem 1.25rem; margin-bottom: 1rem; border: 1px solid #badbcc; border-radius: 0.25rem; color: #0f5132; background-color: #d1e7dd;" role="alert">
                            {{ session('message') }}
                        </div>
                    @endif

                    <!-- Validation Errors -->
                    @if ($errors->any())
                        <div style="padding: 0.75rem 1.25rem; margin-bottom: 1rem; border: 1px solid #f5c2c7; border-radius: 0.25rem; color: #842029; background-color: #f8d7da;" role="alert">
                            <ul style="margin: 0; padding-left: 1.5rem;">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif
                <div class="">

                  <form action="{{ route('admin-add') }}" method="POST" style="max-width: 600px; margin: 0 auto; padding: 30px; border: 1px solid #ddd; border-radius: 10px; background-color: #f9f9f9;">
                        @csrf

                        <h2 style="text-align: center; margin-bottom: 20px; font-size: 24px; font-weight: bold; color: #333;">Add Admin</h2>
                        
                        <!-- Name Field -->
                        <div style="margin-bottom: 20px;">
                            <label for="name" style="display: block; font-weight: bold; margin-bottom: 8px; font-size: 16px; color: #555;">Name</label>
                            <input type="text" id="name" name="name" style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 5px; font-size: 14px; color: #333; background-color: #fff;" required>
                        </div>

                        <!-- Mobile Field -->
                        <div style="margin-bottom: 20px;">
                            <label for="mobile" style="display: block; font-weight: bold; margin-bottom: 8px; font-size: 16px; color: #555;">Mobile</label>
                            <input type="number" id="mobile" name="mobile" style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 5px; font-size: 14px; color: #333; background-color: #fff;" required>
                        </div>

                       <!-- Email Field -->
                        <div style="margin-bottom: 20px;">
                            <label for="email" style="display: block; font-weight: bold; margin-bottom: 8px; font-size: 16px; color: #555;">Email Address</label>
                            <input type="email" id="email" name="email" style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 5px; font-size: 14px; color: #333; background-color: #fff;" required>
                        </div>


                        <!-- Role Field -->
                        <div style="margin-bottom: 20px;">
                            <label for="role" style="display: block; font-weight: bold; margin-bottom: 8px; font-size: 16px; color: #555;">Role</label>
                            <select id="role" name="role" style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 5px; font-size: 14px; color: #333; background-color: #fff;" required>
                                <option value="SUPER">Super</option>
                                <option value="ADMIN">Admin</option>
                                <option value="SUPPORT">Support</option>
                                <option value="ACCOUNT">Account</option>
                                <option value="MANAGER">Manager</option>
                            </select>
                        </div>

                        <!-- Password Field -->
                        <div style="margin-bottom: 20px;">
                            <label for="password" style="display: block; font-weight: bold; margin-bottom: 8px; font-size: 16px; color: #555;">Password</label>
                            <input type="password" id="password" name="password" style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 5px; font-size: 14px; color: #333; background-color: #fff;" required>
                        </div>

                        <!-- Submit Button -->
                        <div style="text-align: center;">
                            <button type="submit" style="background-color: #007bff; color: white; padding: 12px 25px; border: none; border-radius: 5px; font-size: 16px; cursor: pointer; transition: background-color 0.3s;">
                                Save Admin Info
                            </button>
                        </div>
                    </form>

            </div>

    </section>


    <script src="{{ asset('script.js') }}"></script>
    <script src="{{ asset('table/js/jquery.min.js') }}"></script>
    <script src="{{ asset('table/js/popper.js') }}"></script>
    <script src="{{ asset('table/js/bootstrap.min.js') }}"></script>
    <script src="{{ asset('table/js/main.js') }}"></script>
</body>
</html>