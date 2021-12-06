@extends ('master')
@section('title', 'Daftar Restoran1')
@section('content')
<div id="container"></div>
<div>

</div>
@endsection
@push('js')
<script>
    Highcharts.chart('container', {
    chart: {
        type: 'line'
    },
    title: {
        text: 'Monthly Average Rainfall'
    },
    subtitle: {
        text: 'Source: WorldClimate.com'
    },
    xAxis: {
        categories: {!!json_encode($month)!!},
        crosshair: true
    },
    yAxis: {
        min: 0,
        title: {
            // text: 'Rainfall (mm)'
        }
    },
    tooltip: {
        headerFormat: '<span style="font-size:10px">{point.key}</span><table>',
        pointFormat: '<tr><td style="color:{series.color};padding:0">{series.name}: </td>' +
            '<td style="padding:0"><b>{point.y}</b></td></tr>',
        footerFormat: '</table>',
        shared: true,
        useHTML: true
    },
    plotOptions: {
        column: {
            pointPadding: 0.2,
            borderWidth: 0
        }
    },
    series: [{
        name: "jumlah penjualan",
        data: {!!json_encode($data)!!}
       
        
    }]
});
    </script>    
@endpush