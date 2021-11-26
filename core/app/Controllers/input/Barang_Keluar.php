<?php
namespace App\Controllers\Input;

use App\Controllers\BaseController;

class Barang_Keluar extends BaseController {
  function index() {
    if (!check_privileges("BK-I")) return redirect()->route("404");
    if (!is_empty(get_get("id"))) {
      try {
        $response = \fetch_get_request("input/barang_keluar/fetch", ["fetch_barang_keluar" => "edit", "id_user" => session("joyday")["id_user"], "id_barang_keluar" => get_get("id")]);
        if ($response->getStatusCode() !== 200) throw new \Exception($response->getBody());
        $data = \json_decode($response->getBody())[0];
        // var_dump($data);
        if (!is_empty($data->id_barang_keluar)) {
          $client_data = [
            "id_barang_keluar" => $data->id_barang_keluar,
            "no_keluar" => $data->no_keluar,
            "keterangan" => $data->keterangan,
            "barang_keluar1" => explode("#", $data->barang_keluar1)
          ];
          \prepare_flashdata($client_data);
          // var_dump($client_data);
        }
      } catch (\Exception $e) {
        // echo $e;
        debug_exception($e);
      }
      return redirect()->route("input/barang_keluar");
    }

    $response = \fetch_get_request("input/barang_keluar/fetch", ["fetch_barang_keluar" => "ajax", "session" => session("joyday")["id_user"], "type" => "id_gudang", "filter" => session("joyday")["id_user"]]);
    $can_input = true;
    if ($response->getStatusCode() !== 200) {
      $can_input = false; 
    } else {
      $data = json_decode($response->getBody());
      if (empty($data)) {
        $can_input = false;
        $data = ["id_gudang" => null];
      } else {
        $data = ["id_gudang" => $data[0]->id_gudang];
      }
    }

    if (!$can_input) {
      echo "<script>alert('Tidak dapat melakukan input. Data gudang tidak dapat ditemukan.')</script>";
    }

    echo view("input/v_barang_keluar", $data);
  }

  function save() {
    if (!isset($_POST["save_barang_keluar"])) return redirect()->route("404");
    
    try {
      $client_data = [
        "save_barang_keluar" => true,
        "id_user" => session("joyday")["id_user"],
        "id_gudang" => get_post("id_gudang"),
        "id_barang_keluar" => get_post("id_barang_keluar"),
        "no_keluar" => get_post("no_keluar"),
        "keterangan" => get_post("keterangan"),
        "barang_keluar1" => get_post("barang_keluar1", false),
      ];
      $response = \fetch_post_request("input/barang_keluar/save", $client_data);
      // var_dump($client_data);
      // print_r($response->getBody());
      if ($response->getStatusCode() !== 200) {
        unset($client_data["id_user"]);
        $client_data["report"] = get_form_report("error", json_decode($response->getBody(), true));
        prepare_flashdata($client_data);
      } else {
        $id_barang_keluar = json_decode($response->getBody())->id_barang_keluar;
        $url = base_url("tampil/barang_keluar/print?id=$id_barang_keluar");
        \prepare_flashdata(["report" => \get_form_report("success", [
          "Data barang keluar berhasil ditambahkan / diubah.<br>
          <a href='$url' target='_blank'>Cetak Surat Jalan</a>"
        ])]);
      }
    } catch (\Exception $e) {
      \prepare_flashdata(["report" => \get_form_report("error", ["global" => "Tejadi kesalahan internal server."])]);
    }
    return redirect()->route("input/barang_keluar");
  }

    // ajax
  function fetch() {
    if ($this->request->isAJAX()) {
      try {
        $response = \fetch_get_request("input/barang_keluar/fetch", ["fetch_barang_keluar" => "ajax", "id_user" => session("joyday")["id_user"], "type" => get_get("type"), "filter" => get_get("filter")]);
        if ($response->getStatusCode() !== 200) throw new \Exception("invalid response");
        \send_response($response->getBody());
      } catch (\Exception $e) {
        send_500_response(\format_exception($e));
      }
    } 
    return redirect()->route("404");
  }
}
