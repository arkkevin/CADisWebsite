<?php
/**
 * Created by PhpStorm.
 * User: Ark
 * Date: 5/23/17
 * Time: 4:07 PM
 */
defined('BASEPATH') OR exit('No direct script access allowed');
?><!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">

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
    <style>
        .links line {
            stroke: #999;
            stroke-opacity: 0.6;
        }

        .nodes circle {
            stroke: #fff;
            stroke-width: 1.5px;
        }
    </style>
    <svg class="well" width="960" height="600"></svg>
    <script src="https://d3js.org/d3.v4.min.js"></script>
    <script>
        var svg = d3.select("svg"),
            width = +svg.attr("width"),
            height = +svg.attr("height");
        var r = 5;

        var color = d3.scaleOrdinal(d3.schemeCategory20);

        var simulation = d3.forceSimulation()
            .force("link", d3.forceLink().id(function (d) {
                return d.id;
            }).strength(function (d) {
                if (d.source.id.includes("M") && d.target.id.includes("M")) return 1;
                return 0.1;
            }))
            .force("charge", d3.forceManyBody())
            .force("center", d3.forceCenter(width / 2, height / 2));

        d3.json("<?php echo $url_get_cell_netlist . '/' . $table_name . '/' . $id_cell;?>", function (error, graph) {
            if (error) throw error;

            var link = svg.append("g")
                .attr("class", "links")
                .selectAll("line")
                .data(graph.links)
                .enter().append("line")
                .attr("stroke-width", function (d) {
                    return Math.sqrt(d.value);
                });

            var node = svg
                .selectAll("circle")
                .data(graph.nodes)
                .enter()
                .append("g")
                .attr("class", "nodes")
                .on("dblclick", dblclick)
                .call(d3.drag()
                    .on("start", dragstarted)
                    .on("drag", dragged)
                    .on("end", dragended));

            node.append("circle")
                .attr("r", r)
                .attr("fill", function (d) {
                    if (d.id.includes('g'))
                        return 'red';
                    return color(d.group);
                })

            /*
             node.append("image")
             .attr("xlink:href", "https://github.com/favicon.ico")
             .attr("x", -8)
             .attr("y", -8)
             .attr("width", 16)
             .attr("height", 16);
             */

            node.append("text")
                .attr("dx", 12)
                .attr("dy", ".35em")
                .text(function (d) {
                    return d.id
                });

            simulation
                .nodes(graph.nodes)
                .on("tick", ticked);

            simulation.force("link")
                .links(graph.links);

            function ticked() {
                link
                    .attr("x1", function (d) {
                        return d.source.x;
                    })
                    .attr("y1", function (d) {
                        return d.source.y;
                    })
                    .attr("x2", function (d) {
                        return d.target.x;
                    })
                    .attr("y2", function (d) {
                        return d.target.y;
                    });

                node
                    .attr("transform", function (d) {
                        d.x = Math.max(r, Math.min(width - r, d.x));
                        d.y = Math.max(r, Math.min(height - r, d.y));
                        return "translate(" + d.x + "," + d.y + ")";
                    });
            }

        });


        function dblclick(d) {
            d.fx = null;
            d.fy = null;
            d3.select(this).select("circle").style("fill", function (d) {
                if (d.id.includes('g'))
                    return 'red';
                return color(d.group);
            });
        }

        function dragstarted(d) {
            if (!d3.event.active) simulation.alphaTarget(0.3).restart();
            d.fx = d.x;
            d.fy = d.y;
        }

        function dragged(d) {
            d.fx = d3.event.x;
            d.fy = d3.event.y;
            d3.select(this).classed("fixed", d.fixed = true);
        }

        function dragended(d) {
            if (!d3.event.active) simulation.alphaTarget(0);
            //d.fx = null;
            //d.fy = null;
            d3.select(this).select("circle").style("fill", "black");
        }
    </script>
</head>
<body>
<div class="container" style="margin-bottom: 50px">
    <span style="color: #3366cc;">Blue: NMOS </span>
    <span style="color: #ff9900;">Orange: PMOS </span>
    <span style="color: #dc3912;">Red: Gate of a transistor</span>
    Black: Fixed node caused by dragging, double click to release<br>
    <br>
    Cell characteristics: <a target="_blank"
                             href="<?php
                             if ($id_cell == '16820'
                                 || $id_cell == '25666'
                                 || $id_cell == '37894'
                                 || $id_cell == '37895'
                                 || $id_cell == '37896'
                                 || $id_cell == '37897'
                             ) {

                                 echo $url_sizing_options . '/' . 'SAMPLE_PERFORMANCE' . '/' . $id_cell;
                             } else {
                                 echo $url_sizing_options . '/' . 'GENG_PERFORMANCE' . '/' . $id_cell;
                             }
                             ?>">Show
        all sizing options</a>
    <br>
    <?php
    echo 'CELL_NETLIST:' . $netlist . '<br>';
    echo 'CELL_BSF:' . $bsf . '<br>';
    echo 'CELL_BSF_weak:' . $bsf_weak . '<br>';
    ?>
</div>
</body>
</html>
