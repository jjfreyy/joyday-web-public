<?php
namespace App\Controllers\Sistem;

use App\Controllers\BaseController;

class Hak_Akses extends BaseController {

  function index() {
    try { 
      $response = \fetch_get_request("sistem/hak_akses/fetch", ["fetch_hak_akses" => true, "id_user" => session("joyday")["id_user"]]);
      if ($response->getStatusCode() !== 200) throw new \Exception();
      $data["hak_akses_arr"] = json_decode($response->getBody());
      echo view("sistem/v_hak_akses", $data);
    } catch (\Exception $e) {
      return redirect()->route("404");
    }
  }

}
