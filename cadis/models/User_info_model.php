<?php
/**
 * Created by PhpStorm.
 * User: Ark
 * Date: 3/8/18
 * Time: 11:13 PM
 */

defined('BASEPATH') OR exit('No direct script access allowed');

class User_info_model extends CI_Model {
    public function getUserInfo($un, $pw) {
        $res['emp'] = '';
        $pw = sha1($pw);
        $this->db->where('User_Info_username', $un);
        $this->db->where('User_Info_password', $pw);
        $query = $this->db->get('User_Info');

        if (!$query) {
            return 0;
        } elseif ($query->num_rows() == 1) {
            $row = $query->row_array();

            $res['iduser_info'] = $row['idUser_Info'];
            return $res;
        } else {
            //echo $this->db->last_query();
            return 0;
        }
    }
}
