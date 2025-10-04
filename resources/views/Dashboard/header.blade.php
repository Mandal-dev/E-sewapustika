<!DOCTYPE html>
<html lang="en">

<head>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
<link rel="stylesheet" href="{{ asset('css/table.css') }}">
<link rel="stylesheet" href="{{ asset('css/dashboard.css') }}">
<link rel="stylesheet" href="{{ asset('css/new_dashboard.css') }}">
<link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">

    <style>
        .profile-pic {
            width: 80px;
            height: 80px;
            border-radius: 50%;
            border: 2px solid #ddd;
            display: flex;
            /* Center the icon */
            align-items: center;
            justify-content: center;
            background: #f8f9fa;
            /* light background */
            color: #2c3e50;
            /* icon color */
            font-size: 40px;
            /* icon size */
        }
    </style>
</head>

<body>
    @php
        $designation = Session::get('user.designation_type');
    @endphp

    <!-- Sidebar -->
    <aside id="sidebar" class="sidebar">
    <div class="sidebar-header">
        <div class="dash-img">
            <a href="{{ route('police_profile.index', Session::get('user.id')) }}">
                <i class="fa-solid fa-user logo-icon"></i>
            </a>

            <div class="profile-pic">
                <img src="{{ asset('img/police image.png') }}" alt="{{ __('messages.profile_picture') }}">
            </div>

            <a href="{{ route('police_profile.index', Session::get('user.id')) }}">
                <i class="fa-solid fa-gear logo-icon"></i>
            </a>
        </div><br>

        <h1>{{ Session::get('user.name', __('messages.maharashtra')) }}</h1>
        <p>
            {{ Session::get('user.designation_type', __('messages.police')) }}
            &nbsp;
            {{ Session::get('user.district_name', __('messages.admin_system')) }}
        </p>
    </div>

    <nav class="nav">
        <!-- Dashboard -->
        <a href="{{ route('dashboard') }}" class="active dash-btn">
            <span class="db material-icons" style="color:white">grid_view</span> {{ __('messages.dashboard') }}
        </a>

        @php
            $designation = Session::get('user.designation_type');
        @endphp

        <!-- Manage Masters -->
        @if (in_array($designation, ['Admin', 'Head_Person']))
            <div class="nav-group">
                <div class="nav-group-header">
                    <i class="fas fa-users"></i>
                    <span>{{ __('messages.manage_masters') }}</span>
                    <i class="fas fa-chevron-down arrow"></i>
                </div>
                <div class="nav-submenu">
                    @if (in_array($designation, ['Admin']))
                        <a href="{{ route('districts.index') }}" class="nav-item submenu-item">
                            <i class="fas fa-map-marker-alt"></i> {{ __('messages.district_management') }}
                        </a>
                        <a href="{{ route('city.index') }}" class="nav-item submenu-item">
                            <i class="fas fa-city"></i> {{ __('messages.city_management') }}
                        </a>
                    @endif
                    <a href="{{ route('station.index') }}" class="nav-item submenu-item">
                        <i class="fas fa-building"></i> {{ __('messages.station_management') }}
                    </a>
                    <a href="{{ route('police.list.index') }}" class="nav-item submenu-item">
                        <i class="fas fa-user-shield"></i> {{ __('messages.police_user_management') }}
                    </a>
                </div>
            </div>
        @endif


            <div class="nav-group">
    <div class="nav-group-header">
        <i class="fas fa-clipboard-list"></i>
        <span>{{ __('messages.police_information') }}</span>
        <i class="fas fa-chevron-down arrow"></i>
    </div>
    <div class="nav-submenu">
        @if (in_array($designation, ['Admin', 'Head_Person', 'Police']))
            <a href="{{ route('sewa_pustika.index') }}" class="nav-item submenu-item">
                <i class="fas fa-book"></i> {{ __('messages.service_book') }}
            </a>
            <a href="{{ route('salary_increment.index') }}" class="nav-item submenu-item">
                <i class="fas fa-chart-line"></i> {{ __('messages.salary_increment') }}
            </a>
            <a href="{{ route('punishments.index') }}" class="nav-item submenu-item">
                <i class="fas fa-exclamation-triangle"></i> {{ __('messages.punishment') }}
            </a>
            <a href="{{ route('rewards.index') }}" class="nav-item submenu-item">
                <i class="fas fa-trophy"></i> {{ __('messages.reward') }}
            </a>
            <a href="{{ route('sewa_pustika.index') }}" class="nav-item submenu-item">
                <i class="fas fa-comment"></i> {{ __('messages.reward_status') }}
            </a>
        @endif

        @if ($designation === 'Rewards_Department')
            <a href="{{ route('rewards.index') }}" class="nav-item submenu-item">
                <i class="fas fa-trophy"></i> {{ __('messages.reward') }}
            </a>
        @endif

        @if ($designation === 'Sewapustika_Department')
            <a href="{{ route('sewa_pustika.index') }}" class="nav-item submenu-item">
                <i class="fas fa-book"></i> {{ __('messages.service_book') }}
            </a>
        @endif

        @if ($designation === 'Punishment_Department')
            <a href="{{ route('punishments.index') }}" class="nav-item submenu-item">
                <i class="fas fa-exclamation-triangle"></i> {{ __('messages.punishment') }}
            </a>
        @endif

        @if ($designation === 'Account_Department')
            <a href="{{ route('salary_increment.index') }}" class="nav-item submenu-item">
                <i class="fas fa-chart-line"></i> {{ __('messages.salary_increment') }}
            </a>
        @endif
    </div>
</div>
</nav>
</aside>


    <!-- Backdrop -->
    <div id="backdrop"></div>

    <!-- Main Content -->
    <div id="mainContent">
        <header>
            <button id="menuBtn"><span class="dashboard-text material-icons">menu</span>{{ __('messages.dashboard') }}
        </button></button>

            <!-- Right: Icons -->
            <div style="display: flex; align-items: center; padding-right: 48px; gap: 16px;">
                <!-- Language Selector -->
                <div class="lang-dropdown">
                    <div style="display:flex; align-items:center; gap:6px; position:relative;">
                        <img src="{{ asset('img/languageIcon.png') }}" alt="Language" class="lang-icon"
                            style="width:24px; height:24px;">

                        <form id="languageForm" method="POST" action="{{ route('set-language') }}">
                            @csrf
                            <select name="locale" onchange="this.form.submit()"
                                style="padding:4px 8px; border-radius:4px; border:1px solid #ccc; cursor:pointer;">
                                <option value="en" {{ app()->getLocale() == 'en' ? 'selected' : '' }}>English
                                </option>
                                <option value="mr" {{ app()->getLocale() == 'mr' ? 'selected' : '' }}>मराठी
                                </option>
                            </select>
                        </form>
                    </div>

                    <div class="lang-menu">
                        <div data-lang="mr" data-label="मराठी">मराठी</div>
                        <div data-lang="en" data-label="English">English</div>
                    </div>
                </div>

                <!-- Logout Icon -->
                <img src="{{ asset('img/logOutIcon.jpeg') }}" alt="Logout"
                    style="width:24px; height:24px; cursor:pointer;" onclick="confirmLogout(event)">

                <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
                    @csrf
                </form>
            </div>
        </header>

        <main>
            @yield('data')
        </main>
    </div>

    <!-- JS -->
    <script>
        (function() {
            // Check if already initialized
            if (window.sidebarInitialized) return;
            window.sidebarInitialized = true;

            const menuBtn = document.getElementById('menuBtn');
            const sidebar = document.getElementById('sidebar');
            const backdrop = document.getElementById('backdrop');
            const mainContent = document.getElementById('mainContent');

            if (!menuBtn || !sidebar || !backdrop || !mainContent) return;

            // Toggle Sidebar
            function toggleSidebar() {
                if (window.innerWidth >= 768) {
                    sidebar.classList.toggle('hidden-sidebar');
                    mainContent.style.marginLeft = sidebar.classList.contains('hidden-sidebar') ? '0' : '16rem';
                } else {
                    sidebar.classList.toggle('open');
                    backdrop.style.display = sidebar.classList.contains('open') ? 'block' : 'none';
                }
            }

            // Init Sidebar
            function initSidebar() {
                if (window.innerWidth >= 768) {
                    sidebar.classList.remove('hidden-sidebar');
                    sidebar.classList.add('open');
                    mainContent.style.marginLeft = '16rem';
                    backdrop.style.display = 'none';
                } else {
                    sidebar.classList.remove('open', 'hidden-sidebar');
                    mainContent.style.marginLeft = '0';
                    backdrop.style.display = 'none';
                }
            }

            menuBtn.addEventListener('click', toggleSidebar);
            backdrop.addEventListener('click', toggleSidebar);
            window.addEventListener('resize', initSidebar);
            document.addEventListener('DOMContentLoaded', initSidebar);

            // Submenu toggle
            document.querySelectorAll('.nav-group-header').forEach(header => {
                header.addEventListener('click', () => {
                    const submenu = header.nextElementSibling;
                    const arrow = header.querySelector('.arrow');

                    submenu.classList.toggle('open');
                    header.classList.toggle('open-header');

                    if (arrow) {
                        arrow.classList.toggle('fa-chevron-down');
                        arrow.classList.toggle('fa-chevron-up');
                    }

                    // Close other submenus
                    document.querySelectorAll('.nav-group-header').forEach(other => {
                        if (other !== header) {
                            other.classList.remove('open-header');
                            if (other.nextElementSibling) other.nextElementSibling.classList
                                .remove('open');
                            const otherArrow = other.querySelector('.arrow');
                            if (otherArrow) {
                                otherArrow.classList.remove('fa-chevron-up');
                                otherArrow.classList.add('fa-chevron-down');
                            }
                        }
                    });
                });
            });

            // Highlight active link
            document.querySelectorAll('.nav a, .submenu a, .nav-submenu a').forEach(link => {
                link.addEventListener('click', () => {
                    document.querySelectorAll('.submenu a, .nav-submenu a').forEach(l => l.classList
                        .remove('active'));
                    link.classList.add('active');
                });
            });

            // Language dropdown
            const langDropdown = document.querySelector('.lang-dropdown');
            const selectedLangSpan = document.querySelector('.selected-lang');
            const langMenu = document.querySelector('.lang-menu');

            if (langDropdown && langMenu && selectedLangSpan) {
                document.querySelector('.lang-toggle').addEventListener('click', () => {
                    langDropdown.classList.toggle('show');
                });

                langMenu.querySelectorAll('div').forEach(item => {
                    item.addEventListener('click', () => {
                        const langCode = item.dataset.lang;
                        const langLabel = item.dataset.label;

                        selectedLangSpan.textContent = langLabel;
                        langDropdown.classList.remove('show');

                        langMenu.querySelectorAll('div').forEach(div => {
                            div.style.display = div.dataset.lang === langCode ? 'none' :
                                'block';
                        });
                    });
                });

                // Initialize
                const current = selectedLangSpan.textContent.trim();
                langMenu.querySelectorAll('div').forEach(div => {
                    div.style.display = div.dataset.label === current ? 'none' : 'block';
                });

                window.addEventListener('click', e => {
                    if (!e.target.closest('.lang-dropdown')) {
                        langDropdown.classList.remove('show');
                    }
                });
            }

            // Logout confirmation
            window.confirmLogout = function(event) {
                event.preventDefault();
                Swal.fire({
                    title: 'Logout?',
                    text: "Are you sure you want to log out?",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#d33',
                    cancelButtonColor: '#3085d6',
                    confirmButtonText: 'Yes, logout'
                }).then((result) => {
                    if (result.isConfirmed) {
                        document.getElementById('logout-form').submit();
                    }
                });
            };
        })();
    </script>
</body>

</html>
