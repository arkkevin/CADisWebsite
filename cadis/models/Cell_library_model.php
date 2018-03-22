<?php
/**
 * Created by PhpStorm.
 * User: Ark
 * Date: 5/23/17
 * Time: 2:55 PM
 */
defined('BASEPATH') OR exit('No direct script access allowed');

class Cell_library_model extends CI_Model
{
    public function GetDataWithConditions($table_name, $query_conditions)
    {
        $sql_template = "
        (select short_table.idCELL as idCELL, CELL_BSF_UNIFIED, CELL_PMOS_CNT, CELL_NMOS_CNT, CELL_FAMILY, CELL_BSF_weak_UNIFIED,
ifnull(detectable_short, 0) as detectable_short, short_table.total_short as total_short,
ifnull(imp_short, 0) as imp_short, imp_short_table.total_short as ttss,
ifnull(detectable_open, 0) as detectable_open, total_open
 from 

(SELECT total_shorts.idCELL as idCELL, total_short, detectable_short
FROM 
(SELECT count(*) as total_short, idCELL FROM CADisCMOS.WORK_LIB_FAULT_LIB where FAULT_TYPE='short'
group by idCELL) as total_shorts
left join
(SELECT count(*) as detectable_short, idCELL FROM CADisCMOS.WORK_LIB_FAULT_LIB where FAULT_TYPE='short' and FAULTY_VEC_CNT!=0
group by idCELL) as detectable_shorts
on detectable_shorts.idCELL=total_shorts.idCELL
) as short_table,

(SELECT total_shorts.idCELL as idCELL, total_short, imp_short
FROM 
(SELECT count(*) as total_short, idCELL FROM CADisCMOS.WORK_LIB_FAULT_LIB where FAULT_TYPE='short'
group by idCELL) as total_shorts
left join
(SELECT count(*) as imp_short, idCELL FROM CADisCMOS.WORK_LIB_FAULT_LIB where FAULT_TYPE='short' and FAULTY_BSF like BINARY '%r%'
group by idCELL) as imp_shorts
on imp_shorts.idCELL=total_shorts.idCELL
) as imp_short_table,

(SELECT total_opens.idCELL as idCELL, total_open, detectable_open
FROM 
(SELECT count(*) as total_open, idCELL FROM CADisCMOS.WORK_LIB_FAULT_LIB where FAULT_TYPE='open'
group by idCELL) as total_opens
left join
(SELECT count(*) as detectable_open, idCELL FROM CADisCMOS.WORK_LIB_FAULT_LIB where FAULT_TYPE='open' and FAULTY_VEC_CNT!=0
group by idCELL) as detectable_opens
on detectable_opens.idCELL=total_opens.idCELL
) as open_table,

CADisCMOS.WORK_LIB
where short_table.idCELL=open_table.idCELL
and short_table.idCELL=imp_short_table.idCELL
and WORK_LIB.idCELL=short_table.idCELL
) as result_table
        ";
        $sql_template = str_replace("WORK_LIB", $table_name, $sql_template);
        $sql = "SELECT idCELL, CELL_BSF_UNIFIED
, CELL_PMOS_CNT, CELL_NMOS_CNT, CELL_FAMILY
, CELL_BSF_weak_UNIFIED, detectable_short, imp_short, total_short, detectable_open, total_open FROM ".$sql_template." WHERE ".$query_conditions;
        $query = $this->db->query($sql);
        if (!$query) {
            return 0;
        } else {
            return $query->result_array();
        }
    }

    public function GetCellDetail($table_name, $id_cell) {
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

    public function GetCellDefectDetail($table_name, $id_cell, $fault_type) {
        $sql = "SELECT FAULT_DESC, FAULTY_NETLIST, FAULTY_BSF, FAULTY_VEC_CNT FROM ".$table_name."_FAULT_LIB WHERE idCELL=? AND FAULT_TYPE=? ORDER BY FAULTY_VEC_CNT";
        $query = $this->db->query($sql, array((int)$id_cell, $fault_type));
        if (!$query) {
            return 0;
        } else {
            return $query->result_array();
        }
    }
}
