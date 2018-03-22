<?php
/**
 * Created by PhpStorm.
 * User: Ark
 * Date: 5/23/17
 * Time: 2:13 PM
 */
defined('BASEPATH') OR exit('No direct script access allowed');

class Cell_library extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();
        session_start();
    }

    private function check_login() {
        if (!isset($_SESSION['iduser_info'])) {
            $this->load->helper('url');
            $data['url_login'] = site_url('login');
            $this->load->view('login_view', $data);
            return true;
        }
        return false;
    }

    public function show_table()
    {
        if ($this->check_login()) return;
        $this->load->helper('url');
        $data['url_table_query_ajax'] = site_url('cell_library/table_query_ajax');
        $data['url_show_cell'] = site_url('cell_library/show_cell');
        $data['url_show_cell_defect'] = site_url('cell_library/show_cell_defect');
        $this->load->view('cell_library_overview', $data);
    }

    public function show_cell_defect($table_name, $id_cell, $fault_type)
    {
        if ($this->check_login()) return;
        $this->load->helper('url');
        $data['table_name'] = $table_name;
        $data['id_cell'] = $id_cell;

        $this->load->model('Cell_library_model');
        $ret = $this->Cell_library_model->GetCellDefectDetail($table_name, $id_cell, $fault_type);
        if ($ret != 0 && sizeof($ret) != 0) {
            $data['data'] = $ret;
        } else {
            $data['data'] = array();
        }
        $data['fault_type'] = $fault_type;
        $this->load->view('cell_defect_view', $data);
    }

    public function show_cell($table_name, $id_cell)
    {
        if ($this->check_login()) return;
        $this->load->helper('url');
        $data['url_get_cell_netlist'] = site_url('cell_library/get_cell_netlist');
        $data['url_sizing_options'] = site_url('characteristics/sizing_options');
        $data['table_name'] = $table_name;
        $data['id_cell'] = $id_cell;

        $this->load->model('Cell_library_model');
        $ret = $this->Cell_library_model->GetCellDetail($table_name, $id_cell);
        if ($ret == 0) {
            // no cell found or database error
            $data['netlist'] = 'No data';
            $data['bsf'] = 'No data';
            $data['bsf_weak'] = 'No data';
        } else {
            $data['netlist'] = $ret['CELL_NETLIST'];
            $data['bsf'] = $ret['CELL_BSF'];
            $data['bsf_weak'] = $ret['CELL_BSF_weak'];
        }
        $this->load->view('cell_overview', $data);
    }

    public function get_cell_netlist($table_name, $id_cell)
    {
        $this->load->model('Cell_library_model');
        $ret = $this->Cell_library_model->GetCellDetail($table_name, $id_cell);
        $netlist_data = array();
        $node_arr = array();
        $link_arr = array();
        if ($ret == 0) {
            // no netlist found or database error
            $temp_node['id'] = 'No data';
            $temp_node['group'] = 1;
            array_push($node_arr, $temp_node);
        } else {
            $netlist = str_replace("\n", ' ', $ret['CELL_NETLIST']);
            $pieces = explode(" ", trim($netlist));
            for ($i = 0; $i < count($pieces); $i = $i + 6) {
                foreach (array("s", "g", "d") as $terminal) {
                    $temp_node['id'] = $pieces[$i] . $terminal;
                    if ($pieces[$i + 5] == "NMOS") {
                        $temp_node['group'] = 1;
                    } else {
                        $temp_node['group'] = 2;
                    }
                    array_push($node_arr, $temp_node);
                }

                $temp_link['source'] = $pieces[$i] . 'g';
                $temp_link['target'] = $pieces[$i] . 's';
                $temp_link['value'] = 1;
                array_push($link_arr, $temp_link);
                $temp_link['source'] = $pieces[$i] . 'g';
                $temp_link['target'] = $pieces[$i] . 'd';
                $temp_link['value'] = 1;
                array_push($link_arr, $temp_link);
                $temp_link['source'] = $pieces[$i] . 's';
                $temp_link['target'] = $pieces[$i] . 'd';
                $temp_link['value'] = 1;
                array_push($link_arr, $temp_link);

                $temp_node['id'] = $pieces[$i + 1];
                $temp_node['group'] = 3;
                $temp_link['source'] = $pieces[$i] . 's';
                $temp_link['target'] = $pieces[$i + 1];
                $temp_link['value'] = 1;
                array_push($link_arr, $temp_link);
                if (!in_array($temp_node, $node_arr)) {
                    array_push($node_arr, $temp_node);
                }
                $temp_node['id'] = $pieces[$i + 2];
                $temp_node['group'] = 3;
                $temp_link['source'] = $pieces[$i] . 'g';
                $temp_link['target'] = $pieces[$i + 2];
                $temp_link['value'] = 1;
                if (!in_array($temp_node, $node_arr)) {
                    array_push($node_arr, $temp_node);
                }
                array_push($link_arr, $temp_link);
                $temp_node['id'] = $pieces[$i + 3];
                $temp_node['group'] = 3;
                $temp_link['source'] = $pieces[$i] . 'd';
                $temp_link['target'] = $pieces[$i + 3];
                $temp_link['value'] = 1;
                if (!in_array($temp_node, $node_arr)) {
                    array_push($node_arr, $temp_node);
                }
                array_push($link_arr, $temp_link);

            }
        }
        $netlist_data['nodes'] = $node_arr;
        $netlist_data['links'] = $link_arr;
        $data['data'] = $netlist_data;
        $this->load->view('send_json', $data);
    }

    public
    function table_query_ajax()
    {
        $table_name = urldecode($_POST['table_name']);
        $query_conditions = urldecode($_POST['query_conditions']);
        if (empty($query_conditions)) {
            $query_conditions = "idCELL!=0";
        }
        $this->load->model('Cell_library_model');
        $ret = $this->Cell_library_model->GetDataWithConditions($table_name, $query_conditions);
        if ($ret != 0 && sizeof($ret) != 0) {
            $result_temp['data'] = $ret;
        } else {
            $result_temp['data'] = array();
        }
        $data['data'] = $result_temp;
        $this->load->view('send_json', $data);
    }
}
