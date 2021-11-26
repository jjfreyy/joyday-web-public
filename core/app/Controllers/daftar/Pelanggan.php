<?php
namespace App\Controllers\Daftar;

use App\Controllers\BaseController;

class Pelanggan extends BaseController {
    function index() {
      if (!\check_privileges("PEL-V")) return redirect()->route("404");
      $data = ["allow_edit" => \check_privileges("PEL-E"), "allow_delete" => \check_privileges("PEL-D")];
      echo view("daftar/v_pelanggan", $data);
    }

    function delete() {
      if ($this->request->isAJAX()) {
        try {
          $id_pelanggan = sanitize($this->request->getJSON()->id_pelanggan);
          $alasan = sanitize($this->request->getJSON()->alasan);
          $response = \fetch_post_request("daftar/pelanggan/delete", ["delete_pelanggan" => true, "id_user" => session("joyday")["id_user"], "id_pelanggan" => $id_pelanggan, "alasan" => $alasan]);
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
          $response = \fetch_get_request("daftar/pelanggan/fetch", [
            "fetch_pelanggan" => true,
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
