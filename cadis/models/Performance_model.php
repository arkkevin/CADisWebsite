<?php
/**
 * Created by PhpStorm.
 * User: Ark
 * Date: 5/16/17
 * Time: 7:35 PM
 */
defined('BASEPATH') OR exit('No direct script access allowed');

class Performance_model extends CI_Model {
    public function GetDistinctPerformanceSizeForCell($database, $id_cell) {
        $sql = "SELECT DISTINCT SIZE_DESC FROM SIZE_LIB, ".$database." WHERE SIZE_LIB.idSIZE_LIB=".$database.".idSIZE_LIB AND idCELL = ?";
        $query = $this->db->query($sql, array((int)$id_cell));
        if (!$query) {
            return 0;
        } else {
            return $query->result_array();
        }
    }

    public function GetPerformanceDataForCellNSize($database, $id_cell, $str_size) {
        $sql = "SELECT * FROM SIZE_LIB WHERE SIZE_DESC = ?";
        $query = $this->db->query($sql, array($str_size));
        if (!$query) {
            return 0;
        } elseif ($query->num_rows() == 1) {
            $row = $query->row_array();
            $id_size_lib = $row['idSIZE_LIB'];
        } else {
            return 0;
        }

        $sql = "SELECT * FROM ".$database." WHERE idCELL = ? AND idSIZE_LIB = ?";
        $query = $this->db->query($sql, array($id_cell, $id_size_lib));
        if (!$query) {
            return 0;
        } else {
            return $query->result_array();
        }
    }
}
