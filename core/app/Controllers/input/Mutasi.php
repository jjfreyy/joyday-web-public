<?php
namespace App\Controllers\Input;

use App\Controllers\BaseController;

class Mutasi extends BaseController {
  function index() {
    if (!check_privileges("MUT-I")) return redirect()->route("404");
    if (!is_empty(get_get("id"))) {
        try {
          $response = \fetch_get_request("input/mutasi/fetch", ["fetch_mutasi" => "edit", "id_user" => session("joyday")["id_user"], "id_mutasi" => get_get("id")]);
          if ($response->getStatusCode() !== 200) throw new \Exception($response->getBody());
          $data = \json_decode($response->getBody())[0];
          // var_dump($data);
          if (!is_empty($data->id_mutasi)) {
            $client_data = [
              "id_mutasi" => $data->id_mutasi,
              "no_mutasi" => $data->no_mutasi,
              "dari_id_pelanggan" => $data->dari_id_pelanggan,
              "dari_pelanggan" => $data->dari_pelanggan,
              "keterangan" => $data->keterangan,
              "mutasi1" => explode("#", $data->mutasi1),
            ];
            \prepare_flashdata($client_data);
            var_dump($client_data);
          }
        } catch (\Exception $e) {
          echo $e;
          debug_exception($e);
        }
        return redirect()->route("input/mutasi");
    }
    echo view("input/v_mutasi");
  }

  function save() {
    if (!isset($_POST["save_mutasi"])) return redirect()->route("404");
    
    try {
      $client_data = [
        "save_mutasi" => true,
        "id_user" => session("joyday")["id_user"],
        
        "id_mutasi" => get_post("id_mutasi"),
        "no_mutasi" => get_post("no_mutasi"),
        "dari_id_pelanggan" => get_post("dari_id_pelanggan"),
        "dari_pelanggan" => get_post("dari_pelanggan"),
        "keterangan" => get_post("keterangan"),
        "mutasi1" => get_post("mutasi1", false),
      ];
      $response = \fetch_post_request("input/mutasi/save", $client_data);
      // var_dump($client_data);
      // var_dump($response->getBody());
      if ($response->getStatusCode() !== 200) {
        unset($client_data["id_user"]);
        $client_data["report"] = get_form_report("error", json_decode($response->getBody(), true));
        prepare_flashdata($client_data);
      } else {
        \prepare_flashdata(["report" => \get_form_report("success", "mutasi")]);
      }
    } catch (\Exception $e) {
      \prepare_flashdata(["report" => \get_form_report("error", ["global" => "Tejadi kesalahan internal server."])]);
    }
    return redirect()->route("input/mutasi");
  }

  // ajax
  function fetch() {
    if ($this->request->isAJAX()) {
      try {
        $response = \fetch_get_request("input/mutasi/fetch", ["fetch_mutasi" => "ajax", "id_user" => session("joyday")["id_user"], "type" => get_get("type"), "filter" => get_get("filter")]);
        if ($response->getStatusCode() !== 200) throw new \Exception("invalid response");
        \send_response($response->getBody());
      } catch (\Exception $e) {
        send_500_response(\format_exception($e));
      }
    } 
    return redirect()->route("404");
  }
}
