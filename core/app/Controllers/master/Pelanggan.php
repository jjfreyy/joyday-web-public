<?php
namespace App\Controllers\Master;

use App\Controllers\BaseController;

class Pelanggan extends BaseController {
    function index() {
        if (!check_privileges("PEL-I")) return redirect()->route("404");
        if (!is_empty(get_get("id"))) {
            try {
                $response = \fetch_get_request("master/pelanggan/fetch", ["fetch_pelanggan" => "edit", "id_user" => session("joyday")["id_user"], "id_pelanggan" => get_get("id")]);
                if ($response->getStatusCode() !== 200) throw new \Exception($response->getBody());
                if (count(json_decode($response->getBody())) === 1) {
                    $data = json_decode($response->getBody())[0];
                    $client_data = [
                        "id_level" => $data->id_level,
                        "id_agen" => $data->id_agen,
                        "agen" => is_empty($data->id_agen) ? "" : $data->kode_agen. " / " .$data->nama_agen,
                        "id_pelanggan" => $data->id_pelanggan,
                        "kode_pelanggan" => explode("-", $data->kode_pelanggan)[1],
                        
                        "nama_pelanggan" => $data->nama_pelanggan,
                        "no_identitas" => $data->no_identitas,
                        "no_hp1" => $data->no_hp1,
                        "no_hp2" => $data->no_hp2,
                        "email" => $data->email,
                        
                        "id_propinsi" => $data->id_propinsi,
                        "nama_propinsi" => $data->nama_propinsi,
                        "id_kabupaten" => $data->id_kabupaten,
                        "nama_kabupaten" => $data->nama_kabupaten,
                        "id_kecamatan" => $data->id_kecamatan,
                        
                        "nama_kecamatan" => $data->nama_kecamatan,
                        "id_kelurahan" => $data->id_kelurahan,
                        "nama_kelurahan" => $data->nama_kelurahan,
                        "alamat" => $data->alamat,
                        "kode_pos" => $data->kode_pos,
                        
                        "keterangan" => $data->keterangan,
                        "daya_listrik" => $data->daya_listrik,
                        "latitude" => $data->latitude,
                        "longitude" => $data->longitude,
                        "nama_kerabat" => $data->nama_kerabat,
                        
                        "no_identitas_kerabat" => $data->no_identitas_kerabat,
                        "no_hp_kerabat" => $data->no_hp_kerabat,
                        "alamat_kerabat" => $data->alamat_kerabat,
                        "hubungan" => $data->hubungan,
                    ];
                    \prepare_flashdata($client_data);
                }
            } catch (\Exception $e) {
                debug_exception($e);
            }
            return redirect()->route("master/pelanggan");
        }
        echo view("master/v_pelanggan");
    }

    function save() {
        if (!isset($_POST["save_pelanggan"])) return redirect()->route("404");
        
        try {
            $client_data = [
                "save_pelanggan" => true,
                "id_user" => session("joyday")["id_user"],
                
                "id_agen" => get_post("id_agen"),
                "agen" => get_post("agen"),
                "id_pelanggan" => get_post("id_pelanggan"),
                "kode_pelanggan" => get_post("kode_pelanggan"),
                "nama_pelanggan" => get_post("nama_pelanggan"),
                
                "no_identitas" => get_post("no_identitas"),
                "no_hp1" => get_post("no_hp1"),
                "no_hp2" => get_post("no_hp2"),
                "email" => get_post("email"),
                "id_propinsi" => get_post("id_propinsi"),
                
                "nama_propinsi" => get_post("nama_propinsi"),
                "id_kabupaten" => get_post("id_kabupaten"),
                "nama_kabupaten" => get_post("nama_kabupaten"),
                "id_kecamatan" => get_post("id_kecamatan"),
                "nama_kecamatan" => get_post("nama_kecamatan"),
                
                "id_kelurahan" => get_post("id_kelurahan"),
                "nama_kelurahan" => get_post("nama_kelurahan"),
                "alamat" => get_post("alamat"),
                "kode_pos" => get_post("kode_pos"),
                "daya_listrik" => get_post("daya_listrik"),
                
                "keterangan" => get_post("keterangan"),
                "latitude" => get_post("latitude"),
                "longitude" => get_post("longitude"),
                "nama_kerabat" => get_post("nama_kerabat"),
                "no_identitas_kerabat" => get_post("no_identitas_kerabat"),
                
                "no_hp_kerabat" => get_post("no_hp_kerabat"),
                "alamat_kerabat" => get_post("alamat_kerabat"),
                "hubungan" => get_post("hubungan"),
                "id_level" => get_post("id_level"),
            ];
            $response = \fetch_post_request("master/pelanggan/save", $client_data);
            // var_dump($response->getBody());
            if ($response->getStatusCode() !== 200) {
                unset($client_data["id_user"]);
                $client_data["report"] = get_form_report("error", json_decode($response->getBody(), true));
                prepare_flashdata($client_data);
            } else {
                \prepare_flashdata(["report" => \get_form_report("success", "pelanggan")]);
            }
        } catch (\Exception $e) {
            \prepare_flashdata(["report" => \get_form_report("error", ["global" => "Tejadi kesalahan internal server."])]);
        }
        return redirect()->route("master/pelanggan");
    }

    // ajax
    function fetch() {
        if ($this->request->isAJAX()) {
            try {
                $response = \fetch_get_request("master/pelanggan/fetch", ["fetch_pelanggan" => "ajax", "id_user" => session("joyday")["id_user"], "type" => get_get("type"), "filter" => get_get("filter")]);
                if ($response->getStatusCode() !== 200) throw new \Exception("invalid response");
                \send_response($response->getBody());
            } catch (\Exception $e) {
                send_500_response(\format_exception($e));
            }
        } 
        return redirect()->route("404");
    }
}
