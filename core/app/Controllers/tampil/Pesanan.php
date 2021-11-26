<?php
namespace App\Controllers\Tampil;

use App\Controllers\BaseController;

class Pesanan extends BaseController {
    function index() {
      if (!\check_privileges("PES-V")) return redirect()->route("404");
      $data = [
        "allow_edit" => \check_privileges("PES-E"), 
        "allow_delete" => \check_privileges("PES-D"), 
        "period" => json_decode(\fetch_get_request("tampil/pesanan/fetch", ["fetch_pesanan" => true, "type" => "period"])->getBody()),
      ];
      echo view("tampil/v_pesanan", $data);
    }

    function delete() {
      if ($this->request->isAJAX()) {
        try {
          $id_pesanan = sanitize($this->request->getJSON()->id_pesanan);
          $alasan = sanitize($this->request->getJSON()->alasan);
          $response = \fetch_post_request("tampil/pesanan/delete", ["delete_pesanan" => true, "id_user" => session("joyday")["id_user"], "id_pesanan" => $id_pesanan, "alasan" => $alasan]);
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
          $response = \fetch_get_request("tampil/pesanan/fetch", [
            "fetch_pesanan" => true,
            "type" => "pesanan",
            "id_user" => session("joyday")["id_user"],
            "date1" => get_get("date1"),
            "date2" => get_get("date2"),
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