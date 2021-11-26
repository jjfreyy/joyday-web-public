<?php
namespace App\Controllers\Master;

use App\Controllers\BaseController;

class Distributor extends BaseController {
    function index() {
        if (!check_privileges("DIS-I")) return redirect()->route("404");
        if (!is_empty(get_get("id"))) {
            try {
                $response = \fetch_get_request("master/distributor/fetch", ["fetch_distributor" => "edit", "id_user" => session("joyday")["id_user"], "id_distributor" => get_get("id")]);
                if ($response->getStatusCode() !== 200) throw new \Exception($response->getBody());
                if (count(\json_decode($response->getBody())) === 1) {
                    $data = json_decode($response->getBody())[0];
                    $client_data = [
                        "id_distributor" => $data->id_distributor,
                        "kode_distributor" => explode("-", $data->kode_distributor)[1],
                        "nama_distributor" => $data->nama_distributor,
                        "alamat" => $data->alamat,
                        "no_hp" => $data->no_hp,
                        "email" => $data->email,
                        "keterangan" => $data->keterangan,
                    ];
                    \prepare_flashdata($client_data);
                }
            } catch (\Exception $e) {
                debug_exception($e);
            }
            // return redirect()->route("master/distributor");
        }
        echo view("master/v_distributor");
    }

    function save() {
        if (!isset($_POST["save_distributor"])) return redirect()->route("404");
        
        try {
            $client_data = [
                "save_distributor" => true,
                "id_user" => session("joyday")["id_user"],
                "id_distributor" => get_post("id_distributor"),
                "kode_distributor" => get_post("kode_distributor"),
                "nama_distributor" => get_post("nama_distributor"),
                "alamat" => get_post("alamat"),
                "no_hp" => get_post("no_hp"),
                "email" => get_post("email"),
                "keterangan" => get_post("keterangan"),
            ];
            $response = \fetch_post_request("master/distributor/save", $client_data);
            // var_dump($response->getBody());
            if ($response->getStatusCode() !== 200) {
                unset($client_data["id_user"]);
                $client_data["report"] = get_form_report("error", json_decode($response->getBody(), true));
                prepare_flashdata($client_data);
            } else {
                \prepare_flashdata(["report" => \get_form_report("success", "distributor")]);
            }
        } catch (\Exception $e) {
            \prepare_flashdata(["report" => \get_form_report("error", ["global" => "Tejadi kesalahan internal server."])]);
        }
        return redirect()->route("master/distributor");
    }

    // ajax
    function fetch() {
        if ($this->request->isAJAX()) {
            try {
                $response = \fetch_get_request("master/distributor/fetch", ["fetch_distributor" => "ajax", "id_user" => session("joyday")["id_user"], "filter" => get_get("filter")]);
                if ($response->getStatusCode() !== 200) throw new \Exception("invalid response");
                \send_response($response->getBody());
            } catch (\Exception $e) {
                send_500_response(\format_exception($e));
            }
        } 
        
        return redirect()->route("404");
    }
}
