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
                    <span class="text">Blogs</span>
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
                    
                    <div class="row">
                    <div class="container mt-4">
                      <!-- Search Input -->
                      <div class="mb-4">
                        <input type="text" id="searchInput" class="form-control" placeholder="Search users by title">
                      </div>

                      <!-- User Table -->
                      <table class="table table-striped" id="userTable">

                        <thead class="thead-primary">
                          <tr>
                            <th>Title</th>
                            <th>Posted By</th>
                            <th>Action</th>
                          </tr>
                        </thead>
                        <tbody>
                          @foreach($posts as $post)
                            <tr>
                              <td>{{ $post->post_title }}</td>
                              <td>{{ $post->user->name }}</td>
                             <td>
                                <a href="#" onclick="confirmStatusChange('{{ $post->id }}', '{{ $post->is_status }}')" class="btn btn-danger" id="status-btn-{{ $post->id }}">
                                {{ $post->is_status == 'ACTIVE' ? 'BAND POST' : 'UNBOUND POST' }}
                              </a>
                            </td>
                            </tr>
                          @endforeach
                        </tbody>
                      </table>
                        <!-- Pagination Links -->
                     <div class="pagination-container">
                            {{ $posts->links('pagination::bootstrap-4') }}
                          </div> 
                    </div>
            </div>

    </section>

<!-- Include Bootstrap JS and jQuery (for search functionality) -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
  // Function to confirm the status change and toggle the post status
  function confirmStatusChange(postId, currentStatus) {
    // Determine the confirmation message based on current status
    let actionText = currentStatus === 'ACTIVE' ? 'band' : 'unbound';
    let actionConfirmText = currentStatus === 'ACTIVE' ? 'Yes, band it!' : 'Yes, unbound it!';
    let actionMessage = currentStatus === 'ACTIVE' ? 'Do you really want to band this post?' : 'Do you really want to unbound this post?';

    // Show SweetAlert confirmation
    Swal.fire({
      title: 'Are you sure?',
      text: actionMessage,
      icon: 'warning',
      showCancelButton: true,
      confirmButtonColor: '#3085d6',
      cancelButtonColor: '#d33',
      confirmButtonText: actionConfirmText,
      cancelButtonText: 'No, cancel!',
    }).then((result) => {
      // If the user clicks "Yes", update the post status
      if (result.isConfirmed) {
        // Redirect to the route with the post ID to update the status
        window.location.href = '/admin/post/' + postId + '/update-status-id';
      }
    });
  }
</script>

<script>
  // Search functionality
  $('#searchInput').on('keyup', function() {
    var value = $(this).val().toLowerCase();
    $('#userTable tbody tr').filter(function() {
      $(this).toggle($(this).text().toLowerCase().indexOf(value) > -1);
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