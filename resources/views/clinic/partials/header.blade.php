<header id="header" class="header fixed-top">

    <div class="topbar d-flex align-items-center dark-background">
        <div class="container d-flex justify-content-center justify-content-md-between">
            <div class="contact-info d-flex align-items-center">
                <i class="bi bi-envelope d-flex align-items-center"><a
                        href="mailto:{{ $site_email }}">{{ $site_email }}</a></i>
                <i class="bi bi-phone d-flex align-items-center ms-4"><span>{{ $site_phone }}</span></i>
            </div>
            <div class="social-links d-none d-md-flex align-items-center">
                @foreach (json_decode($site_social, true) as $social)
                    <a target="_blank" href="{{ $social['url'] }}" class="{{ $social['icon'] }}">
                        <i class="bi bi-{{ $social['icon'] }}"></i>
                    </a>
                @endforeach
            </div>
        </div>
    </div>

    <div class="branding d-flex align-items-cente">

        <div class="container position-relative d-flex align-items-center justify-content-between">
            <a href="{{ route('clinic.home') }}" class="logo d-flex align-items-center">
                @if ($site_logo)
                    <img src="{{ $site_logo }}" alt="Logo">
                @else
                    <h1 class="sitename">{{ config('app.name') }}</h1>
                @endif
            </a>

            <nav id="navmenu" class="navmenu">
                <ul>
                    <li><a href="{{ route('clinic.home') }}"
                            class="{{ request()->routeIs('clinic.home') ? 'active' : '' }}">Home</a>
                    </li>
                    <li><a href="{{ route('clinic.about') }}"
                            class="{{ request()->routeIs('clinic.about') ? 'active' : '' }}">About Us</a>
                    </li>
                    <li><a href="{{ route('clinic.services') }}"
                            class="{{ request()->routeIs('clinic.services') ? 'active' : '' }}">Services</a>
                    </li>
                    <li><a href="{{ route('clinic.contact') }}"
                            class="{{ request()->routeIs('clinic.contact') ? 'active' : '' }}">Contact Us</a>
                    </li>
                </ul>
                <i class="mobile-nav-toggle d-xl-none bi bi-list"></i>
            </nav>

        </div>

    </div>

</header>
