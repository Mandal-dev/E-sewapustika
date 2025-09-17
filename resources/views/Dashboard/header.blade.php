<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Maharashtra Police Admin Dashboard</title>

    <!-- Google Icons -->
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
    <!-- FontAwesome for extra icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <link rel="stylesheet" href="{{ asset('css/new_dashboard.css') }}">
</head>

<body>

    <!-- Sidebar -->
    <aside id="sidebar" class="sidebar">
        <div class="sidebar-header">
            <h1>Maharashtra<br>Police</h1>
            <p>Admin System</p>
        </div>

        <nav class="nav">
            <!-- Dashboard -->
            <a href="#" class="active">
                <span class="db material-icons" style="color:white">grid_view</span> Dashboard
            </a>

            @php
                $designation = Session::get('user.designation_type');
            @endphp

            <!-- Manage Masters (Only Admin / Head_Person) -->
            @if (in_array($designation, ['Admin', 'Head_Person']))
                <div class="nav-group">
                    <div class="nav-group-header">
                        <i class="fas fa-users"></i>
                        <span>Manage Masters</span>
                        <i class="fas fa-chevron-down arrow"></i>
                    </div>
                    <div class="nav-submenu">
                        @if (in_array($designation, ['Admin']))
                            <a href="{{ route('districts.index') }}" class="nav-item submenu-item">
                                <i class="fas fa-map-marker-alt"></i> जिल्हा व्यवस्थापन
                            </a>
                            <a href="{{ route('city.index') }}" class="nav-item submenu-item">
                                <i class="fas fa-city"></i> शहर व्यवस्थापन
                            </a>
                        @endif
                        <a href="{{ route('station.index') }}" class="nav-item submenu-item">
                            <i class="fas fa-building"></i> पोलीस ठाणे व्यवस्थापन
                        </a>
                        <a href="{{ route('police.list.index') }}" class="nav-item submenu-item">
                            <i class="fas fa-user-shield"></i> पोलीस वापरकर्ता व्यवस्थापन
                        </a>
                    </div>
                </div>
            @endif

            <!-- Police Information -->
            <div class="nav-group">
                <div class="nav-group-header">
                    <i class="fas fa-clipboard-list"></i>
                    <span>Police Information</span>
                    <i class="fas fa-chevron-down arrow"></i>
                </div>
                <div class="nav-submenu">
                    <a href="{{ route('sewa_pustika.index') }}" class="nav-item submenu-item"><i
                            class="fas fa-book"></i> सेवा पुस्तिका</a>
                    <a href="{{ route('salary_increment.index') }}" class="nav-item submenu-item"><i
                            class="fas fa-chart-line"></i> वेतनवाढ</a>
                    <a href="{{ route('punishments.index') }}" class="nav-item submenu-item"><i
                            class="fas fa-exclamation-triangle"></i> शिक्षा</a>
                    <a href="{{ route('rewards.index') }}" class="nav-item submenu-item"><i class="fas fa-trophy"></i>
                        बक्षीस</a>

                    <a href="{{ route('sewa_pustika.index') }}" class="nav-item submenu-item"><i
                            class="fas fa-comment"></i> पुरस्काराची स्थिती</a>
                </div>
            </div>
        </nav>
    </aside>

    <!-- Backdrop -->
    <div id="backdrop"></div>

    <!-- Main Content -->
    <div id="mainContent">
        <header>
            <button id="menuBtn"><span class="dashboard-text material-icons">menu</span> Dashboard</button>



            <!-- Right: Icons -->
            <div style="display: flex; align-items: center; padding-right: 48px; gap: 16px;">
                <div class="lang-dropdown">
                    <div style="display:flex; align-items:center; gap:6px; cursor:pointer; position:relative;"
                        class="lang-toggle" onclick="toggleLanguageForm()">

                        <img src="{{ asset('img/languageIcon.png') }}" alt="Language" class="lang-icon"
                            style="width:24px; height:24px;">

                        <span class="selected-lang">
                            {{ app()->getLocale() == 'mr' ? 'मराठी' : 'English' }}
                        </span>

                        <!-- Hidden Language Form -->
                        <form id="languageForm" method="POST" action="{{ url('set-language') }}"
                            style="display:none; position:absolute; top:30px; right:0; background:#fff; padding:6px; border:1px solid #ccc; border-radius:6px; z-index:1000;">
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
                <!-- Logout -->

                <!-- Logout Icon -->
                <img src="{{ asset('img/logOutIcon.jpeg') }}" alt="Logout"
                    style="width:24px; height:24px; cursor:pointer;"
                    onclick="event.preventDefault(); document.getElementById('logout-form').submit();">

                <!-- Hidden Logout Form -->
                <form id="logout-form" action="{{ route('logout') }}" class="d-none">

                </form>

            </div>
        </header>

        <main style="padding:1rem;">


            @yield('data')
        </main>
    </div>

    <!-- JS -->
    <script>
        const menuBtn = document.getElementById('menuBtn');
        const sidebar = document.getElementById('sidebar');
        const backdrop = document.getElementById('backdrop');
        const mainContent = document.getElementById('mainContent');

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
                header.classList.toggle('open-header'); // 👈 use a new class

                // Toggle arrow direction
                if (arrow.classList.contains('fa-chevron-down')) {
                    arrow.classList.remove('fa-chevron-down');
                    arrow.classList.add('fa-chevron-up');
                } else {
                    arrow.classList.remove('fa-chevron-up');
                    arrow.classList.add('fa-chevron-down');
                }

                // Close other menus
                document.querySelectorAll('.nav-group-header').forEach(other => {
                    if (other !== header) {
                        other.classList.remove('open-header');
                        other.nextElementSibling.classList.remove('open');
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
                document.querySelectorAll(' .submenu a, .nav-submenu a').forEach(l => l.classList
                    .remove('active'));
                link.classList.add('active');
            });
        });


        const langDropdown = document.querySelector('.lang-dropdown');
        const selectedLangSpan = document.querySelector('.selected-lang');
        const langMenu = document.querySelector('.lang-menu');

        document.querySelector('.lang-toggle').addEventListener('click', () => {
            langDropdown.classList.toggle('show');
        });

        // Change language
        langMenu.querySelectorAll('div').forEach(item => {
            item.addEventListener('click', () => {
                const langCode = item.dataset.lang;
                const langLabel = item.dataset.label;

                // Update the selected text
                selectedLangSpan.textContent = langLabel;

                // Hide dropdown
                langDropdown.classList.remove('show');

                // Hide the selected language from dropdown, show the other
                langMenu.querySelectorAll('div').forEach(div => {
                    if (div.dataset.lang === langCode) {
                        div.style.display = 'none';
                    } else {
                        div.style.display = 'block';
                    }
                });

                console.log("Language changed to:", langCode);
            });
        });

        // Initialize so current language is hidden in menu
        (function initLang() {
            const current = selectedLangSpan.textContent.trim();
            langMenu.querySelectorAll('div').forEach(div => {
                if (div.dataset.label === current) {
                    div.style.display = 'none';
                } else {
                    div.style.display = 'block';
                }
            });
        })();

        // Close dropdown if clicked outside
        window.addEventListener('click', e => {
            if (!e.target.closest('.lang-dropdown')) {
                langDropdown.classList.remove('show');
            }
        });
    </script>
</body>

</html>
