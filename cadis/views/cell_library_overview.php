<?php
/**
 * Created by PhpStorm.
 * User: Ark
 * Date: 5/23/17
 * Time: 2:09 PM
 */
defined('BASEPATH') OR exit('No direct script access allowed');
?><!DOCTYPE html>
<html lang="en">
<head>
    <meta http-equiv="content-type" content="text/html; charset=utf-8"/>
    <title>CADis Data-driven Access Point</title>

    <link rel="stylesheet" type="text/css"
          href="https://cdn.datatables.net/r/bs-3.3.5/jq-2.1.4,dt-1.10.8/datatables.min.css"/>

    <script src="https://cdn.datatables.net/r/bs-3.3.5/jqc-1.11.3,dt-1.10.8/datatables.min.js"></script>
    <script type="text/javascript" charset="utf-8">
        $(document).ready(function () {
            // Setup - add a text input to each footer cell
            $('#cell_table tfoot th').each(function () {
                var title = $(this).text();
                $(this).html('<input style="width:100%" type="text" placeholder=" ' + title + '" />');
            });

            var table = $('#cell_table').DataTable(
                {
                    "search": {"regex": true},
                    "autoWidth": true,
                    "ajax": {
                        "url": "<?php echo $url_table_query_ajax; ?>",
                        "type": "POST",
                        "data": function (d) {
                            return $.extend({}, d, {
                                "table_name": encodeURIComponent($('#table_name_box').val()),
                                "query_conditions": encodeURIComponent($('#query_box').val())
                            });
                        }
                    },
                    columns: [
                        {
                            data: null, render: function (data, type, row) {
                            // change the content of the cell
                            return '<a target="_blank" href="<?php echo $url_show_cell;?>/' + $('#table_name_box').val() + '/' + data.idCELL + '">' + data.idCELL + '</a>';
                        }
                        },
                        {data: 'CELL_PMOS_CNT'},
                        {data: 'CELL_NMOS_CNT'},
                        {data: 'CELL_BSF'},
                        {data: 'CELL_BSF_UNIFIED'},
                        {data: 'CELL_BSF_weak'},
                        {data: 'CELL_BSF_weak_UNIFIED'}
                    ]
                });
            // Apply the search
            table.columns().every(function () {
                var that = this;

                $('input', this.footer()).on('keyup change', function () {
                    if (that.search() !== this.value) {
                        that
                            .search("^" + this.value, true)
                            .draw();
                    }
                });
            });
            $("#id_search_button").click(function () {
                table.ajax.reload();
            });
        });
    </script>
</head>
<body>
<div class="container">
    <form>
        <legend>CADis Cell Library</legend>
        <div style="margin-bottom:50px">
            <div class="form-group">
                <label for="query" class="control-label">Library:</label>
                <input id="table_name_box" type="text" name="table_name" value="WORK_LIB" class="form-control"/>
                <label for="query" class="control-label">Query:</label>
                <input id="query_box" type="text" name="query" value="idCELL!=0" class="form-control"/>
            </div>
            <input type="button" id="id_search_button" value="Search" name="search_button" class="btn btn-primary"/>
        </div>
    </form>
</div>

<div class="container">

    <table id="cell_table" class="table table-striped table-bordered" cellspacing="0" width="100%">
        <thead>
        <tr>
            <th>idCELL</th>
            <th>PMOS</th>
            <th>NMOS</th>
            <th>BSF</th>
            <th>BSF_UNI</th>
            <th>BSF_weak</th>
            <th>BSF_weak_UNI</th>
        </tr>
        </thead>
        <tfoot>
        <tr>
            <th>idCELL</th>
            <th>PMOS</th>
            <th>NMOS</th>
            <th>BSF</th>
            <th>BSF_UNI</th>
            <th>BSF_weak</th>
            <th>BSF_weak_UNI</th>
        </tr>
        </tfoot>
    </table>
</body>
</html>
