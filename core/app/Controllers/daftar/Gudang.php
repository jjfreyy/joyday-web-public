<?php
namespace App\Controllers\Daftar;

use App\Controllers\BaseController;

class Gudang extends BaseController {
    function index() {
      if (!\check_privileges("GUD-V")) return redirect()->route("404");
      $data = ["allow_edit" => \check_privileges("GUD-E"), "allow_delete" => \check_privileges("GUD-D")];
      echo view("daftar/v_gudang", $data);
    }

    function delete() {
      if ($this->request->isAJAX()) {
        try {
          $id_gudang = sanitize($this->request->getJSON()->id_gudang);
          $alasan = sanitize($this->request->getJSON()->alasan);
          $response = \fetch_post_request("daftar/gudang/delete", ["delete_gudang" => true, "id_user" => session("joyday")["id_user"], "id_gudang" => $id_gudang, "alasan" => $alasan]);
          if ($response->getStatusCode() === 500) throw new \Exception();
          \send_response($response->getStatusCode(), $response->getBody());
        } catch (\Exception $e) {
          \send_500_response(\format_exception($e));
        }
      }

      return redirect()->route("404");
    }

    function fetch() {
      if ($this->request->isAJAX()) {
        try {
          $response = \fetch_get_request("daftar/gudang/fetch", [
            "fetch_gudang" => true,
            "id_user" => session("joyday")["id_user"],
            "filter" => get_get("filter"),
            "page" => get_get("page"),
            "display_per_page" => get_get("display_per_page"),
          ]);
          if ($response->getStatusCode() !== 200) throw new \Exception("invalid response");
          \send_response($response->getBody());
        } catch (\Exception $e) {
          \send_500_response(\format_exception($e));
        }
      }
      
      return redirect()->route("404");
    }

}