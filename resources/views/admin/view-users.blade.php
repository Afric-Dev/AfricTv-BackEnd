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

    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <title>Admin Dashboard</title> 
</head>
<body>
    <style>
        body {
            font-family: 'Arial', sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f4f4f9;
            color: #333;
        }
        .container {
            max-width: 700px;
            margin: 50px auto;
            background: #fff;
            border-radius: 12px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            padding: 30px;
        }
        .header {
            text-align: center;
            margin-bottom: 30px;
        }
        .header h1 {
            font-size: 1.8rem;
            margin: 0;
            color: #007bff;
        }
        .header p {
            font-size: 1rem;
            color: #555;
        }
        .avatar-container {
            text-align: center;
            margin-bottom: 20px;
        }
        .avatar {
            width: 120px;
            height: 120px;
            border-radius: 50%;
            object-fit: cover;
            border: 4px solid #007bff;
        }
        .details {
            display: grid;
            grid-template-columns: 1fr 2fr;
            row-gap: 15px;
            column-gap: 20px;
            font-size: 1rem;
        }
        .details div {
            display: flex;
            align-items: center;
        }
        .details span {
            font-weight: bold;
            color: #555;
        }
        .details .value {
            margin-left: 10px;
            color: #333;
        }
        .footer {
            text-align: center;
            margin-top: 30px;
        }
        .footer p {
            font-size: 0.9rem;
            color: #666;
        }
    .dropdown-btn {
            display: block;
            width: 100%;
            padding: 15px;
            font-size: 16px;
            color: #fff;
            background-color: #007bff;
            border: none;
            border-radius: 8px;
            cursor: pointer;
            text-align: center;
        }
        .dropdown-content {
            position: absolute;
            top: 30%;
            left: 0;
            width: 100%;
            background: #fff;
            border: 1px solid #ddd;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            border-radius: 8px;
            z-index: 10;
            display: none; /* Hidden by default */
        }
        .dropdown-content form {
            padding: 20px;
        }
        .form-group {
            margin-bottom: 15px;
        }
        .form-group label {
            display: block;
            font-weight: bold;
            margin-bottom: 5px;
        }
        .form-group input,
        .form-group textarea {
            width: 100%;
            padding: 10px;
            border: 1px solid #ccc;
            border-radius: 4px;
        }
        .form-group textarea {
            resize: vertical;
        }
        .form-group button {
            display: block;
            width: 100%;
            padding: 10px;
            font-size: 16px;
            color: #fff;
            background-color: #007bff;
            border: none;
            border-radius: 4px;
            cursor: pointer;
        }
        .form-group button:hover {
            background-color: #0056b3;
        }
    </style>
</style>
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
                    <span class="text">User Info</span>
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

    <div class="container">
        <div class="header">
            <h1>User Profile</h1>
            <p>Details of the selected user</p>
        </div>

        <div class="avatar-container">
            @if($user->avatar)
                <img src="{{ $user->avatar }}" alt="User Avatar" class="avatar">
            @else
                <img src="https://via.placeholder.com/120" alt="Default Avatar" class="avatar">
            @endif
        </div>

        <div class="details">
            <div>
                <span>Name:</span>
                <div class="value">{{ $user->name }}</div>
            </div>
            <div>
                <span>Unique ID:</span>
                <div class="value">{{ $user->unique_id }}</div>
            </div>
            <div>
                <span>Email:</span>
                <div class="value">{{ $user->email }}</div>
            </div>
            <div>
                <span>Phone Number:</span>
                <div class="value">{{ $user->phone_number ?? 'N/A' }}</div>
            </div>
            <div>
                <span>Subscription Status:</span>
                <div class="value">{{ $user->subscribtion_status }}</div>
            </div>
            <div>
                <span>Verification Status:</span>
                <div class="value">{{ $user->verification_status }}</div>
            </div>
            <div>
                <span>Subscribers:</span>
                <div class="value">{{ $user->subscribers_number }}</div>
            </div>
            <div>
                <span>Bio:</span>
                <div class="value">{{ $user->bio ?? 'N/A' }}</div>
            </div>
            <div>
                <span>Status:</span>
                <div class="value">{{ $user->status }}</div>
            </div>
            <div>
                <span>Link:</span>
                <div class="value">{{ $user->link ?? 'N/A' }}</div>
            </div>
        </div>
            <div style="gap: 10px; display: flex; justify-content: center; align-items: center;">
                 <button class="dropdown-btn">Edit ID</button>
                 <a href="#" onclick="confirmStatusChange('{{ url('admin/users/' . $user->id . '/update-status-id') }}')">
                 <button class="dropdown-btn">Edit Status</button>
                 </a>

                 <form id="verification-form" method="POST" action="{{ route('updateVerification', $user->id) }}" style="display:none;">
                    @csrf
                    <input type="hidden" name="status" id="status-input">
                </form>

                 <button class="dropdown-btn"  onclick="changeVerificationStatus()">Edit VERFICATION</button>
            </div>
          
                <div class="dropdown-content" style="display: none;">
                    <form action="{{ url('admin/users/' . $user->id . '/update-unique-id') }}" method="post">
                        @csrf
                        <div class="form-group">
                            <label for="unique_id">Unique ID</label>
                            <input type="text" id="unique_id" value="{{ $user->unique_id }}" name="unique_id" placeholder="Enter unique_id" required>
                        </div>
                        <div class="form-group">
                            <button type="submit">Submit</button>
                        </div>
                    </form>
                </div>
        <div class="footer">
            <p>&copy; 2025 AfricTv. All rights reserved.</p>
        </div>
    </div>
    </section>

<script>
    function changeVerificationStatus() {
        // Show SweetAlert prompt with options
        Swal.fire({
            title: 'Select Verification Status',
            input: 'radio',
            inputOptions: {
                'FREE': 'FREE',
                'MEDIUM': 'MEDIUM',
                'PREMIUM': 'PREMIUM',
            },
            inputValidator: (value) => {
                if (!value) {
                    return 'Please select a status';
                }
            },
            showCancelButton: true,
            confirmButtonText: 'Update Status',
            cancelButtonText: 'Cancel',
        }).then((result) => {
            if (result.isConfirmed) {
                // Get selected status
                const status = result.value;

                // Set status in the hidden form input
                document.getElementById('status-input').value = status;

                // Submit the form to update the status
                document.getElementById('verification-form').submit();

                // Show success message
                Swal.fire({
                    title: 'Status Updated!',
                    text: `User status has been changed to ${status}.`,
                    icon: 'success',
                });
            }
        });
    }
</script>

<script>
    function confirmStatusChange(url) {
        // Show SweetAlert confirmation dialog
        Swal.fire({
            title: 'Are you sure?',
            text: 'Do you want to change the status of this user?',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: 'Yes, change it!',
            cancelButtonText: 'No, keep it',
        }).then((result) => {
            if (result.isConfirmed) {
                // If confirmed, navigate to the URL
                window.location.href = url;
            }
        });
    }
</script>
    <script>
        // JavaScript to handle the dropdown functionality
        document.addEventListener('DOMContentLoaded', () => {
            const dropdownBtn = document.querySelector('.dropdown-btn');
            const dropdownContent = document.querySelector('.dropdown-content');

            dropdownBtn.addEventListener('click', () => {
                // Toggle inline style for display property
                if (dropdownContent.style.display === 'none' || dropdownContent.style.display === '') {
                    dropdownContent.style.display = 'block';
                } else {
                    dropdownContent.style.display = 'none';
                }
            });

            // Optional: Close the dropdown if clicked outside
            document.addEventListener('click', (event) => {
                if (!dropdownBtn.contains(event.target) && !dropdownContent.contains(event.target)) {
                    dropdownContent.style.display = 'none';
                }
            });
        });
    </script>

    <script src="{{ asset('script.js') }}"></script>
    <script src="{{ asset('table/js/jquery.min.js') }}"></script>
    <script src="{{ asset('table/js/popper.js') }}"></script>
    <script src="{{ asset('table/js/bootstrap.min.js') }}"></script>
    <script src="{{ asset('table/js/main.js') }}"></script>
</body>
</html>