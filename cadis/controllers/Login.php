<?php
/**
 * Created by PhpStorm.
 * User: Ark
 * Date: 3/8/18
 * Time: 11:06 PM
 */

defined('BASEPATH') OR exit('No direct script access allowed');
class Login extends CI_Controller {
    public function __construct() {
        parent::__construct();
        session_start();
    }

    public function index() {
        $username = "";
        $password = "";
        $errmsg = "";

        $this->load->helper('url');
        $home_page_url = site_url('cell_library/show_table');
        $data['url_home'] = site_url('cell_library/show_table');
        $data['url_login'] = site_url('login');

        if (!isset($_POST['username'])) {
            $errmsg = 'Invalid login un';
        } else {
            $username = $this->input->post('username');
        }

        if (!isset($_POST['password'])) {
            $errmsg = 'Invalid login pa';
        } else {
            $password = $this->input->post('password');
        }

        if (strlen($username) > 0 && strlen($password) > 0) {
            $errmsg = "";

            $this->load->model('user_info_model');
            if ($res = $this->user_info_model->getUserInfo($username, $password)) {

                $_SESSION['iduser_info'] = $res['iduser_info'];
            } else {
                $errmsg = 'Invalid login dbe';
            }
        }

        if (strlen($errmsg) > 0) {
            $data['emp'] = '';
            $data['errmsg'] = $errmsg;
            $this->load->view('login_view', $data);
        } else {
            $this->load->helper('url');
            redirect($home_page_url);
        }
    }
}
