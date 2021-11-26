<header class="header_navbar">
  <nav class="navbar">
    <a href="#" id="navicon" onclick="slide_menu_open()">
      <svg width="30" height="25">
      <path d="M0,5 30,5" stroke="#fff" stroke-width="5" />
      <path d="M0,14 30,14" stroke="#fff" stroke-width="5" />
      <path d="M0,23 30,23" stroke="#fff" stroke-width="5" />
      </svg>
    </a>

    <ul id="nav_menu" class="navbar_list">
      <?php
        $response = fetch_get_request("menu/get_menu", ["type" => "header", "id_user" => session("joyday")["id_user"]]);
        if ($response && $response && $response->getStatusCode() === 200) echo $response->getBody();
      ?>
      <li><?php echo anchor('logout', 'Keluar'); ?></li>
    </ul>

  </nav>
</header>
