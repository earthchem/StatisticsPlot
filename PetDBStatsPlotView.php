<?php
/* This script will draw PetDB Search usage graph on fly
* @Author: Lulin Song created on July 18, 2016
*
* $Id:$
* $LastChangedDate:$
* $LastChangedBy:$
* $LastChangedRevision:$
*/
require_once 'PetDBStatsPlot.php';
date_default_timezone_set('America/New_York');
$nextyear  = mktime(0, 0, 0, date("m"),   date("d"),   date("Y")-1);
$currentTime  = mktime(0, 0, 0, date("m"),   date("d"),   date("Y") -0);
$monthOneYearBefore = date('Y-m-d', $nextyear);
$monthCurrentYear = date('Y-m-d', $currentTime);

$plotview = new PetDBStatsPlot("http://isotope.ldeo.columbia.edu:7001/loadpetdb/search/download_stat.jsp",  
                              array("start"=>"$monthOneYearBefore","end"=>"$monthCurrentYear") 
                            );

$plotData = json_decode($plotview->getPlotArray());

?>
<html>
<head>
    <script type="text/javascript" src="https://www.google.com/jsapi"></script>
    <script type="text/javascript">
      google.load("visualization", "1", {packages: ["corechart","table","annotatedtimeline"] });
      google.setOnLoadCallback(drawChart);

      function drawChart() {

                var dom_data = new google.visualization.DataTable();
                dom_data.addColumn('date', 'Month');
                dom_data.addColumn('number', 'Unique IP addresses');
                dom_data.addColumn('number', 'Unique data downloads');
                dom_data.addRows(<?= sizeof($plotData) ?>);
                 <?php
                 $idx=0;
                 //Important: When using this Date String Represenation, as when using the new Date() constructor, months are indexed starting at zero (January is month 0, December is month 11). 
                 foreach ($plotData as $index => $row )
                 {
                     $dateStrArr = explode(",",$row[0]);
                     $dateStr = $dateStrArr[0].",".(intval($dateStrArr[1])-1);
                 ?>
                 dom_data.setCell( <?=$idx?>,0, new Date( <?= $dateStr ?> ) );
                 dom_data.setCell( <?=$idx?>,1, <?= $row[1] ?> );
                 dom_data.setCell( <?=$idx?>,2, <?= $row[2] ?> );
                 <?php
                     $idx++;
                 }
                 ?>
                var dom_options = {
                        title: 'PetDB Usage',
                        colors:['#728FCE','#C34A2C'],
                        selectionMode: 'multiple',
                        isStacked: false,
                        areaOpacity:'0.4',
                        hAxis: {title: 'Month',
                                gridlines:{count: '6'}
                               },
                        vAxes: { 
                                 0: {title: 'IP addresses',
                                    minValue:0},
                                 1: {title: 'Downloads',
                                    minValue: 0}
                               },
                        series: { 0:{targetAxisIndex:0},
                                  1:{targetAxisIndex:1}
                                },

                        focusTarget: 'category',
                        aggregationTarget: 'category',
                        legend: 'top'
                };
                var dom_chart= new google.visualization.AreaChart(document.getElementById('chart_div'));
                dom_chart.draw(dom_data,dom_options);
      }
    </script>
    </head>
    <body>
    <div>
         <div id="chart_div" style="width: 492px; height: 200px;" ></div>
    </div>
    </body>
</html>
