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
    <!-- Global site tag (gtag.js) - Google Analytics -->
    <script async src="https://www.googletagmanager.com/gtag/js?id=UA-112994406-2"></script>
    <script>
        window.dataLayer = window.dataLayer || [];
        function gtag(){dataLayer.push(arguments);}
        gtag('js', new Date());

        gtag('config', 'UA-112994406-2');
    </script>

    <meta charset="utf-8">
    <title>Cell overview</title>

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
    <script src="https://d3js.org/d3.v4.min.js"></script>
</head>
<body>
<div class="container" style="margin-bottom: 50px; margin-top: 50px; width: 90%;">
    <legend class="text-center">Cell overview</legend>
    <table class="table table-striped table-bordered" cellspacing="0" width="100%">
        <thead>
        <tr>
            <th colspan="2" class="text-center">Cell information</th>
        </tr></thead>
        <tr>
            <td>Cell BSF</td>
            <td><?php echo $bsf;?></td>
        </tr>
        <tr>
            <td>Cell BSF weak</td>
            <td><?php echo $bsf_weak;?></td>
        </tr>
        <tr>
            <td style="vertical-align: middle">Cell netlist</td>
            <td style="font-family: 'Lucida Console'"><?php
                $netlist = str_replace("\n", '<br>', $netlist);
                $netlist = str_replace("VDD", "-VDD-", $netlist);
                $netlist = str_replace("GND", "-GND-", $netlist);
                echo $netlist;
                ?></td>
        </tr>
        <tr>
            <td>Cell characteristics</td>
            <td><a target="_blank"
                             href="<?php
                                 echo $url_sizing_options . '/' . 'GENG_PERFORMANCE' . '/' . $id_cell;
                             ?>">Show
        all sizing options</a></td>
        </tr>
        <tr>
            <td style="vertical-align: middle">
                Cell schematic<br>
                <br>
                Legend<br>
                <span style="color: #3366cc;">Blue: NMOS diffusions</span><br>
                <span style="color: #ff9900;">Orange: PMOS diffusions</span><br>
                <span style="color: #dc3912;">Red: Gate of a transistor</span><br>
                Black: Fixed node caused by dragging<br>double click to release
            </td>
            <td>
                <div id="div_schematic" class="container" style="width: 100%;">
                    <svg id="schematic" class="well" width="100%" height="100%"></svg>
                </div>
            </td>
        </tr>
    </table>
</div>
<script>
    var svg = d3.select("#schematic");
    //var width = +svg.attr("width"),
    var width = $("#div_schematic").width();
    $("#div_schematic").css({'height':width*0.625+'px'});
    var height = $("#div_schematic").height();
    var r = 5;

    var color = d3.scaleOrdinal(d3.schemeCategory20);

    var simulation = d3.forceSimulation()
        .force("link", d3.forceLink().id(function (d) {
            return d.id;
        }).strength(function (d) {
            if (d.source.id.includes("M") && d.target.id.includes("M")) return 2;
            return 0.1;
        }))
        .force("charge", d3.forceManyBody())
        .force("center", d3.forceCenter(width / 2, height / 2));

    var schematic = null;
    link = null;
    node = null;
    d3.json("<?php echo $url_get_cell_netlist . '/' . $table_name . '/' . $id_cell;?>", function (error, graph) {
        if (error) throw error;

        schematic = graph;
        console.log(graph);
        link = svg.append("g")
            .attr("class", "links")
            .selectAll("line")
            .data(graph.links)
            .enter().append("line")
            .attr("stroke-width", function (d) {
                return Math.sqrt(d.value);
            });

        node = svg
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
            });

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

    });

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

                // force VDD to be on the top
                if (d.id.includes("VDD")) {
                    d.x = width/2;
                    d.y = 50;
                }
                // force GND to be on the bottom
                if (d.id.includes("GND")) {
                    d.x = width/2;
                    d.y = height-50;
                }
                // force output to be on the right
                if (d.id.includes("OUT")) {
                    if (d.x < width*0.8) {
                        d.x = width*0.8;
                    }
                    d.y = height/2;
                }
                return "translate(" + d.x + "," + d.y + ")";
            });
    }

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
        d3.select(this).select("circle").style("fill", "black");
    }

    $(window).resize(function () {
        graph = schematic;
        width = $("#div_schematic").width();
        height = $("#div_schematic").height();
        simulation
            .force("charge", d3.forceManyBody())
            .force("center", d3.forceCenter(width / 2, height / 2)).restart();
        simulation
            .nodes(graph.nodes)
            .on("tick", ticked);

        simulation.force("link")
            .links(graph.links);
        $("#div_schematic").css({'height':width*0.625+'px'});
    });
</script>
</body>
</html>
