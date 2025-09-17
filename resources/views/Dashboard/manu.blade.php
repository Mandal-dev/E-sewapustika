<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Maharashtra Police Admin System</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/header.css') }}">


</head>

<body>
    <div class="app-container">
        <!-- Sidebar -->
        <div class="sidebar" id="sidebar">
            <!-- Sidebar Header -->
            <div class="sidebar-header">
                <div class="sidebar-logo">
                    <div class="logo-icon">
                        <i class="fas fa-shield-alt"></i>
                    </div>
                    <div class="logo-text">
                        <h1>महाराष्ट्र पोलिस</h1>
                        <p>Admin System</p>
                    </div>
                </div>
            </div>

            <!-- Navigation Menu -->
            <nav class="sidebar-nav">
                <button class="nav-item active" data-section="dashboard">
                    <i class="fas fa-tachometer-alt"></i>
                    <span>Dashboard</span>
                </button>

                <!-- First Submenu Group -->
                <div class="nav-group">
                    <div class="nav-group-header" onclick="toggleSubmenu(this)">
                        <i class="fas fa-users"></i>
                        <span>Manage Masters</span>
                        <i class="fas fa-chevron-down" style="margin-left: auto; transition: transform 0.3s;"></i>
                    </div>
                    <div class="nav-submenu">
                        <a href="{{ route('districts.index') }}" class="nav-item submenu-item" data-section="district">
                            <i class="fas fa-map-marker-alt"></i>
                            <span>जिल्हा व्यवस्थापन</span>
                        </a>

                        <a href="{{ route('city.index') }}" class="nav-item submenu-item" data-section="city">
                            <i class="fas fa-city"></i>
                            <span>शहर व्यवस्थापन</span>
                        </a>

                        <a href="{{ route('station.index') }}" class="nav-item submenu-item" data-section="station">
                            <i class="fas fa-building"></i>
                            <span>पोलीस ठाणे व्यवस्थापन</span>
                        </a>

                        <a href="{{ route('police.list.index') }}" class="nav-item submenu-item"
                            data-section="police-user">
                            <i class="fas fa-user-shield"></i>
                            <span>पोलीस वापरकर्ता व्यवस्थापन</span>
                        </a>
                    </div>

                </div>

                <!-- Second Submenu Group -->
                <div class="nav-group">
                    <div class="nav-group-header" onclick="toggleSubmenu(this)">
                        <i class="fas fa-clipboard-list"></i>
                        <span>Police Information</span>
                        <i class="fas fa-chevron-down" style="margin-left: auto; transition: transform 0.3s;"></i>
                    </div>
                    <div class="nav-submenu">
                        <a href="{{ route('sewa_pustika.index') }}" class="nav-item submenu-item"
                            data-section="sewa-pustika">
                            <i class="fas fa-book"></i>
                            <span>सेवा पुस्तिका</span>
                        </a>

                        <a href="{{ route('salary_increment.index') }}" class="nav-item submenu-item"
                            data-section="salary-increment">
                            <i class="fas fa-chart-line"></i>
                            <span>वेतनवाढ</span>
                        </a>

                        <a href="{{ route('punishments.index') }}" class="nav-item submenu-item"
                            data-section="punishment">
                            <i class="fas fa-exclamation-triangle"></i>
                            <span>शिक्षा</span>
                        </a>

                        <a href="{{ route('rewards.index') }}" class="nav-item submenu-item" data-section="salary">
                            <i class="fas fa-trophy"></i>
                            <span>बक्षीस</span>
                        </a>

                        <a href="{{ route('sewa_pustika.index') }}" class="nav-item submenu-item" data-section="leave">
                            <i class="fas fa-calendar"></i>
                            <span>रजा</span>
                        </a>

                        <a href="{{ route('sewa_pustika.index') }}" class="nav-item submenu-item"
                            data-section="feedback">
                            <i class="fas fa-comment"></i>
                            <span>समस्या व समाधान</span>
                        </a>
                    </div>

                </div>

            </nav>
        </div>

        <!-- Main Content -->
        <div class="main-content" id="mainContent">
            <header class="top-header">
                <div class="header-left">
                    <button class="sidebar-toggle" id="sidebarToggle">
                        <i class="fas fa-bars"></i>
                    </button>
                    <div class="breadcrumb">
                        <i class="fas fa-home"></i>
                        <i class="fas fa-chevron-right"></i>
                        <span id="breadcrumbText">Dashboard</span>
                    </div>
                </div>
                <div class="header-right">

                    <div class="user-avatar"> <i class="fas fa-sign-out-alt"></i></div>
                </div>
            </header>


            @yield('data')

        </div>
    </div>

    <!-- JavaScript -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Update time every second


            // Sidebar toggle functionality
            const sidebarToggle = document.getElementById('sidebarToggle');
            const sidebar = document.getElementById('sidebar');
            const mainContent = document.getElementById('mainContent');

            sidebarToggle.addEventListener('click', function() {
                if (window.innerWidth <= 768) {
                    sidebar.classList.toggle('open');
                } else {
                    sidebar.classList.toggle('collapsed');
                    mainContent.classList.toggle('expanded');
                }
            });

            // Active menu item functionality
            const navItems = document.querySelectorAll('.nav-item');
            navItems.forEach(item => {
                item.addEventListener('click', function() {
                    // Remove active class from all items
                    navItems.forEach(el => el.classList.remove('active'));

                    // Add active class to clicked item
                    this.classList.add('active');

                    // Update breadcrumb
                    const section = this.getAttribute('data-section');
                    if (section) {
                        const breadcrumbText = section.replace(/-/g, ' ')
                            .replace(/\b\w/g, l => l.toUpperCase());
                        document.getElementById('breadcrumbText').textContent = breadcrumbText;
                    }

                    // Close sidebar on mobile after selection
                    if (window.innerWidth <= 768) {
                        sidebar.classList.remove('open');
                    }
                });
            });

            // Handle window resize
            window.addEventListener('resize', function() {
                if (window.innerWidth > 768) {
                    sidebar.classList.remove('open');
                    if (sidebar.classList.contains('collapsed')) {
                        mainContent.classList.add('expanded');
                    } else {
                        mainContent.classList.remove('expanded');
                    }
                } else {
                    sidebar.classList.remove('collapsed');
                    mainContent.classList.remove('expanded');
                }
            });
        });

        // Toggle submenu function
        function toggleSubmenu(header) {
            const submenu = header.nextElementSibling;
            const chevron = header.querySelector('.fa-chevron-down');

            if (submenu && submenu.classList.contains('nav-submenu')) {
                submenu.classList.toggle('open');
                chevron.style.transform = submenu.classList.contains('open') ?
                    'rotate(180deg)' : 'rotate(0deg)';
            }
        }
    </script>
</body>

</html>
