<?php include 'svg-icons.php'; ?>

<?php
// Fetch dynamic data for dropdowns
$countries = getDistinctValues($conn, 'country');
$cities = getDistinctValues($conn, 'city');
$manufacturers = getDistinctValues($conn, 'manufacturer');
$tags = getDistinctValues($conn, 'tag'); // Tags represent places
?>

<header class="navbar navbar-expand-lg bd-navbar sticky-top">
<nav class="container-xxl bd-gutter flex-wrap flex-lg-nowrap" aria-label="Offcanvas navbar large">
      <div class="d-lg-none" style="width: 4.25rem;"></div>

    <a class="navbar-brand p-0 me-0 me-lg-2" href="/" aria-label="Camhacker">


<svg xmlns="http://www.w3.org/2000/svg" width="48" height="48" fill="currentColor" class="bi bi-webcam d-block" viewBox="0 0 16 16"><title>Camhacker</title>
  <path d="M0 6a2 2 0 0 1 2-2h12a2 2 0 0 1 2 2v3a2 2 0 0 1-2 2H9.269c.144.162.33.324.531.475a7 7 0 0 0 .907.57l.014.006.003.002A.5.5 0 0 1 10.5 13h-5a.5.5 0 0 1-.224-.947l.003-.002.014-.007a5 5 0 0 0 .268-.148 7 7 0 0 0 .639-.421c.2-.15.387-.313.531-.475H2a2 2 0 0 1-2-2zm2-1a1 1 0 0 0-1 1v3a1 1 0 0 0 1 1h12a1 1 0 0 0 1-1V6a1 1 0 0 0-1-1z"/>
  <path d="M8 6.5a1 1 0 1 0 0 2 1 1 0 0 0 0-2m-2 1a2 2 0 1 1 4 0 2 2 0 0 1-4 0m7 0a.5.5 0 1 1-1 0 .5.5 0 0 1 1 0"/>
</svg>
    </a>

    <div class="d-flex">
      <div class="bd-search" id="docsearch" data-bd-docs-version="5.3">
<button type="button" class="DocSearch DocSearch-Button" aria-label="Search" data-bs-toggle="modal" data-bs-target="#myModal"><span class="DocSearch-Button-Container"><svg width="20" height="20" class="DocSearch-Search-Icon" viewBox="0 0 20 20" aria-hidden="true"><path d="M14.386 14.386l4.0877 4.0877-4.0877-4.0877c-2.9418 2.9419-7.7115 2.9419-10.6533 0-2.9419-2.9418-2.9419-7.7115 0-10.6533 2.9418-2.9419 7.7115-2.9419 10.6533 0 2.9419 2.9418 2.9419 7.7115 0 10.6533z" stroke="currentColor" fill="none" fill-rule="evenodd" stroke-linecap="round" stroke-linejoin="round"></path></svg><span class="DocSearch-Button-Placeholder">Search</span></span><span class="DocSearch-Button-Keys"><kbd class="DocSearch-Button-Key">⌘</kbd><kbd class="DocSearch-Button-Key">K</kbd></span></button></div>

      <button class="navbar-toggler d-flex d-lg-none order-3 p-2" type="button" data-bs-toggle="offcanvas" data-bs-target="#bdNavbar" aria-controls="bdNavbar" aria-label="Toggle navigation">
        <svg class="bi" aria-hidden="true"><use xlink:href="#three-dots"></use></svg>
      </button>
    </div>

    <div class="offcanvas-lg offcanvas-end flex-grow-1" tabindex="-1" id="bdNavbar" aria-labelledby="bdNavbarOffcanvasLabel" data-bs-scroll="true">
      <div class="offcanvas-header px-4 pb-0">
        <h5 class="offcanvas-title text-white" id="bdNavbarOffcanvasLabel">Camhacker</h5>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="offcanvas" aria-label="Close" data-bs-target="#bdNavbar"></button>
      </div>

      <div class="offcanvas-body p-4 pt-0 p-lg-0">
        <hr class="d-lg-none text-white-50">
<ul class="navbar-nav flex-row flex-wrap bd-navbar-nav">

     <!-- Countries Dropdown -->
            <li class="nav-item dropdown col-6 col-lg-auto">
                <a class="nav-link dropdown-toggle py-2 px-0 px-lg-2" href="#" id="countriesDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                    Countries
                </a>
                <ul class="dropdown-menu overflow-auto" style="max-height: 300px;" aria-labelledby="countriesDropdown">
                    <?php foreach ($countries as $country_value): ?>
                        <li><a class="dropdown-item" href="<?php echo generateSeoUrl('country', $country_value); ?>"><?php echo htmlspecialchars($country_value); ?></a></li>
                    <?php endforeach; ?>
                </ul>
            </li>

            <!-- Cities Dropdown -->
            <li class="nav-item dropdown col-6 col-lg-auto">
                <a class="nav-link dropdown-toggle py-2 px-0 px-lg-2" href="#" id="citiesDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                    Cities
                </a>
                <ul class="dropdown-menu overflow-auto" style="max-height: 300px;" aria-labelledby="citiesDropdown">
                    <?php foreach ($cities as $city_value): ?>
                        <li><a class="dropdown-item" href="<?php echo generateSeoUrl('city', $city_value); ?>"><?php echo htmlspecialchars($city_value); ?></a></li>
                    <?php endforeach; ?>
                </ul>
            </li>

            <!-- Places Dropdown (Tags) -->
            <li class="nav-item dropdown col-6 col-lg-auto">
                <a class="nav-link dropdown-toggle py-2 px-0 px-lg-2" href="#" id="placesDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                    Places
                </a>
                <ul class="dropdown-menu overflow-auto" style="max-height: 300px;" aria-labelledby="placesDropdown">
                    <?php foreach ($tags as $tag_value): ?>
                        <li><a class="dropdown-item" href="<?php echo generateSeoUrl('place', $tag_value); ?>"><?php echo htmlspecialchars($tag_value); ?></a></li>
                    <?php endforeach; ?>
                </ul>
            </li>

            <!-- Manufacturers Dropdown -->
            <li class="nav-item dropdown col-6 col-lg-auto">
                <a class="nav-link dropdown-toggle py-2 px-0 px-lg-2" href="#" id="manufacturersDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                    Manufacturers
                </a>
                <ul class="dropdown-menu overflow-auto" style="max-height: 300px;" aria-labelledby="manufacturersDropdown">
                    <?php foreach ($manufacturers as $manufacturer_value): ?>
                        <li><a class="dropdown-item" href="<?php echo generateSeoUrl('manufacturer', $manufacturer_value); ?>"><?php echo htmlspecialchars($manufacturer_value); ?></a></li>
                    <?php endforeach; ?>
                </ul>
            </li>
</ul>


        <hr class="d-lg-none text-white-50">

        <ul class="navbar-nav flex-row flex-wrap ms-md-auto">
          <li class="nav-item col-6 col-lg-auto">
            <a class="nav-link py-2 px-0 px-lg-2" href="https://twitter.com/camhacker" target="_blank" rel="noopener">
              <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" class="navbar-nav-svg" viewBox="0 0 512 416.32" role="img"><title>Twitter</title><path fill="currentColor" d="M160.83 416.32c193.2 0 298.92-160.22 298.92-298.92 0-4.51 0-9-.2-13.52A214 214 0 0 0 512 49.38a212.93 212.93 0 0 1-60.44 16.6 105.7 105.7 0 0 0 46.3-58.19 209 209 0 0 1-66.79 25.37 105.09 105.09 0 0 0-181.73 71.91 116.12 116.12 0 0 0 2.66 24c-87.28-4.3-164.73-46.3-216.56-109.82A105.48 105.48 0 0 0 68 159.6a106.27 106.27 0 0 1-47.53-13.11v1.43a105.28 105.28 0 0 0 84.21 103.06 105.67 105.67 0 0 1-47.33 1.84 105.06 105.06 0 0 0 98.14 72.94A210.72 210.72 0 0 1 25 370.84a202.17 202.17 0 0 1-25-1.43 298.85 298.85 0 0 0 160.83 46.92"></path></svg>
              <small class="d-lg-none ms-2">Twitter</small>
            </a>
          </li>


          <li class="nav-item py-2 py-lg-1 col-12 col-lg-auto">
            <div class="vr d-none d-lg-flex h-100 mx-lg-2 text-white"></div>
            <hr class="d-lg-none my-2 text-white-50">
          </li>

          <li class="nav-item dropdown">
            <button class="btn btn-link nav-link py-2 px-0 px-lg-2 dropdown-toggle d-flex align-items-center" id="bd-theme" type="button" aria-expanded="false" data-bs-toggle="dropdown" data-bs-display="static" aria-label="Toggle theme (dark)">
              <svg class="bi my-1 theme-icon-active"><use href="#moon-stars-fill"></use></svg>
              <span class="d-lg-none ms-2" id="bd-theme-text">Toggle theme</span>
            </button>
            <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="bd-theme-text">
              <li>
                <button type="button" class="dropdown-item d-flex align-items-center" data-bs-theme-value="light" aria-pressed="false">
                  <svg class="bi me-2 opacity-50"><use href="#sun-fill"></use></svg>
                  Light
                  <svg class="bi ms-auto d-none"><use href="#check2"></use></svg>
                </button>
              </li>
              <li>
                <button type="button" class="dropdown-item d-flex align-items-center" data-bs-theme-value="dark" aria-pressed="false">
                  <svg class="bi me-2 opacity-50"><use href="#moon-stars-fill"></use></svg>
                  Dark
                  <svg class="bi ms-auto d-none"><use href="#check2"></use></svg>
                </button>
              </li>
              <li>
                <button type="button" class="dropdown-item d-flex align-items-center" data-bs-theme-value="auto" aria-pressed="false">
                  <svg class="bi me-2 opacity-50"><use href="#circle-half"></use></svg>
                  Auto
                  <svg class="bi ms-auto d-none"><use href="#check2"></use></svg>
                </button>
              </li>
            </ul>
          </li>
        </ul>
      </div>
    </div>
  </nav>
</header>