<aside id="slide_menu" class="side_nav">
  <a href="#" class="btn_close" onclick="slide_menu_close()">&times;</a>

  <?php
    $response = fetch_get_request("menu/get_menu", ["type" => "aside", "id_user" => session("joyday")["id_user"]]);
    if ($response && $response->getStatusCode() === 200) echo $response->getBody();
  ?>
  <a href="<?php echo base_url("logout"); ?>" class="link_aside">Keluar</a>
</aside>
