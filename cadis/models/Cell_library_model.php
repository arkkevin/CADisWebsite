<?php
/**
 * Created by PhpStorm.
 * User: Ark
 * Date: 5/23/17
 * Time: 2:55 PM
 */
defined('BASEPATH') OR exit('No direct script access allowed');

class cell_library_model extends CI_Model
{
    public function GetDataWithConditions($table_name, $query_conditions)
    {
        $sql = "SELECT idCELL, CELL_BSF, CELL_BSF_UNIFIED
, CELL_PMOS_CNT, CELL_NMOS_CNT, CELL_BSF_weak
, CELL_BSF_weak_UNIFIED FROM ".$table_name." WHERE ".$query_conditions;
        $query = $this->db->query($sql);
        if (!$query) {
            return 0;
        } else {
            return $query->result_array();
        }
    }
    public function GetCellDetails($table_name, $id_cell) {
        $sql = "SELECT CELL_NETLIST, CELL_BSF, CELL_BSF_weak  FROM ".$table_name." WHERE idCELL=?";
        $query = $this->db->query($sql, array((int)$id_cell));
        if (!$query) {
            return 0;
        } elseif ($query->num_rows() == 1) {
            return $query->row_array();
        } else {
            return 0;
        }
    }
}
