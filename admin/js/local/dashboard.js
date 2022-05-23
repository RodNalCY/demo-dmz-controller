jQuery(document).ready(function ($) {
  $("#mdNuevoDescuento").modal("show");


  // $("#btnActivarWoocomersAPI").click(function () {
    
  //   console.log("Activar API ");

  //   var api_url = SolicitudesAjax.url;
  //   var api_non = SolicitudesAjax.seguridad;
  //   var api_id  = 1;

  //   console.log(api_id + " | "+ api_url + " | "+ api_non);
  //   $.ajax({
  //     type: "POST",
  //     url: api_url,
  //     data: {
  //       action: "peticionActivarWoocommers",
  //       nonce: api_non,
  //       id:    api_id,
  //     },
  //     success: function (data) {
  //       console.log("RDX>", data);
  //       Swal.fire("Activado !", "Se ejecuto Correctamente!", "success");
  //     },
  //     error: function (data) {
  //       console.log("Error", data);
  //     },
  //   });
    

  //});






  google.charts.load("current", { packages: ["corechart", "bar"] });
  google.charts.setOnLoadCallback(drawMultSeries);

  function drawMultSeries() {
    var data = new google.visualization.DataTable();
    data.addColumn("timeofday", "Time of Day");
    data.addColumn("number", "Motivation Level");
    data.addColumn("number", "Energy Level");

    data.addRows([
      [{ v: [8, 0, 0], f: "8 am" }, 1, 0.25],
      [{ v: [9, 0, 0], f: "9 am" }, 2, 0.5],
      [{ v: [10, 0, 0], f: "10 am" }, 3, 1],
      [{ v: [11, 0, 0], f: "11 am" }, 4, 2.25],
      [{ v: [12, 0, 0], f: "12 pm" }, 5, 2.25],
      [{ v: [13, 0, 0], f: "1 pm" }, 6, 3],
      [{ v: [14, 0, 0], f: "2 pm" }, 7, 4],
      [{ v: [15, 0, 0], f: "3 pm" }, 8, 5.25],
      [{ v: [16, 0, 0], f: "4 pm" }, 9, 7.5],
      [{ v: [17, 0, 0], f: "5 pm" }, 10, 10],
    ]);

    var options = {
      title: "Motivation and Energy Level Throughout the Day",
      hAxis: {
        title: "Time of Day",
        format: "h:mm a",
        viewWindow: {
          min: [7, 30, 0],
          max: [17, 30, 0],
        },
      },
      vAxis: {
        title: "Rating (scale of 1-10)",
      },
    };

    var chart = new google.visualization.ColumnChart(
      document.getElementById("chart_div")
    );

    chart.draw(data, options);
  }

////////////////////////////////////////////////////////////////////////////////////
google.charts.load("current", { packages: ["corechart"] });
google.charts.setOnLoadCallback(drawChartDonut);
function drawChartDonut() {
  var data = google.visualization.arrayToDataTable([
    ["Task", "Hours per Day"],
    ["Work", 11],
    ["Eat", 2],
    ["Commute", 2],
    ["Watch TV", 2],
    ["Sleep", 7],
  ]);

  var options = {
    title: "My Daily Activities",
    pieHole: 0.4,
  };

  var chart = new google.visualization.PieChart(
    document.getElementById("donutchart")
  );
  chart.draw(data, options);
}
///////////////////////////////////////////////////////////////////////////////////////////
google.charts.load('current', {'packages':['corechart']});
google.charts.setOnLoadCallback(drawChart);

function drawChart() {
  var data = google.visualization.arrayToDataTable([
    ['Year', 'Sales', 'Expenses'],
    ['2013',  1000,      400],
    ['2014',  1170,      460],
    ['2015',  660,       1120],
    ['2016',  1030,      540]
  ]);

  var options = {
    title: 'Company Performance',
    hAxis: {title: 'Year',  titleTextStyle: {color: '#333'}},
    vAxis: {minValue: 0}
  };

  var chart = new google.visualization.AreaChart(document.getElementById('chart_div_2'));
  chart.draw(data, options);
}

});