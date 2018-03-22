<?php
/**
 * Created by PhpStorm.
 * User: Ark
 * Date: 3/8/18
 * Time: 11:28 PM
 */

defined('BASEPATH') OR exit('No direct script access allowed');
class Logout extends CI_Controller {
    public function __construct() {
        parent::__construct();
        session_start();
    }

    public function index() {
        session_destroy();
        $this->load->helper('url');
        $home_page_url = site_url('login');
        redirect($home_page_url);
    }
}
