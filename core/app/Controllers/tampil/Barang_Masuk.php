<?php
namespace App\Controllers\Tampil;

use App\Controllers\BaseController;

class Barang_Masuk extends BaseController {
    
    function index() {
      if (!\check_privileges("BM-V")) return redirect()->route("404");
      $data = [
        "id_level" => session("joyday")["id_level_user"],
        "allow_edit" => \check_privileges("BM-E"), 
        "allow_delete" => \check_privileges("BM-D"), 
        "period" => json_decode(\fetch_get_request("tampil/barang_masuk/fetch", ["fetch_barang_masuk" => true, "type" => "period"])->getBody())
      ];
      echo view("tampil/v_barang_masuk", $data);
    }

    function delete() {
      if ($this->request->isAJAX()) {
        try {
          $id_barang_masuk = sanitize($this->request->getJSON()->id_barang_masuk);
          $alasan = sanitize($this->request->getJSON()->alasan);
          $response = \fetch_post_request("tampil/barang_masuk/delete", ["delete_barang_masuk" => true, "id_user" => session("joyday")["id_user"], "id_barang_masuk" => $id_barang_masuk, "alasan" => $alasan]);
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
          $response = \fetch_get_request("tampil/barang_masuk/fetch", [
            "fetch_barang_masuk" => true,
            "type" => "barang_masuk",
            "id_user" => session("joyday")["id_user"],
            "date1" => get_get("date1"),
            "date2" => get_get("date2"),
            "tipe" => get_get("tipe"),
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