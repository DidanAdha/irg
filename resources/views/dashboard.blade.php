@extends ('master')
@section('title', 'Dashboard')
@section('content')
<div class="card">
  <div class="card-header">

    <h4></h4>
    <div class="card-header-form">

    </div>
  </div>
  <div class="card-body p-0">
  <!-- // -->
  <div style="margin:auto;">
  <!-- // -->
  <div class="row">
            <div class="col-md-4">
              <div class="card card-statistic-1">
                <div class="card-icon bg-primary">
                  <i class="far fa-user"></i>
                </div>
                <div class="card-wrap">
                  <div class="card-header">
                    <h4>Restaurant</h4>
                  </div>
                  <div class="card-body">
                    {{$restaurant}}
                  </div>
                </div>
              </div>
            </div>
            <div class="col-md-4">
              <div class="card card-statistic-1">
                <div class="card-icon bg-success">
                  <i class="far fa-user"></i>
                </div>
                <div class="card-wrap">
                  <div class="card-header">
                    <h4>Customer</h4>
                  </div>
                  <div class="card-body">
                    {{$customer}}
                  </div>
                </div>
              </div>
            </div>
            <div class="col-md-4">
              <div class="card card-statistic-1">
                <div class="card-icon bg-secondary">
                  <i class="fas fa-circle"></i>
                </div>
                <div class="card-wrap">
                  <div class="card-header">
                    <h4>Admin</h4>
                  </div>
                  <div class="card-body">
                    {{$admin}}
                  </div>
                </div>
              </div>
            </div>
          </div>
          </div>

          <div class="ml-2 row">
            <div  class="col-sm-11">
              <form action="" method="get">
                <div class="btn-group">
                <button type="submit" value="1" name="ih" class="btn btn-{!!$btn1!!}" >Restaurant</button>
                  <button type="submit" value="" name="ih" class="btn btn-{!!$btn2!!}">Customer</button>

                </div>
              </form>
              <div id="container"></div>
        </div>
          <div class="card col-md-3">
          </div>
  </div>

</div>
@endsection
@push('js')
<script>
      ///////////////////////////////////////
      Highcharts.chart('container', {
    chart: {
        type: 'line'
    },
    title: {
        text: {!!json_encode($title)!!}
    },
    subtitle: {
        text: {!!json_encode($year)!!}
    },
    xAxis: {
        categories: 
        {!!json_encode($month)!!}
        ,
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
        name: {!!json_encode($name)!!},
        data: {!!json_encode($data)!!}
      
        
    }]
});
/////////////////////////////////////
            // function refreshPage () {
            //     var page_y = document.getElementsByTagName("body")[0].scrollTop;
            //     window.location.href = window.location.href.split('?')[0] + '?page_y=' + page_y;
            // }
            // window.onload = function () {
            //     setTimeout(refreshPage, 500);
            //     if ( window.location.href.indexOf('page_y') != -1 ) {
            //         var match = window.location.href.split('?')[1].split("&")[0].split("=");
            //         document.getElementsByTagName("body")[0].scrollTop = match[1];
            //     }
            // }
            ///////////////////////////////////
            // function refreshPage () {
            //     var page_y = document.getElementsByTagName("body")[0].scrollTop;
            //     window.location.href = window.location.href.split('?')[0] + '?page_y=' + page_y;
            // }
            // window.onload = function () {
            //     setTimeout(refreshPage, 7000);
            //     if ( window.location.href.indexOf('page_y') != -1 ) {
            //         var match = window.location.href.split('?')[1].split("&")[0].split("=");
            //         document.getElementsByTagName("body")[0].scrollTop = match[1];
            //     }
            // }
    </script>    
@endpush