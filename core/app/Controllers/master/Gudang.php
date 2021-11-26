<?php
namespace App\Controllers\Master;

use App\Controllers\BaseController;

class Gudang extends BaseController {
    function index() {
        if (!check_privileges("GUD-I")) return redirect()->route("404");
        if (!is_empty(get_get("id"))) {
            try {
                $response = \fetch_get_request("master/gudang/fetch", ["fetch_gudang" => "edit", "id_user" => session("joyday")["id_user"], "id_gudang" => get_get("id")]);
                if ($response->getStatusCode() !== 200) throw new \Exception($response->getBody());
                if (count(\json_decode($response->getBody())) === 1) {
                    $data = json_decode($response->getBody())[0];
                    $client_data = [
                        "id_gudang" => $data->id_gudang,
                        "id_kepala_gudang" => $data->id_kepala_gudang,
                        "kepala_gudang" => is_empty($data->id_kepala_gudang) ? "" : $data->kode_kepala_gudang. " / " .$data->nama_kepala_gudang,
                        "kode_gudang" => explode("-", $data->kode_gudang)[1],
                        "nama_gudang" => $data->nama_gudang,
                        "keterangan" => $data->keterangan,
                    ];
                    \prepare_flashdata($client_data);
                }
            } catch (\Exception $e) {
                debug_exception($e);
            }
            // return redirect()->route("master/gudang");
        }
        echo view("master/v_gudang");
    }

    function save() {
        if (!isset($_POST["save_gudang"])) return redirect()->route("404");
        
        try {
            $client_data = [
                "save_gudang" => true,
                "id_user" => session("joyday")["id_user"],
                "id_gudang" => get_post("id_gudang"),
                "id_kepala_gudang" => get_post("id_kepala_gudang"),
                "kepala_gudang" => get_post("kepala_gudang"),
                "kode_gudang" => get_post("kode_gudang"),
                "nama_gudang" => get_post("nama_gudang"),
                "keterangan" => get_post("keterangan"),
            ];
            $response = \fetch_post_request("master/gudang/save", $client_data);
            // var_dump($response->getBody());
            if ($response->getStatusCode() !== 200) {
                unset($client_data["id_user"]);
                $client_data["report"] = get_form_report("error", json_decode($response->getBody(), true));
                prepare_flashdata($client_data);
            } else {
                \prepare_flashdata(["report" => \get_form_report("success", "gudang")]);
            }
        } catch (\Exception $e) {
            \prepare_flashdata(["report" => \get_form_report("error", ["global" => "Tejadi kesalahan internal server."])]);
        }
        return redirect()->route("master/gudang");
    }

    // ajax
    function fetch() {
        if ($this->request->isAJAX()) {
            try {
                $response = \fetch_get_request("master/gudang/fetch", ["fetch_gudang" => "ajax", "id_user" => session("joyday")["id_user"], "type" => get_get("type"), "filter" => get_get("filter")]);
                if ($response->getStatusCode() !== 200) throw new \Exception("invalid response");
                \send_response($response->getBody());
            } catch (\Exception $e) {
                send_500_response(\format_exception($e));
            }
        } 
        return redirect()->route("404");
    }
}
