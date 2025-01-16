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
                    <span class="text">Ads</span>
                </div>
                    <div style="text-align: center; padding: 5px;">
                        <a href="{{ url('admin/ads/refresh') }}">
                            <button type="submit" style="background-color: #007bff; color: white; padding: 12px 25px; border: none; border-radius: 5px; font-size: 16px; cursor: pointer; transition: background-color 0.3s;">
                                Refresh Ads
                            </button>
                        </a>
                        </div>
                    @if($errors->any())
                        @foreach ($errors->all() as $error)
                            <div style="padding: 0.75rem 1.25rem; margin-bottom: 1rem; border: 1px solid #f5c2c7; border-radius: 0.25rem; color: #842029; background-color: #f8d7da;" role="alert">
                                {{ $error }}
                            </div>
                        @endforeach
                    @endif

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
            <div class="row">
                <div class="col-md-12">
                    <div class="table-wrap">
                        <table class="table">
                        <thead class="thead-primary">
                          <tr>
                            <th>Title</th>
                            <th>Status</th>
                            <th>Clicks</th>
                            <th>Uploaded By</th>
                            <th>Action</th>
                          </tr>
                        </thead>
                        <tbody>
                        @foreach($ads as $ad)
                          <tr>
                            <th scope="row" class="scope border-bottom-0">{{ $ad->title }}</th>
                            <td class="border-bottom-0">{{ $ad->status }}</td>
                            <td class="border-bottom-0">{{ $ad->clicks }}</td>
                            <td class="border-bottom-0">{{ $ad->user->name }}</td>
                            <!-- 
                            <td class="border-bottom-0"><a href="{{ url('admin/admin', $ad->id) }}" class="btn btn-primary">View</a></td> -->
                          </tr>
                        @endforeach
                        </tbody>
                      </table>
                    </div>
                </div>
            </div>

    </section>


    <script src="{{ asset('script.js') }}"></script>
    <script src="{{ asset('table/js/jquery.min.js') }}"></script>
    <script src="{{ asset('table/js/popper.js') }}"></script>
    <script src="{{ asset('table/js/bootstrap.min.js') }}"></script>
    <script src="{{ asset('table/js/main.js') }}"></script>
</body>
</html>