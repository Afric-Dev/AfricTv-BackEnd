    <nav>
        <div class="logo-name">
<!--             <div class="logo-image">
                <img src="images/logo.png" alt="">
            </div> -->

            <span class="logo_name">AfricTv Dashboard</span>
        </div>

        <div class="menu-items">
            <ul class="nav-links">
                <li><a href="{{ url('admin/dashboard') }}">
                    <i class="uil uil-estate"></i>
                    <span class="link-name">Dahsboard</span>
                </a></li>
                <li><a href="{{ url('admin/users') }}">
                   <i class="uil uil-user"></i>
                    <span class="link-name">Users</span>
                </a></li>
                <li><a href="{{ url('admin/posts') }}">
                    <i class="uil uil-file-alt"></i>
                    <span class="link-name">Blog Post</span>
                </a></li>
                <li><a href="{{ url('admin/videos') }}">
                    <i class="uil uil-video"></i>
                    <span class="link-name">Video Posts (Educational)</span>
                </a></li>
                <li><a href="{{ url('admin/ads') }}">
                    <i class="uil uil-comments"></i>
                    <span class="link-name">Ads</span>
                </a></li>
                <li><a href="{{ url('admin/newsletter') }}">
                    <i class="uil uil-comments"></i>
                    <span class="link-name">Send Mail</span>
                </a></li>
                  @if(auth()->user()->role === 'ADMIN' || auth()->user()->role === 'SUPER')
                    <li><a href="{{ url('admin\admins') }}">
                        <i class="uil uil-shield-check"></i>
                        <span class="link-name">Admins</span>
                    </a></li>
                @endif

            </ul>
            
            <ul class="logout-mode">
                <li><a href="#">
                    <i class="uil uil-signout"></i>
                      <form action="{{ url('admin/logout') }}" method="get">
                    @csrf   
                    <button style="background: none; border: none;" class="link-name">Logout</button>
                </form>
                </a></li>

                <li class="mode">
                    <a href="#">
                        <i class="uil uil-moon"></i>
                    <span class="link-name">Dark Mode</span>
                </a>

                <div class="mode-toggle">
                  <span class="switch"></span>
                </div>
            </li>
            </ul>
        </div>
    </nav>