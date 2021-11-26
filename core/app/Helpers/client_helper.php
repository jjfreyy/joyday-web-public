<?php

function check_privileges($kode_akses) {
    try {
        $response = \fetch_post_request("authentication/check_privileges", ["check_privileges" => true, "id_user" => session("joyday")["id_user"], "kode_akses" => $kode_akses]);
        if ($response->getStatusCode() !== 200) throw new \Exception($response->getBody());
        return true;
    } catch (\Exception $e) {
        return false;
    }
}

function check_session() {
    $joyday = session("joyday");
    if (is_empty($joyday)) return false;
    try {
        $response = fetch_post_request("authentication/check_session", ["check_session" => true, "id_user" => $joyday["id_user"], "username" => $joyday["username"]]);
        if ($response->getStatusCode() !== 200) return false;
    } catch (\Exception $e) {
        echo debug_exception($e);
        return false;
    }
    return true;
}

function fetch_get_request($url, $query = null) {
    try {
        $client = get_client();
        $response = $client->get($url, ["query" => $query]);
        return $response;
    } catch (\Exception $e) {
        echo debug_exception($e);
        return false;
    }
}

function fetch_post_request($url, $form_params = null) {
    try {
        $client = get_client();
        $response = $client->post($url, ["form_params" => $form_params]);
        return $response;
    } catch (\Exception $e) {
        echo debug_exception($e);
        return false;
    }
}

function get_client() {
    $client = new \CodeIgniter\HTTP\CURLRequest(
        new \Config\App(),
        new \CodeIgniter\HTTP\URI(),
        new \CodeIgniter\HTTP\Response(new \Config\App()),
        [
            "baseURI" => get_server_url(),
            "auth" => [
                "a5f563e241e4776cd8117bef5b266caa74af56d50006035d1acb7a01c91c9cd4c85a561c0e38b5a78653a45a9422228e44f6030d926f0c09a67b145e74b5fd00", "3cc1027fdd6043e49880de142ef0a29676dd1f7f11a3b1241f22c971eaf3ea4688763984e5fdc4eadb507a6b750c0eafdee83e1b10365a068450732eea20b480", 
                "basic"
            ],
            "http_errors" => false,
        ]
    );
    return $client;
}

function get_server_url() {
    return "http://localhost/dev/joyday_api/";
}

function load_css($data = null) {
    $path = base_url("src/css");
    $ext = env("CI_ENVIRONMENT") === "development" ? ".css" : ".min.css";
    echo "<link href='$path/base$ext' rel='stylesheet' type='text/css' />";
    echo "<link href='$path/style$ext' rel='stylesheet' type='text/css' />";
    echo "<link href='$path/nav$ext' rel='stylesheet' type='text/css' />";
    
    if (is_empty($data)) return;
    if (!is_array($data)) {
        echo "<link href='$path/$data$ext' rel='stylesheet' type='text/css' />";
        return;
    }

    for($i = 0; $i < count($data); $i++) {
        $file = $data[$i];
        echo "<link href='$path/$file$ext' rel='stylesheet' type='text/css' />";        
    }
}

function load_js($data = null) {
    $path = base_url("src/js");
    $ext = env("CI_ENVIRONMENT") === "development" ? ".js" : ".min.js";
    echo "<script src='$path/lib/jquery-3.5.1.min.js'></script>";
    echo "<script>
    const base_url = '" .base_url(). "/'
    const src_base_url = '" .base_url("src"). "/'
    </script>";
    echo "<script src='$path/script$ext'></script>";
    echo "<script src='$path/nav$ext'></script>";

    if (is_empty($data)) return;
    if (!is_array($data)) {
        echo "<script src='$path/$data$ext'></script>";
        return; 
    } 

    for($i = 0; $i < count($data); $i++) {
        $file = $data[$i];
        echo "<script src='$path/$file$ext'></script>"; 
    }
}

function send_500_response($error = "") {
    return send_response(500, ["message" => is_empty($error) || env("CI_ENVIRONMENT") === "production" ? "Terjadi kesalahan internal server." : $error]);
}

function send_response() {
    $status_code = 200;
    $body = "";

    switch (func_num_args()) {
        case 1: $body = func_get_arg(0); break;
        case 2:
            $status_code = func_get_arg(0);
            $body = func_get_arg(1);
        break;
    }

    $response = \Config\Services::response();
    $response->setStatusCode($status_code);
    $response->setBody($body);
    $response->send();
    die();
}
