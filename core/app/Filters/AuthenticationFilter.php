<?php
namespace App\Filters;

use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;
use CodeIgniter\Filters\FilterInterface;

class AuthenticationFilter implements FilterInterface {
    function before(RequestInterface $request, $arguments = null) {
        helper(["client", "validations", "utils"]);
        if (!check_session()) 
        return redirect()->route("login");
    }
    
    function after(RequestInterface $request, ResponseInterface $response, $arguments = null) {

    }
}
