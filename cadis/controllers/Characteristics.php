<?php
/**
 * Created by PhpStorm.
 * User: Ark
 * Date: 5/16/17
 * Time: 7:54 PM
 */
defined('BASEPATH') OR exit('No direct script access allowed');

class Characteristics extends CI_Controller {
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
    public function sizing_options($database, $id_cell) {
        if ($this->check_login()) return;
        $this->load->helper('url');
        $data['url_characteristics'] = site_url('characteristics/performance_table');
        $this->load->model('Performance_model');
        $ret = $this->Performance_model->GetDistinctPerformanceSizeForCell($database, $id_cell);
        $data['ret'] = $ret;
        $data['id_cell'] = $id_cell;
        $data['database'] = $database;
        $this->load->view('cell_sizes_view', $data);
    }

    public function performance_table($database, $id_cell, $str_size) {
        if ($this->check_login()) return;
        $this->load->model('Performance_model');
        $ret = $this->Performance_model->GetPerformanceDataForCellNSize($database, $id_cell, $str_size);
        $input_slew_array = array();
        $output_load_array = array();

        foreach ($ret as $row) {
            if (!in_array($row['INPUT_SLEW'], $input_slew_array)) {
                array_push($input_slew_array, $row['INPUT_SLEW']);
            }
            if (!in_array($row['OUTPUT_LOAD'], $output_load_array)) {
                array_push($output_load_array, $row['OUTPUT_LOAD']);
            }
        }
        $data['input_slew_array'] = $input_slew_array;
        $data['output_load_array'] = $output_load_array;
        $data['ret'] = $ret;
        $this->load->view('characteristics_tables_view', $data);
    }
}
