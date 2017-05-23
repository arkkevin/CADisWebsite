<?php
/**
 * Created by PhpStorm.
 * User: Ark
 * Date: 5/16/17
 * Time: 7:54 PM
 */
defined('BASEPATH') OR exit('No direct script access allowed');

class Characteristics extends CI_Controller {
    public function index($id_cell) {
        $this->load->helper('url');
        $data['url_characteristics'] = site_url('characteristics/performance_table');
        $this->load->model('Performance_model');
        $ret = $this->Performance_model->GetDistinctPerformanceSizeForCell($id_cell);
        $data['ret'] = $ret;
        $data['id_cell'] = $id_cell;
        $this->load->view('cell_overview', $data);
    }

    public function performance_table($id_cell, $str_size) {
        $this->load->model('Performance_model');
        $ret = $this->Performance_model->GetPerformanceDataForCellNSize($id_cell, $str_size);
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
        $this->load->view('characteristics_tables', $data);
    }
}
