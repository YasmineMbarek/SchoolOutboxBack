@extends('layouts.admin')
@section('content')
<div class="col-12">
    <div class="card  ">
        <div class="card-body">
            <h3 class="mb-1">Customers statistic</h3>
            <div id="curve_chart" style="width: 100%; height: 100%"></div>
        </div>
    </div>
</div>
<div><br></div>
<div class="col-12">
    <div class="card  ">
        <div class="card-body">
            <h3 class="mb-1">Articles received</h3>
            <div id="mon-chart-date" style="height: 301px; width: 451px; margin-left: 27%;"></div>

        </div>
    </div>
</div>
<div><br></div>

 <!--<div class="col-12">
    <div class="card  ">
        <div class="card-body">
            <h3 class="mb-1">Articles affected</h3>
            <div id="mon-chart-affected" style="height: 301px; width: 451px; margin-left: 27%;"></div>

        </div>
    </div>
</div> -->
<div><br></div>

<div class="col-12">
    <div class="card  ">
        <div class="card-body">
            <h3 class="mb-1">Articles by categories</h3>
            <div id="mon-chart" style="height: 301px; width: 451px; margin-left: 27%;"></div>


        </div>
    </div>
</div>
<div><br></div>





<script type="text/javascript" src="https://www.gstatic.com/charts/loader.js"></script>
<script type="text/javascript">
    google.charts.load('current', {'packages': ['corechart']});
    <!-- chart1 -->


    google.charts.setOnLoadCallback(drawChartCustomerByYM);

    function drawChartCustomerByYM() {
        var data = google.visualization.arrayToDataTable([
            ['MY', 'Number of customer by date'],
                @foreach ($nbCustomer as $key => $values)
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
    function drawChartdate() {
        var data = google.visualization.arrayToDataTable([
            ['Date', 'Articles'],
                @foreach ($article as $key => $values)
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
    function affectedArticle() {
        var data = google.visualization.arrayToDataTable([
            ['Date', 'Articles'],
                @foreach ($articleAffected as $key => $values)
            ['{{ $key  }}', {{ $values->count()}},],
            @endforeach

        ]);

        var options = {
            chart: {
                subtitle: 'Articles by date',
            },
            bars: 'vertical'
            ,

        };

        var chart = new google.charts.Bar(document.getElementById('mon-chart-affected'));

        chart.draw(data, google.charts.Bar.convertOptions(options));
    }

    google.charts.load('current', {'packages': ['bar']});
    google.charts.setOnLoadCallback(affectedArticle);


    google.charts.setOnLoadCallback(drawChart);

    function drawChart() {

        var data = google.visualization.arrayToDataTable([
            ['Catégories', 'articles'],
                @foreach ($articleByCat as $key => $values)
            ["{{ $key}}", {{ $values->count() }}],
            @endforeach
        ]);

        var options = {
            is3D: true
        };

        var chart = new google.visualization.PieChart(document.getElementById('mon-chart'));

        chart.draw(data, options);
    }
</script>
@endsection()
