<?php
namespace App\Controllers;

class Unknown extends BaseController {
    function index() {
        $data["message"] = "
        <h1>Oops. . .</h1>
        <p>Halaman yang anda cari tidak dapat ditemukan.</p>
        " .anchor("", "Kembali ke halaman utama.");

        echo view("errors/error_page", $data);
    }
}