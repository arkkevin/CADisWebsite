<?php
/**
 * Created by PhpStorm.
 * User: Ark
 * Date: 2/26/18
 * Time: 9:59 PM
 */
defined('BASEPATH') OR exit('No direct script access allowed');
?><!DOCTYPE html>
<html lang="en">
<head>
    <!-- Global site tag (gtag.js) - Google Analytics -->
    <script async src="https://www.googletagmanager.com/gtag/js?id=UA-112994406-2"></script>
    <script>
        window.dataLayer = window.dataLayer || [];
        function gtag(){dataLayer.push(arguments);}
        gtag('js', new Date());

        gtag('config', 'UA-112994406-2');
    </script>

    <meta charset="utf-8">
    <title>Cell defect view</title>
    <script src="//code.jquery.com/jquery-1.12.0.min.js"></script>
    <script src="//code.jquery.com/jquery-migrate-1.2.1.min.js"></script>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css"
          integrity="sha384-BVYiiSIFeK1dGmJRAkycuHAHRg32OmUcww7on3RYdg4Va+PmSTsz/K68vbdEjh4u" crossorigin="anonymous">

    <!-- Optional theme -->
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap-theme.min.css"
          integrity="sha384-rHyoN1iRsVXV4nD0JutlnGaslCJuC7uwjduW9SVrLvRYooPp2bWYgmgJQIXwl/Sp" crossorigin="anonymous">

    <!-- Latest compiled and minified JavaScript -->
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"
            integrity="sha384-Tc5IQib027qvyjSMfHjOMaLkfuWVxZxUPnCJA7l2mCWNIpG9mGCD8wGNIcPD7Txa"
            crossorigin="anonymous"></script>
</head>
<body>
<div class="container" style="margin-bottom: 50px; margin-top: 50px; width: 90%;">
    <legend class="text-center">Cell <?php echo $fault_type;?> soft defects</legend>
    <table class="table table-striped table-bordered" cellspacing="0" width="100%">
        <thead>
        <tr>
            <th class="text-center">Fault description</th>
            <th class="text-center">Faulty netlist</th>
            <th class="text-center">Faulty BSF</th>
            <th class="text-center">Number of faulty vectors</th>
        </tr>
        </thead>
        <?php
        echo '<br>';
        foreach($data as $one_fault) {
            echo '<tr>';
            echo '<td class="text-center" style="vertical-align: middle">'. $one_fault['FAULT_DESC'] .'</td>';
            $netlist = str_replace("\n", "<br>", $one_fault['FAULTY_NETLIST']);
            $netlist = str_replace("VDD", "-VDD-", $netlist);
            $netlist = str_replace("GND", "-GND-", $netlist);
            echo '<td class="text-center" style="vertical-align: middle"><span style="font-family: '.'Lucida Console'.'">'. $netlist .'</span></td>';
            echo '<td class="text-center" style="vertical-align: middle">'. $one_fault['FAULTY_BSF'] .'</td>';
            echo '<td class="text-center" style="vertical-align: middle">'. $one_fault['FAULTY_VEC_CNT'] .'</td>';
            echo '</tr>';
        }
        ?>
    </table>
</div>
</body>
</html>
