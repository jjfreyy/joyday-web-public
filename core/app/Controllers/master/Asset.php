<?php
namespace App\Controllers\Master;

use App\Controllers\BaseController;

class Asset extends BaseController {
  function index() {
    if (!check_privileges("ASS-I")) return redirect()->route("404");
    if (!is_empty(get_get("id"))) {
        try {
            $response = \fetch_get_request("master/asset/fetch", ["fetch_asset" => "edit", "id_user" => session("joyday")["id_user"], "id_asset" => get_get("id")]);
            // var_dump($response->getBody());
            if ($response->getStatusCode() !== 200) throw new \Exception($response->getBody());
            if (count(json_decode($response->getBody())) === 1) {
                $data = json_decode($response->getBody())[0];
                $client_data = [
                    "id_asset" => $data->id_asset,
                    "asset" => $data->asset,
                    "qr_code" => $data->qr_code,
                    "serial_number" => $data->serial_number,
                    "tanggal_akuisisi_asset" => $data->tanggal_akuisisi_asset,
                    "no_surat_kontrak" => $data->no_surat_kontrak,
                    "tanggal_berakhir_kontrak" => $data->tanggal_berakhir_kontrak,
                    "id_kepemilikan" => $data->id_kepemilikan,
                    "keterangan" => $data->keterangan,
                    "sta" => $data->sta,
                    "alasan" => $data->alasan,
                ];
                \prepare_flashdata($client_data);
            }
        } catch (\Exception $e) {
            \debug_exception($e);
        }
        return redirect()->route("master/asset");
    }

    $response = \fetch_get_request("master/asset/fetch", [
      "fetch_asset" => "ajax", 
      "id_user" => session("joyday")["id_user"],
      "type" => "kepemilikan",
    ]);
    if ($response->getStatusCode() !== 200) return redirect()->route("404");
    $kepemilikan_list = json_decode($response->getBody());
    if (is_empty_array($kepemilikan_list)) return redirect()->route("404");
    $data = ["kepemilikan_list" => $kepemilikan_list];
    echo view("master/v_asset", $data);
  }

  function save() {
    if (!isset($_POST["save_asset"])) return redirect()->route("404");
    
    try {
      $client_data = [
        "save_asset" => true,
        "id_user" => session("joyday")["id_user"],
        "asset" => get_post("asset"),
        "id_asset" => get_post("id_asset"),
        "id_barang" => get_post("id_barang"),
        "barang" => get_post("barang"),
        "qr_code" => get_post("qr_code"),
        "serial_number" => get_post("serial_number"),
        "tanggal_akuisisi_asset" => get_post("tanggal_akuisisi_asset"),
        "no_surat_kontrak" => get_post("no_surat_kontrak"),
        "tanggal_berakhir_kontrak" => get_post("tanggal_berakhir_kontrak"),
        "id_kepemilikan" => get_post("id_kepemilikan"),
        "keterangan" => get_post("keterangan"),
        "id_gudang" => get_post("id_gudang"),
        "gudang" => get_post("gudang"),
        "id_pelanggan" => get_post("id_pelanggan"),
        "pelanggan" => get_post("pelanggan"),
        "sta" => get_Post("sta"),
        "alasan" => get_Post("alasan"),
      ];
      $response = \fetch_post_request("master/asset/save", $client_data);
      // var_dump($client_data);
      // var_dump($response->getBody());
      if ($response->getStatusCode() !== 200) {
        unset($client_data["id_user"]);
        $client_data["report"] = get_form_report("error", json_decode($response->getBody(), true));
        prepare_flashdata($client_data);
      } else {
        \prepare_flashdata(["report" => \get_form_report("success", "asset")]);
      }
    } catch (\Exception $e) {
      \prepare_flashdata(["report" => \get_form_report("error", ["global" => "Tejadi kesalahan internal server."])]);
    }
    return redirect()->route("master/asset");
  }

  // ajax
  function fetch() {
    if ($this->request->isAJAX()) {
      try {
          $response = \fetch_get_request("master/asset/fetch", ["fetch_asset" => "ajax", "id_user" => session("joyday")["id_user"], "type" => get_get("type"), "filter" => get_get("filter")]);
          if ($response->getStatusCode() !== 200) throw new \Exception("invalid response");
          \send_response($response->getBody());
      } catch (\Exception $e) {
          send_500_response(\format_exception($e));
      }
    }
    return redirect()->route("404");
  }
}
