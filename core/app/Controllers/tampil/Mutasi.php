<?php
namespace App\Controllers\Tampil;

use App\Controllers\BaseController;

class Mutasi extends BaseController {
  function index() {
    if (!\check_privileges("MUT-V")) return redirect()->route("404");
    $data = [
      "allow_edit" => \check_privileges("MUT-E"),
      "allow_delete" => \check_privileges("MUT-D"),
      "period" => \json_decode(\fetch_get_request("tampil/mutasi/fetch", ["fetch_mutasi" => true, "type" => "period"])->getBody()),
    ];
    echo view("tampil/v_mutasi", $data);
  }

  function delete() {
    if ($this->request->isAJAX()) {
      try {
        $id_mutasi = sanitize($this->request->getJSON()->id_mutasi);
        $alasan = sanitize($this->request->getJSON()->alasan);
        $response = \fetch_post_request("tampil/mutasi/delete", ["delete_mutasi" => true, "id_user" => session("joyday")["id_user"], "id_mutasi" => $id_mutasi, "alasan" => $alasan]);
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
        $response = \fetch_get_request("tampil/mutasi/fetch", [
          "fetch_mutasi" => true,
          "type" => "mutasi",
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
