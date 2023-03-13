@extends('layouts.admin')
@section('content')
    <style>


        h3 {
            text-align: center !important;

        }

        h2 {
            text-align: center !important;


        }

    </style>

    <div class="col-12">
        <div class="card  ">
            <div class="card-body">
                <h3 class="mb-1">Customers statistic</h3>
                <div id="curve_chart" style="width: 100%; height: 100%"></div>
            </div>
        </div>
    </div>
    <div><br></div>


    <!--<div class=" col-12">
        <div class="card  ">
            <div class="card-body">
                <h3 class="mb-1">Affected article </h3>
                <div id="columnchart_values" style="width: 100%; height: 100%;"></div>

            </div>

        </div>
    </div>
    <div><br></div>

    <div class=" col-12">
        <div class="card  ">
            <div class="card-body">
                <h3 class="mb-1">Article by status</h3>

                <div id="donutchart" style="width: 701px; height: 451px; margin-left: 20%;"></div>
            </div>
        </div>
    </div>-->


    <div><br></div>

    <div class=" col-12">
        <div class="card card-mini mb-4">
            <div class="card-body">
                <h3 class="mb-1">Articles statistic</h3>
                <div id="mon-chart" style="height: 301px; width: 451px;  margin-left: 27%;"></div>
            </div>
        </div>
    </div>
    <div class="col-12">
        <div class="card card-mini  mb-4">
            <div class="card-body">
                <h3 class="mb-1">Regions statistic</h3>
                <div id="regions" style="height: 301px; width: 451px;  margin-left: 27%;"></div>
            </div>
        </div>
    </div>
    <div class="col-12">
        <div class="card card-mini mb-4">
            <div class="card-body">
                <h3 class="mb-1">Articles statistic</h3>
                <div id="mon-chart-bar" style="height: 301px; width: 451px;  margin-left: 27%;"></div>
            </div>
        </div>
    </div>
    <div class=" col-12">
        <div class="card card-mini  mb-4">
            <div class="card-body">
                <h3 class="mb-1">Articles by date </h3>
                <div id="mon-chart-date" style="height: 301px; width: 451px; margin-left: 27%;"></div>

            </div>
        </div>
    </div>
    <div>
        <div class=" col-12">
            <div class="card card-mini  mb-4">
                <div class="card-body">
                    <h3 class="mb-1">Received article by regions</h3>
                    <div id="chart_div" style="width: 900px; height: 500px;"></div>


                </div>
            </div>
        </div>


        <script type="text/javascript" src="https://www.gstatic.com/charts/loader.js"></script>
        <script type="text/javascript">
            google.charts.load('current', {'packages': ['corechart']});
            <!-- chart1 -->


            google.charts.setOnLoadCallback(drawChartCustomerByYM);

            function drawChartCustomerByYM() {
                var data = google.visualization.arrayToDataTable([
                    ['MY', 'Number of customer by date'],
                        @foreach ($nbCustomerByMY as $key => $values)
                    ['{{ $key  }}', {{ $values->count()}},],
                    @endforeach

                ]);


                var options = {
                    curveType: 'function',
                    legend: {position: 'bottom'}
                };

                var chart = new google.visualization.LineChart(document.getElementById('curve_chart'));

                chart.draw(data, options);

            }

            <!-- chart2 -->


            google.charts.setOnLoadCallback(drawChart4);

            function drawChart4() {
                var data = google.visualization.arrayToDataTable([
                    ["Element", "Numbers"],
                        @foreach ($articleAffectedByYM as $key => $values)
                    ['{{ $key  }}', {{ $values->count()}},],
                    @endforeach

                ]);

                var view = new google.visualization.DataView(data);


                var options = {

                    bar: {groupWidth: "10%"},
                    legend: {position: "none"},
                };
                var chart = new google.visualization.ColumnChart(document.getElementById("columnchart_values"));
                chart.draw(view, options);
            }

            <!-- chart3 -->


            google.charts.setOnLoadCallback(articleStatus);

            function articleStatus() {
                var data = google.visualization.arrayToDataTable([
                    ['status', 'Number of articles'],
                        @foreach ($articleStatus as $key => $values)
                    ['{{ $key  }}', {{ $values->count()}},],
                    @endforeach

                ]);

                var options = {
                    pieHole: 0.4,
                };

                var chart = new google.visualization.PieChart(document.getElementById('donutchart'));
                chart.draw(data, options);
            }


            <!-- chart4-->

            google.charts.setOnLoadCallback(drawChart);

            function drawChart() {

                var data = google.visualization.arrayToDataTable([
                    ['Catégories', 'articles'],
                        @foreach ($articlesByCategories as $category)
                    ["{{ $category->type}}", {{ $category->articles->count() }}],
                    @endforeach
                ]);

                var options = {
                    is3D: true
                };

                var chart = new google.visualization.PieChart(document.getElementById('mon-chart'));

                chart.draw(data, options);
            }

            <!-- chart5 -->
            function drawChartRegion() {

                var data = google.visualization.arrayToDataTable([
                    ['Catégories', 'articles'],
                        @foreach ($customersByRegions as $region)
                    ["{{ $region->name}}", {{ $region->customers->count() }}],
                    @endforeach
                ]);

                var options = {
                    is3D: true,

                };

                var chart = new google.visualization.PieChart(document.getElementById('mon-chart'));
                var chart = new google.visualization.PieChart(document.getElementById('regions'));

                chart.draw(data, options);
            }

            google.charts.load('current', {'packages': ['bar']});
            google.charts.setOnLoadCallback(drawChartBar);

            <!-- chart6 -->


            function drawChartBar() {
                var data = google.visualization.arrayToDataTable([
                    ['Category', 'Articles'],
                        @foreach ($categoriesArticles as $categoriesArticle)
                    ['{{ $categoriesArticle->type }}', {{ $categoriesArticle->articles->count() }}],
                    @endforeach
                ]);

                var options = {
                    chart: {
                    },
                    bars: 'vertical',
                };


                var chart = new google.charts.Bar(document.getElementById('mon-chart-bar'));

                chart.draw(data, google.charts.Bar.convertOptions(options));
            }

            google.charts.load('current', {'packages': ['bar']});

            google.charts.setOnLoadCallback(drawChartRegion);


            <!-- chart7 -->

            google.charts.setOnLoadCallback(drawChartBar);

            function drawChartdate() {
                var data = google.visualization.arrayToDataTable([
                    ['Date', 'Articles'],
                        @foreach ($articleReceivedDate as $key => $values)
                    ['{{ $key  }}', {{ $values->count()}},],
                    @endforeach

                ]);

                var options = {
                    colors: ['#a1eca7'],
                    chart: {
                        subtitle: 'Articles by date',
                    },
                    bars: 'vertical'
                };

                var chart = new google.charts.Bar(document.getElementById('mon-chart-date'));

                chart.draw(data, google.charts.Bar.convertOptions(options));
            }

            google.charts.load('current', {'packages': ['bar']});
            google.charts.setOnLoadCallback(drawChartdate);


            <!-- chart8 -->

            google.load('visualization', '1', {'packages': ['geochart']});
            google.setOnLoadCallback(drawVisualization);

            function drawVisualization() {
                var data = google.visualization.arrayToDataTable([
                    ['State', 'number of articles received'],
                        @foreach ($articlesRegions as $key => $values)
                    ['{{ $key  }}', {{ $values->count()}},],
                    @endforeach

                ]);

                var opts = {
                    region: 'TN',
                    displayMode: 'regions',
                    resolution: 'provinces',
                    width: 640,
                    height: 480,
                    colorAxis: {colors: ['#a1eca7', '#4374e0']}
                };
                var geochart = new google.visualization.GeoChart(
                    document.getElementById('chart_div'));
                geochart.draw(data, opts);
            };


        </script>






@endsection()
