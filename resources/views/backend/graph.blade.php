{{-- @extends('backend.layouts.app')
@section('content') --}}
<canvas id="canvas_bar" height="600" width="1200"></canvas>







<script src="{{ asset('charts/ChartNew.js') }}"></script>
<script src="{{ asset('charts/Add-ins/stats.js') }}"></script>
<script type="text/javascript">

defCanvasWidth=1200;
defCanvasHeight=600;

var mydata1 = {
	labels : ["January","February","March","April","May","June"],
	datasets : [
		{
			fillColor : "rgba(220,220,220,0.5)",
			strokeColor : "rgba(220,220,220,1)",
			pointColor : "rgba(220,220,220,1)",
			pointStrokeColor : "#fff",
			data : [7,10,15,15,13,8],
      title : "Europe"
		},
		{
			fillColor : "rgba(151,187,205,0.5)",
			strokeColor : "rgba(151,187,205,1)",
			pointColor : "rgba(151,187,205,1)",
			pointStrokeColor : "#fff",
			data : [10,13,12,15,8,15],
      title : "North-America"
		},
		{
			fillColor : "rgba(187,151,205,0.5)",
			strokeColor : "rgba(187,151,205,1)",
			pointColor : "rgba(187,151,205,1)",
			pointStrokeColor : "#fff",
			data : [11,14,13,12,15,18],
      title : "South-America"
		},
		{
			fillColor : "rgba(151,187,151,0.5)",
			strokeColor : "rgba(151,187,151,1)",
			pointColor : "rgba(151,187,151,1)",
			pointStrokeColor : "#fff",
			data : [12,16,10,5,7,11],
      title : "Asia"
		},
		{
      type : "Line",
			fillColor : "rgba(0,220,0,0.5)",
			strokeColor : "rgba(0,220,0,1)",
			pointColor : "rgba(0,220,0,1)",
			pointStrokeColor : "#fff",
			data : [10,13.25,12.5,11.75,10.75,13],
      title : "Mean Value of the month"
		},
		{
      type : "Line",
			fillColor : "rgba(0,0,220,0.5)",
			strokeColor : "rgba(0,0,220,1)",
			pointColor : "rgba(0,0,220,1)",
			pointStrokeColor : "#fff",
			data : [9.70,14,12,11,10.2,14],
      title : "Mean same month last year"
		}
	]
}

function annotateAllX(area,ctx,data,statData,posi,posj,othervars) {
	retstring=statData[posi][posj].v2+'<BR>';
	retstring=retstring+'<BR><U>Bar Data:</U><BR>';
	for(var i=0;i<data.datasets.length;i++){
		if(typeof statData[i][posj].datavalue!="undefined" && data.datasets[i].type != "Line")retstring=retstring+statData[i][posj].v1+"="+statData[i][posj].datavalue+"<BR>";
	}
	retstring=retstring+'<BR><U>Line Data:</U><BR>';
	for(var i=0;i<data.datasets.length;i++){
		if(typeof statData[i][posj].datavalue!="undefined" && data.datasets[i].type == "Line")retstring=retstring+statData[i][posj].v1+"="+statData[i][posj].datavalue+"<BR>";
	}
	return "<%='"+retstring+"'%>";
}


var statOptions = {
      canvasBorders : true,
      yAxisMinimumInterval : 1,
      annotateDisplay : true,
      annotateLabel: annotateAllX,
      legend : true,
      barValueSpacing : 30,
      detectAnnotateOnFullLine : true
}

function roundToNumber(num, place) {
    var newval=1*num;

    if(typeof(newval)=="number"){
      if(place<=0){
        var roundVal=-place;
        newval= +(Math.round(newval + "e+" + roundVal) + "e-" + roundVal);
      }
      else {
        var roundVal=place;
        var divval= "1e+"+roundVal;
        newval= +(Math.round(newval/divval))*divval;
      }
    }
    return(newval);
} ;

$(function() {
	window.onload = function() {
    var myBar = new Chart(document.getElementById("canvas_bar").getContext("2d")).Bar(mydata1,statOptions);
    }   
});




</script>
{{-- @endsection --}}