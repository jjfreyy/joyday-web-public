<?php
namespace App\Controllers\Daftar;

use App\Controllers\BaseController;

class Barang extends BaseController {
    function index() {
      if (!\check_privileges("B-V")) return redirect()->route("404");
      $data = ["allow_edit" => \check_privileges("B-E"), "allow_delete" => \check_privileges("B-D")];
      echo view("daftar/v_barang", $data);
    }

    function delete() {
      if ($this->request->isAJAX()) {
        try {
          $id_barang = sanitize($this->request->getJSON()->id_barang);
          $alasan = sanitize($this->request->getJSON()->alasan);
          $response = \fetch_post_request("daftar/barang/delete", ["delete_barang" => true, "id_user" => session("joyday")["id_user"], "id_barang" => $id_barang, "alasan" => $alasan]);
          if ($response->getStatusCode() === 500) throw new \Exception();
          \send_response($response->getStatusCode(), $response->getBody());
        } catch (\Exception $e) {
          \send_500_response(\format_exception($e));
        }
      }
      // $response = \fetch_post_request("daftar/barang/delete", ["delete_barang" => true, "id_user" => session("joyday")["id_user"], "id_barang" => 4, "alasan" => "Test"]);
      // var_dump($response->getBody());
      return redirect()->route("404");
    }

    function fetch() {
      if ($this->request->isAJAX()) {
        try {
          $response = \fetch_get_request("daftar/barang/fetch", [
            "fetch_barang" => true,
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
