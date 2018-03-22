<?php
/**
 * Created by PhpStorm.
 * User: Ark
 * Date: 5/16/17
 * Time: 10:58 PM
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
    <title>Cell Sizing options</title>
    <!-- Latest compiled and minified CSS -->
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css" integrity="sha384-BVYiiSIFeK1dGmJRAkycuHAHRg32OmUcww7on3RYdg4Va+PmSTsz/K68vbdEjh4u" crossorigin="anonymous">

    <!-- Optional theme -->
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap-theme.min.css" integrity="sha384-rHyoN1iRsVXV4nD0JutlnGaslCJuC7uwjduW9SVrLvRYooPp2bWYgmgJQIXwl/Sp" crossorigin="anonymous">

    <!-- Latest compiled and minified JavaScript -->
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js" integrity="sha384-Tc5IQib027qvyjSMfHjOMaLkfuWVxZxUPnCJA7l2mCWNIpG9mGCD8wGNIcPD7Txa" crossorigin="anonymous"></script>
</head>
<body>
<div style="margin-top: 50px" class="container">
    <table class="table table-striped table-bordered" >
        <tr>
            <th class="text-center" colspan="10">Sizes</th>
        </tr>
        <?php
        $cnt = 0;
        foreach ($ret as $size) {
            if ($cnt == 0) {
                echo '<tr>';
            }
            echo '<td>';
            echo '<a href="'.$url_characteristics.'/'.$database.'/'.$id_cell.'/'.$size['SIZE_DESC'].'">'.$size['SIZE_DESC'].'</a>';
            echo '</td>';
            $cnt++;
            if ($cnt == 10) {
                echo '</tr>';
                $cnt = 0;
            }
        }
        ?>
    </table>
</div>
</body>
</html>
