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
                    <span class="text">Dashboard</span>
                </div>

                <div class="boxes">
                    <div class="box box1">
                        <i class="uil uil-user"></i>
                        <span class="text">Total Users</span>
                        <span class="number">{{ $userCount }}</span>
                    </div>
                    <div class="box box2">
                         <i class="uil uil-file-alt"></i>
                        <span class="text">Total Blogs | Total Videos</span>
                        <span class="number">{{ $postCount }} | {{ $videoCount }}</span>
                    </div>
                    <div class="box box3">
                        <i class="uil uil-video"></i>
                        <span class="text">Total Ads</span>
                        <span class="number">{{ $adsCount }}</span>
                    </div>
                </div>
            </div>
<!-- 
            <div class="activity">
                <div class="title">
                    <i class="uil uil-clock-three"></i>
                    <span class="text">Users</span>
                </div>

                <table style="width: 100%; border-collapse: collapse; text-align: left;">
                    <thead>
                        <tr>
                            <th style="border: 1px solid #ddd; padding: 8px; background-color: #f4f4f4;">Name</th>
                            <th style="border: 1px solid #ddd; padding: 8px; background-color: #f4f4f4;">Email</th>
                            <th style="border: 1px solid #ddd; padding: 8px; background-color: #f4f4f4;">Joined</th>
                            <th style="border: 1px solid #ddd; padding: 8px; background-color: #f4f4f4;">Type</th>
                            <th style="border: 1px solid #ddd; padding: 8px; background-color: #f4f4f4;">Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td style="border: 1px solid #ddd; padding: 8px;">Prem Shahi</td>
                            <td style="border: 1px solid #ddd; padding: 8px;">premshahi@gmail.com</td>
                            <td style="border: 1px solid #ddd; padding: 8px;">2022-02-12</td>
                            <td style="border: 1px solid #ddd; padding: 8px;">New</td>
                            <td style="border: 1px solid #ddd; padding: 8px;">Liked</td>
                        </tr>
                        <tr>
                            <td style="border: 1px solid #ddd; padding: 8px;">Deepa Chand</td>
                            <td style="border: 1px solid #ddd; padding: 8px;">deepachand@gmail.com</td>
                            <td style="border: 1px solid #ddd; padding: 8px;">2022-02-12</td>
                            <td style="border: 1px solid #ddd; padding: 8px;">Member</td>
                            <td style="border: 1px solid #ddd; padding: 8px;">Liked</td>
                        </tr>
                        <tr>
                            <td style="border: 1px solid #ddd; padding: 8px;">Manisha Chand</td>
                            <td style="border: 1px solid #ddd; padding: 8px;">prakashhai@gmail.com</td>
                            <td style="border: 1px solid #ddd; padding: 8px;">2022-02-13</td>
                            <td style="border: 1px solid #ddd; padding: 8px;">Member</td>
                            <td style="border: 1px solid #ddd; padding: 8px;">Liked</td>
                        </tr>
                        <tr>
                            <td style="border: 1px solid #ddd; padding: 8px;">Pratima Shahi</td>
                            <td style="border: 1px solid #ddd; padding: 8px;">manishachand@gmail.com</td>
                            <td style="border: 1px solid #ddd; padding: 8px;">2022-02-13</td>
                            <td style="border: 1px solid #ddd; padding: 8px;">New</td>
                            <td style="border: 1px solid #ddd; padding: 8px;">Liked</td>
                        </tr>
                        <tr>
                            <td style="border: 1px solid #ddd; padding: 8px;">Man Shahi</td>
                            <td style="border: 1px solid #ddd; padding: 8px;">pratimashhai@gmail.com</td>
                            <td style="border: 1px solid #ddd; padding: 8px;">2022-02-14</td>
                            <td style="border: 1px solid #ddd; padding: 8px;">Member</td>
                            <td style="border: 1px solid #ddd; padding: 8px;">Liked</td>
                        </tr>
                        <tr>
                            <td style="border: 1px solid #ddd; padding: 8px;">Ganesh Chand</td>
                            <td style="border: 1px solid #ddd; padding: 8px;">manshahi@gmail.com</td>
                            <td style="border: 1px solid #ddd; padding: 8px;">2022-02-14</td>
                            <td style="border: 1px solid #ddd; padding: 8px;">New</td>
                            <td style="border: 1px solid #ddd; padding: 8px;">Liked</td>
                        </tr>
                        <tr>
                            <td style="border: 1px solid #ddd; padding: 8px;">Bikash Chand</td>
                            <td style="border: 1px solid #ddd; padding: 8px;">ganeshchand@gmail.com</td>
                            <td style="border: 1px solid #ddd; padding: 8px;">2022-02-15</td>
                            <td style="border: 1px solid #ddd; padding: 8px;">Member</td>
                            <td style="border: 1px solid #ddd; padding: 8px;">Liked</td>
                        </tr>
                    </tbody>
                </table>
 -->
    </section>


    <script src="{{ asset('script.js') }}"></script>
</body>
</html>