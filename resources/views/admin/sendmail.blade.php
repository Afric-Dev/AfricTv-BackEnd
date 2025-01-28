<!DOCTYPE html>
<!--=== Coding by CodingLab | www.codinglabweb.com === -->
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    
    <!----======== CSS ======== -->
    <link rel="stylesheet" href="{{ asset('style.css') }}">

     
    <!----===== Iconscout CSS ===== -->
    <link rel="stylesheet" href="https://unicons.iconscout.com/release/v4.0.0/css/line.css">
   <script src="https://cdn.ckeditor.com/4.16.2/standard/ckeditor.js"></script>
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
                <div class="title">
                    <i class="uil uil-tachometer-fast-alt"></i>
                    <span class="text">Send Mail</span>
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


                        
                <form action="{{ route('posts.send-mail') }}" method="POST" enctype="multipart/form-data" style="max-width: 70rem; margin: 1rem auto; background-color: white; box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1); border-radius: 0.375rem; padding: 1rem;">
                    @csrf

                    <!-- Subject Field -->
                    <div style="margin-bottom: 1rem;">
                        <label for="subject" style="display: block; font-weight: 600; color: #2d3748;">Subject</label>
                        <input type="text" style="width: 100%; margin-top: 0.25rem; padding: 0.5rem 1rem; border: 1px solid #e2e8f0; border-radius: 0.375rem; outline: none; transition: all 0.2s ease; focus:ring: 2px solid #5a67d8;" id="subject" name="subject" placeholder="Enter subject" required>
                    </div>

                    <!-- Content Field -->
                    <div style="margin-bottom: 1rem;">
                        <label for="content" style="display: block; font-weight: 600; color: #2d3748;">Content</label>
                        <textarea style="width: 100%; margin-top: 0.25rem; padding: 0.5rem 1rem; border: 1px solid #e2e8f0; border-radius: 0.375rem; outline: none; transition: all 0.2s ease; focus:ring: 2px solid #5a67d8;" id="content" name="content" rows="8" placeholder="Write your content here..." required></textarea>
                    </div>

                    <div style="text-align: right; margin-top: 1rem;">
                        <button type="submit" style="padding: 0.75rem 1.5rem; background-color: #dd6b20; color: white; font-size: 1.125rem; font-weight: 600; border-radius: 0.375rem; cursor: pointer; transition: all 0.2s ease; border: none; outline: none;">
                            Send
                        </button>
                    </div>
                </form>


        </div>

            </div>

    </section>
    <script src="https://cdn.ckeditor.com/4.16.2/standard/ckeditor.js"></script>
    <script>
        // Initialize CKEditor for the textarea
        CKEDITOR.replace('content');
    </script>

    <script src="{{ asset('script.js') }}"></script>
</body>
</html>