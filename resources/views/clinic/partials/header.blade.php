@php
  $navLinks = json_decode($clinic_nav_links, true) ?? [];
  $socialLinks = json_decode($clinic_topbar_social, true) ?? [];
  $logo = $clinic_logo ?? null;
  $logoAlt = $clinic_logo_alt ?? ($clinic_site_name ?? '');
@endphp

<header id="header" class="header fixed-top">

  <div class="topbar d-flex align-items-center dark-background">
    <div class="container d-flex justify-content-center justify-content-md-between">
      <div class="contact-info d-flex align-items-center">
        <i class="bi bi-envelope d-flex align-items-center"><a
            href="mailto:{{ $clinic_topbar_email ?? '' }}">{{ $clinic_topbar_email ?? '' }}</a></i>
        <i class="bi bi-phone d-flex align-items-center ms-4"><span>{{ $clinic_topbar_phone ?? '' }}</span></i>
      </div>
      <div class="social-links d-none d-md-flex align-items-center">
        @foreach ($socialLinks as $social)
          <a href="{{ $social['url'] ?? '#' }}" class="{{ $social['icon'] ?? '' }}"><i class="bi bi-{{ $social['icon'] ?? '' }}"></i></a>
        @endforeach
      </div>
    </div>
  </div>

  <div class="branding d-flex align-items-cente">

    <div class="container position-relative d-flex align-items-center justify-content-between">
      <a href="{{ route('clinic.home') }}" class="logo d-flex align-items-center">
        @if ($logo)
          <img src="{{ $logo }}" alt="{{ $logoAlt }}">
        @else
          <h1 class="sitename">{{ $clinic_site_name ?? '' }}</h1>
        @endif
      </a>

      <nav id="navmenu" class="navmenu">
        <ul>
          @foreach ($navLinks as $link)
            @php
              $routeName = $link['route'] ?? null;
              $url = $link['url'] ?? ($routeName ? route($routeName) : '#');
              $isActive = $routeName ? request()->routeIs($routeName) : false;
            @endphp
            <li><a href="{{ $url }}" class="{{ $isActive ? 'active' : '' }}">{{ $link['label'] ?? '' }}</a></li>
          @endforeach
        </ul>
        <i class="mobile-nav-toggle d-xl-none bi bi-list"></i>
      </nav>

    </div>

  </div>

</header>
