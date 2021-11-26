<?php
namespace App\Controllers;

class Dashboard extends BaseController {
    function index() {
        echo view("v_dashboard");
    }
}
