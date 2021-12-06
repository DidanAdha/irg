@extends ('master')
@section('title', 'Info Restaurant')
@section('content')
<div class="card">
  <div class="card-header">
  <form action="" class="form-inline" method="get">
      @csrf
      <input type="text" class="form-control col-md-5" name="date" id="bulan" placeholder="Pilih tahun">
      &nbsp
      <select name="by" class="custom-select custom-select-sm form-control">
        <option value="3">By</option>
        <option value="1">Hari</option>
        <option value="2">Bulan</option>
        <option value="3">Tahun</option>
      </select>
      &nbsp
      <button type="submit" class="btn btn-success"><span class="fas fa-check"></span></button>  

    </form>
    <h4></h4>
    <div class="card-header-form">

    </div>
  </div>
  <div class="card-body p-0">

  </div>
  <div id="container"></div><br>
  <br><br>
  <a href="/analisis"  class="btn btn-danger col-md-12 mx-auto">Back</a>
</div>

              @endsection
              @push('js')
<script>
      $(document).ready(function(){
        $('#bulan').datepicker({
          format: "dd-mm-yyyy",
          startView: "days", 
          minViewMode: "days"
        });
      });
      ///////////////////////////////////////
      Highcharts.chart('container', {
    chart: {
        type: 'line'
    },
    title: {
        text: 'Data Penjualan'
    },
    subtitle: {
        text: {{$tahun}}
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