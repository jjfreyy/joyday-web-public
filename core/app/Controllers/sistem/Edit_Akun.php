<?php
namespace App\Controllers\Sistem;

use App\Controllers\BaseController;

class Edit_Akun extends BaseController {
    function index() {
      try {
        $response = \fetch_get_request("sistem/edit_akun/fetch", ["fetch_user" => true, "id_user" => session("joyday")["id_user"]]);
        if ($response->getStatusCode() !== 200) throw new \Exception();
        $data = json_decode($response->getBody(), true)[0];
        echo view("sistem/v_edit_akun", $data);
      } catch (\Exception $e) {
        return redirect()->route("404");
      }
    }

    function save() {
      if (!isset($_POST["save_user"])) return redirect()->route("404");
      
      try {
          $client_data = [
            "save_user" => true,
            "id_user" => session("joyday")["id_user"],
            "nama_user" => get_post("nama_user"),
            "username" => get_post("username"),
            "password" => get_post("password"),
            "confirm_password" => get_post("confirm_password"),
            "no_hp" => get_post("no_hp"),
            "email" => get_post("email"),
            "keterangan" => get_post("keterangan"),
          ];
          $response = \fetch_post_request("sistem/edit_akun/save", $client_data);
          // var_dump($client_data);
          // print_r($response->getBody());
          if ($response->getStatusCode() !== 200) {
            unset($client_data["id_user"]);
            $client_data["report"] = get_form_report("error", json_decode($response->getBody(), true));
            prepare_flashdata($client_data);
          } else {  
            $data = json_decode($response->getBody());
            $this->session->set("joyday", [
              "id_user" => $data->id_user,
              "nama_user" => $data->nama_user, 
              "username" => $data->username, 
              "no_hp_user" => $data->no_hp, 
              "email_user" => $data->email, 
              "id_level_user" => $data->id_level, 
              "nama_level" => $data->nama_level, 
            ]);
            var_dump(session("joyday"));
            \prepare_flashdata(["report" => \get_form_report("success", "user")]);
          }
      } catch (\Exception $e) {
          \prepare_flashdata(["report" => \get_form_report("error", ["global" => "Tejadi kesalahan internal server."])]);
      }
      return redirect()->route("sistem/edit_akun");
  }

}