<?php
namespace App\Controllers;

use App\Controllers\BaseController;

class Logout extends BaseController {
  function index() {
    $this->session->destroy();
    return redirect()->route("login");
  }
}
