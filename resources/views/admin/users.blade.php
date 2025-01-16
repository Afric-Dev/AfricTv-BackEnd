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
                    <span class="text">Users</span>
                </div>

                    <div class="row">
                    <div class="container mt-4">
                      <!-- Search Input -->
                      <div class="mb-4">
                        <input type="text" id="searchInput" class="form-control" placeholder="Search users by name, email, or unique ID">
                      </div>

                      <!-- User Table -->
                      <table class="table table-striped" id="userTable">

                        <thead class="thead-primary">
                          <tr>
                            <th>Name</th>
                            <th>Email</th>
                            <th>Unique ID</th>
                            <th>Action</th>
                          </tr>
                        </thead>
                        <tbody>
                          @foreach($users as $user)
                            <tr>
                              <td>{{ $user->name }}</td>
                              <td>{{ $user->email }}</td>
                              <td>{{ $user->unique_id }}</td>
                              <td><a href="{{ url('admin/user', $user->id) }}" class="btn btn-primary">View</a></td>
                            </tr>
                          @endforeach
                        </tbody>
                      </table>
                        <!-- Pagination Links -->
                     <div class="pagination-container">
                            {{ $users->links('pagination::bootstrap-4') }}
                          </div> 
                    </div>
            </div>

    </section>

<!-- Include Bootstrap JS and jQuery (for search functionality) -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>

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