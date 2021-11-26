<?php
namespace App\Controllers\Master;

use App\Controllers\BaseController;

class Barang extends BaseController {
    function index() {
        if (!check_privileges("B-I")) return redirect()->route("404");
        if (!is_empty(get_get("id"))) {
            try {
                $response = \fetch_get_request("master/barang/fetch", ["fetch_barang" => "edit", "id_user" => session("joyday")["id_user"], "id_barang" => get_get("id")]);
                // var_dump($response->getBody());
                if ($response->getStatusCode() !== 200) throw new \Exception($response->getBody());
                if (count(json_decode($response->getBody())) === 1) {
                    $data = json_decode($response->getBody())[0];
                    $client_data = [
                        "id_barang" => $data->id_barang,
                        "kode_barang" => $data->kode_barang,
                        "nama_barang" => $data->nama_barang,
                        "id_brand" => $data->id_brand,
                        "nama_brand" => $data->nama_brand,
                        "id_tipe" => $data->id_tipe,
                        "nama_tipe" => $data->nama_tipe,
                        "ukuran" => $data->ukuran,
                        "keterangan" => $data->keterangan,
                    ];
                    \prepare_flashdata($client_data);
                }
            } catch (\Exception $e) {
                \debug_exception($e);
            }
            return redirect()->route("master/barang");
        }
        echo view("master/v_barang");
    }

    function save() {
        if (!isset($_POST["save_barang"])) return redirect()->route("404");
        
        try {
            $client_data = [
                "save_barang" => true,
                "id_user" => session("joyday")["id_user"],
                "id_barang" => get_post("id_barang"),
                "kode_barang" => get_post("kode_barang"),
                "nama_barang" => get_post("nama_barang"),
                "id_brand" => get_post("id_brand"),
                "nama_brand" => get_post("nama_brand"),
                "id_tipe" => get_post("id_tipe"),
                "nama_tipe" => get_post("nama_tipe"),
                "ukuran" => get_post("ukuran"),
                "keterangan" => get_post("keterangan"),
            ];
            // var_dump($client_data);
            $response = \fetch_post_request("master/barang/save", $client_data);
            // var_dump($response->getBody());
            if ($response->getStatusCode() !== 200) {
                unset($client_data["id_user"]);
                $client_data["report"] = get_form_report("error", json_decode($response->getBody(), true));
                prepare_flashdata($client_data);
            } else {
                \prepare_flashdata(["report" => \get_form_report("success", "barang")]);
            }
        } catch (\Exception $e) {
            \prepare_flashdata(["report" => \get_form_report("error", ["global" => "Tejadi kesalahan internal server."])]);
        }
        return redirect()->route("master/barang");
    }

    // ajax
    function fetch() {
        if ($this->request->isAJAX()) {
            try {
                $response = \fetch_get_request("master/barang/fetch", ["fetch_barang" => "ajax", "id_user" => session("joyday")["id_user"], "type" => get_get("type"), "filter" => get_get("filter")]);
                if ($response->getStatusCode() !== 200) throw new \Exception("invalid response");
                \send_response($response->getBody());
            } catch (\Exception $e) {
                send_500_response(\format_exception($e));
            }
        } 
        return redirect()->route("404");
    }
}
