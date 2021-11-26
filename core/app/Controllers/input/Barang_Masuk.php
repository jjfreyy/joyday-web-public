<?php
namespace App\Controllers\Input;

use App\Controllers\BaseController;

class Barang_Masuk extends BaseController {
  function index() {
    if (!check_privileges("BM-I")) return redirect()->route("404");
    if (!is_empty(get_get("id"))) {
      try {
        $response = \fetch_get_request("input/barang_masuk/fetch", ["fetch_barang_masuk" => "edit", "id_user" => session("joyday")["id_user"], "id_barang_masuk" => get_get("id")]);
        // var_dump($response->getBody());
        if ($response->getStatusCode() !== 200) throw new \Exception($response->getBody());
        $data = \json_decode($response->getBody())[0];
        // var_dump($data);
        if (!is_empty($data->id_barang_masuk)) {
          $id_barang_masuk = $data->id_barang_masuk;
          $no_masuk = $data->no_masuk;
          $tipe = $data->tipe;
          $no_faktur = $data->no_faktur;
          $dari_id_pesanan = $data->dari_id_pesanan;
          $no_po = $data->no_po;
          $ke_id_agen = $data->ke_id_agen;
          $ke_agen = $data->ke_agen;
          $alamat = $data->alamat;
          $keterangan = $data->keterangan;
          $barang_masuk1 = is_empty($data->barang_masuk1) ? null : explode("#", $data->barang_masuk1);
          if (in_array($tipe, ["0", "2"])) {
            $client_data = [
              "tipe" => $tipe,
              "id_barang_masuk" => $id_barang_masuk,
              "no_masuk" => $no_masuk,
              "no_faktur" => $no_faktur,
              "dari_id_pesanan" => $dari_id_pesanan,
              "no_po" => $no_po,
              "ke_id_agen" => $ke_id_agen,
              "ke_agen" => $ke_agen,
              "alamat" => $alamat,
              "keterangan" => $keterangan,
              "barang_masuk1" => $barang_masuk1,
            ];
          } else {
            $client_data = [
              "tipe" => $tipe,
              "id_barang_masuk" => $id_barang_masuk,
              "no_masuk" => $no_masuk,
              "keterangan" => $keterangan,
            ];
          }
          // var_dump($client_data);
          \prepare_flashdata($client_data);
        }
      } catch (\Exception $e) {
        // echo $e;
        debug_exception($e);
      }
      return redirect()->route("input/barang_masuk");
    }

    $data = [
      "id_level" => session("joyday")["id_level_user"],
    ];
    echo view("input/v_barang_masuk", $data);
  }

  function save() {
    if (!isset($_POST["save_barang_masuk"])) return redirect()->route("404");
    try {
      $client_data = [
        "save_barang_masuk" => true,
        "id_user" => session("joyday")["id_user"],
        
        "tipe" => get_post("tipe"),
        "id_barang_masuk" => get_post("id_barang_masuk"),
        "no_masuk" => get_post("no_masuk"),
        "no_faktur" => get_post("no_faktur"),
        "dari_id_pesanan" => get_post("dari_id_pesanan"),
        
        "no_po" => get_post("no_po"),
        "ke_id_agen" => get_post("ke_id_agen"),
        "ke_agen" => get_post("ke_agen"),
        "alamat" => get_post("alamat"),
        "keterangan" => get_post("keterangan"),
        
        "barang_masuk1" => get_post("barang_masuk1", false),
      ];
      $response = \fetch_post_request("input/barang_masuk/save", $client_data);
      var_dump($client_data);
      var_dump($response->getBody());
      if ($response->getStatusCode() !== 200) {
        unset($client_data["id_user"]);
        $client_data["report"] = get_form_report("error", json_decode($response->getBody(), true));
        prepare_flashdata($client_data);
      } else {
        \prepare_flashdata(["report" => \get_form_report("success", "barang masuk")]);
      }
    } catch (\Exception $e) {
      \prepare_flashdata(["report" => \get_form_report("error", ["global" => "Tejadi kesalahan internal server."])]);
    }
    return redirect()-> route("input/barang_masuk");
  }

  // ajax
  function fetch() {
    if ($this->request->isAJAX()) {
      try {
        $response = \fetch_get_request("input/barang_masuk/fetch", ["fetch_barang_masuk" => "ajax", "id_user" => session("joyday")["id_user"], "type" => get_get("type"), "filter" => get_get("filter")]);
        if ($response->getStatusCode() !== 200) throw new \Exception("invalid response");
        \send_response($response->getBody());
      } catch (\Exception $e) {
        send_500_response(\format_exception($e));
      }
    } 
    return redirect()->route("404");
  }
}
