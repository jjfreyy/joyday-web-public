<?php
namespace App\Controllers\Sistem;

use App\Controllers\BaseController;

class Tambah_User extends BaseController {
    function index() {
      if (!\check_privileges("USR-I")) return redirect()->route("404");
      echo view("sistem/v_tambah_user");
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
              "id_level" => get_post("id_level"),
          ];
          $response = \fetch_post_request("sistem/tambah_user/save", $client_data);
        // print_r($response->getBody());
          if ($response->getStatusCode() !== 200) {
              unset($client_data["id_user"]);
              $client_data["report"] = get_form_report("error", json_decode($response->getBody(), true));
              prepare_flashdata($client_data);
          } else {
              \prepare_flashdata(["report" => \get_form_report("success", "user")]);
          }
      } catch (\Exception $e) {
          \prepare_flashdata(["report" => \get_form_report("error", ["global" => "Tejadi kesalahan internal server."])]);
      }
      return redirect()->route("sistem/tambah_user");
  }

}