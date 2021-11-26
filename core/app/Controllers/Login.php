<?php 
namespace App\Controllers;

class Login extends BaseController {
    function index() {
        echo view("v_login");
    }

    function process() {
        if (!isset($_POST["login_btn"])) return redirect()->route("404");

        try {
            $client_data = [
                "login" => true,
                "username" => get_post("username"),
                "password" => get_post("password", false),
            ];
            $response = \fetch_post_request("authentication/login", $client_data);
            if ($response && $response->getStatusCode() !== 200) {
                $this->session->setFlashdata("report", \get_form_report("error", [json_decode($response->getBody())->message]));
                return redirect()->to(base_url("login"));
            } 
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
            return redirect()->route("/"); 
        } catch (\Exception $e) {
            echo \format_exception($e);
            \prepare_flashdata(["report" => \get_form_report("error", ["message" => "Tejadi kesalahan internal server."])]);
            return redirect()->route("login");
        }
    }
}
