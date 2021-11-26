<?php
namespace App\Controllers\Input;

use App\Controllers\BaseController;

class Pesanan extends BaseController {
  function index() {
    if (!check_privileges("PES-I")) return redirect()->route("404");
    if (!is_empty(get_get("id"))) {
        try {
          $response = \fetch_get_request("input/pesanan/fetch", ["fetch_pesanan" => "edit", "id_user" => session("joyday")["id_user"], "id_pesanan" => get_get("id")]);
          if ($response->getStatusCode() !== 200) throw new \Exception($response->getBody());
          $data = \json_decode($response->getBody());
          if (count($data) > 1) {
            $client_data = [
              "id_pesanan" => $data[0]->id_pesanan,
              "no_po" => explode("-", $data[0]->no_po)[1],
              "id_distributor" => $data[0]->id_distributor,
              "distributor" => $data[0]->distributor,
              "keterangan" => $data[0]->keterangan,
            ];

            for ($i = 1; $i < count($data); $i++) {
              $pesanan1[] = $data[$i]->id_barang. ";" .$data[$i]->barang. ";" .$data[$i]->qty;
            }
            $client_data["pesanan1"] = $pesanan1;
            \prepare_flashdata($client_data);
          }
        } catch (\Exception $e) {
            debug_exception($e);
        }
        return redirect()->route("input/pesanan");
    }
    echo view("input/v_pesanan");
  }

  function save() {
    if (!isset($_POST["save_pesanan"])) return redirect()->route("404");
    
    try {
      $client_data = [
        "save_pesanan" => true,
        "id_user" => session("joyday")["id_user"],
        "id_pesanan" => get_post("id_pesanan"),
        "no_po" => get_post("no_po"),
        "id_distributor" => get_post("id_distributor"),
        "distributor" => get_post("distributor"),
        "keterangan" => get_post("keterangan"),
        "pesanan1" => get_post("pesanan1", false),
      ];
      $response = \fetch_post_request("input/pesanan/save", $client_data);
      // var_dump($client_data);
      // var_dump($response->getBody());
      if ($response->getStatusCode() !== 200) {
        unset($client_data["id_user"]);
        $client_data["report"] = get_form_report("error", json_decode($response->getBody(), true));
        prepare_flashdata($client_data);
      } else {
        \prepare_flashdata(["report" => \get_form_report("success", "pesanan")]);
      }
    } catch (\Exception $e) {
      \prepare_flashdata(["report" => \get_form_report("error", ["global" => "Tejadi kesalahan internal server."])]);
    }
    return redirect()->route("input/pesanan");
  }

  // ajax
  function fetch() {
    if ($this->request->isAJAX()) {
      try {
        $response = \fetch_get_request("input/pesanan/fetch", ["fetch_pesanan" => "ajax", "id_user" => session("joyday")["id_user"], "type" => get_get("type"), "filter" => get_get("filter")]);
        if ($response->getStatusCode() !== 200) throw new \Exception("invalid response");
        \send_response($response->getBody());
      } catch (\Exception $e) {
        send_500_response(\format_exception($e));
      }
    } 
    return redirect()->route("404");
  }
}
