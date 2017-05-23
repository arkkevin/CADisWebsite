<?php
/**
 * Created by PhpStorm.
 * User: Ark
 * Date: 5/16/17
 * Time: 8:02 PM
 */
defined('BASEPATH') OR exit('No direct script access allowed');
?><!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>Characteristics</title>
    <!-- Latest compiled and minified CSS -->
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css" integrity="sha384-BVYiiSIFeK1dGmJRAkycuHAHRg32OmUcww7on3RYdg4Va+PmSTsz/K68vbdEjh4u" crossorigin="anonymous">

    <!-- Optional theme -->
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap-theme.min.css" integrity="sha384-rHyoN1iRsVXV4nD0JutlnGaslCJuC7uwjduW9SVrLvRYooPp2bWYgmgJQIXwl/Sp" crossorigin="anonymous">

    <!-- Latest compiled and minified JavaScript -->
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js" integrity="sha384-Tc5IQib027qvyjSMfHjOMaLkfuWVxZxUPnCJA7l2mCWNIpG9mGCD8wGNIcPD7Txa" crossorigin="anonymous"></script>
</head>
<body>
<?php
// pre-processing data
$propagation_delay_dict = array();
$input_array = array();
$input_act_array = array();
$input_slew_array = array();
$output_load_array = array();

for ($i=0; $i<=3; $i++) {
    array_push($input_act_array, array());
}
foreach ($ret as $row) {
    if ($row['PROPAGATION_DELAY'] == 'None') continue;
    $act = $row['INPUT_RF'].' ('.$row['OTHER_INPUTS'].')';
    $key = $row['INPUT_NUM'].$act.$row['INPUT_SLEW'].$row['OUTPUT_LOAD'];
    $propagation_delay_dict[$key] = $row['PROPAGATION_DELAY'];
    $transition_delay_dict[$key] = $row['OUTPUT_TRANSITION'];
    $dynamic_power_dict[$key] = $row['POWER_CONS'];
    if (!in_array($act, $input_act_array[$row['INPUT_NUM']])) {
        array_push($input_act_array[$row['INPUT_NUM']], $act);
    }

    if (!in_array($row['INPUT_NUM'], $input_array)) {
        array_push($input_array, $row['INPUT_NUM']);
    }
    if (!in_array($row['INPUT_SLEW'], $input_slew_array)) {
        array_push($input_slew_array, $row['INPUT_SLEW']);
    }
    if (!in_array($row['OUTPUT_LOAD'], $output_load_array)) {
        array_push($output_load_array, $row['OUTPUT_LOAD']);
    }
}
?>
<div style="margin-top: 50px" class="container">
    <table class="table table-striped table-bordered" >
        <tr>
            <th class="text-center" colspan="<?php echo 2+sizeof($input_slew_array)*sizeof($output_load_array);?>">Propagation Delay [s]</th>
        </tr>
        <tr>
            <th class="text-center" colspan="2">Input Transition [ps]</th>
            <?php
            foreach ($input_slew_array as $input_slew) {
                echo '<th class="text-center" colspan="'.sizeof($output_load_array).'">'.$input_slew.'</th>';
            }
            ?>
        </tr>
        <tr>
            <th class="text-center" colspan="2">Output Load [INV]</th>
            <?php
            foreach ($input_slew_array as $input_slew) {
                foreach ($output_load_array as $output_load) {
                    echo '<th class="text-center">' . $output_load . 'X</th>';
                }
            }
            ?>
        </tr>
        <?php
        foreach ($input_array as $input) {
            $is_first = true;
            foreach ($input_act_array[$input] as $act) {
                echo '<tr>';
                if ($is_first) {
                    $is_first = false;
                    echo '<th style="vertical-align: middle" class="text-center" rowspan="'.sizeof($input_act_array[$input]).'">In'.$input.' to Out</th>';
                }
                echo '<th class="text-center">'.$act.'</th>';
                for ($j=0; $j<(sizeof($input_slew_array)); $j++) {
                    for ($k=0; $k<(sizeof($output_load_array)); $k++) {
                        $key = $input . $act . $input_slew_array[$j] . $output_load_array[$k];
                        echo '<td>' . $propagation_delay_dict[$key] . '</td>';
                    }
                }
                echo '</tr>';
            }
        }
        ?>
    </table>
</div>

<div style="margin-top: 50px" class="container">
    <table class="table table-striped table-bordered" >
        <tr>
            <th class="text-center" colspan="<?php echo 2+sizeof($input_slew_array)*sizeof($output_load_array);?>">Output Transition [s]</th>
        </tr>
        <tr>
            <th class="text-center" colspan="2">Input Transition [ps]</th>
            <?php
            foreach ($input_slew_array as $input_slew) {
                echo '<th class="text-center" colspan="'.sizeof($output_load_array).'">'.$input_slew.'</th>';
            }
            ?>
        </tr>
        <tr>
            <th class="text-center" colspan="2">Output Load [INV]</th>
            <?php
            foreach ($input_slew_array as $input_slew) {
                foreach ($output_load_array as $output_load) {
                    echo '<th class="text-center">' . $output_load . 'X</th>';
                }
            }
            ?>
        </tr>
        <?php
        foreach ($input_array as $input) {
            $is_first = true;
            foreach ($input_act_array[$input] as $act) {
                echo '<tr>';
                if ($is_first) {
                    $is_first = false;
                    echo '<th style="vertical-align: middle" class="text-center" rowspan="'.sizeof($input_act_array[$input]).'">In'.$input.' to Out</th>';
                }
                echo '<th class="text-center">'.$act.'</th>';
                for ($j=0; $j<(sizeof($input_slew_array)); $j++) {
                    for ($k=0; $k<(sizeof($output_load_array)); $k++) {
                        $key = $input . $act . $input_slew_array[$j] . $output_load_array[$k];
                        echo '<td>' . $transition_delay_dict[$key] . '</td>';
                    }
                }
                echo '</tr>';
            }
        }
        ?>
    </table>
</div>

<div style="margin-top: 50px; margin-bottom: 100px" class="container">
    <table class="table table-striped table-bordered" >
        <tr>
            <th class="text-center" colspan="<?php echo 2+sizeof($input_slew_array)*sizeof($output_load_array);?>">Dynamic Power Consumption [W]</th>
        </tr>
        <tr>
            <th class="text-center" colspan="2">Input Transition [ps]</th>
            <?php
            foreach ($input_slew_array as $input_slew) {
                echo '<th class="text-center" colspan="'.sizeof($output_load_array).'">'.$input_slew.'</th>';
            }
            ?>
        </tr>
        <tr>
            <th class="text-center" colspan="2">Output Load [INV]</th>
            <?php
            foreach ($input_slew_array as $input_slew) {
                foreach ($output_load_array as $output_load) {
                    echo '<th class="text-center">' . $output_load . 'X</th>';
                }
            }
            ?>
        </tr>
        <?php
        foreach ($input_array as $input) {
            $is_first = true;
            foreach ($input_act_array[$input] as $act) {
                echo '<tr>';
                if ($is_first) {
                    $is_first = false;
                    echo '<th style="vertical-align: middle" class="text-center" rowspan="'.sizeof($input_act_array[$input]).'">In'.$input.' to Out</th>';
                }
                echo '<th class="text-center">'.$act.'</th>';
                for ($j=0; $j<(sizeof($input_slew_array)); $j++) {
                    for ($k=0; $k<(sizeof($output_load_array)); $k++) {
                        $key = $input . $act . $input_slew_array[$j] . $output_load_array[$k];
                        echo '<td>' . $dynamic_power_dict[$key] . '</td>';
                    }
                }
                echo '</tr>';
            }
        }
        ?>
    </table>
</div>
</body>
</html>
