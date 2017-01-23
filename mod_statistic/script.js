var visitorsData = {};
var start_date = '';
var end_date = '';

$( document ).ready(function() {

	start_date = moment().subtract(6, 'days').format('YYYY-MM-DD');
	end_date = moment().format('YYYY-MM-DD');
	
	callworldmap(end_date,start_date);
	SparklineCharts(end_date,start_date);


  $('.daterange').daterangepicker({
    ranges: {
      'Today': [moment(), moment()],
      'Yesterday': [moment().subtract(1, 'days'), moment().subtract(1, 'days')],
      'Last 7 Days': [moment().subtract(6, 'days'), moment()],
      'Last 30 Days': [moment().subtract(29, 'days'), moment()],
      'This Month': [moment().startOf('month'), moment().endOf('month')],
      'Last Month': [moment().subtract(1, 'month').startOf('month'), moment().subtract(1, 'month').endOf('month')]
    },
    startDate: moment().subtract(6, 'days'),
    endDate: moment()
  }, function (start, end) {
	  	start_date = start.format('YYYY-MM-DD');
	  	end_date = end.format('YYYY-MM-DD');
    	callworldmap(end_date,start_date);
		SparklineCharts(end_date,start_date);
  });  
   
});  
function callworldmap(start_date,end_date){
	$('#world-map').html('').addClass('ajax-Loading');
	$.getJSON( "mod_statistic/loaddata.php", { start_date: start_date, end_date:end_date } )
	  .done(function( json ) {		
	  visitorsData = json;
	  $('#world-map').removeClass('ajax-Loading');
	  loadworldmap();
	 });	
}
function loadworldmap(){  	
  //World map by jvectormap
  $('#world-map').vectorMap({
    map: 'world_mill_en',
    backgroundColor: "transparent",
    regionStyle: {
      initial: {
        fill: '#e4e4e4',
        "fill-opacity": 1,
        stroke: 'none',
        "stroke-width": 0,
        "stroke-opacity": 1
      }
    },
    series: {
      regions: [{
        values: visitorsData,
        scale: ["#92c1dc", "#ebf4f9"],
        normalizeFunction: 'polynomial'
      }]
    },
    onRegionLabelShow: function (e, el, code) {
      if (typeof visitorsData[code] != "undefined")
        el.html(el.html() + ': ' + visitorsData[code] + ' new visitors');
    }
  });	
}
  //Sparkline charts
function SparklineCharts(start_date,end_date){  
	var url = 'mod_statistic/loaddata_visitors.php';
	
	$('.sparkline').html('').addClass('ajax-Loading2');
	$.getJSON( url, { start_date: start_date, end_date:end_date } )
	  .done(function( json ) {		
	  
	  var myvalues1 = [];
	  var myvalues2 = [];
	  $.each( json, function( key, val ) {
	    myvalues1.push(val[0]);
	    myvalues2.push(val[1]);
	  });
	  
	  $('#sparkline-1').removeClass('ajax-Loading2').sparkline(myvalues1, {
	    type: 'line',
	    lineColor: '#92c1dc',
	    fillColor: "#ebf4f9",
	    height: '50',
	    width: '100%'
	  }); 
	  $('#sparkline-2').removeClass('ajax-Loading2').sparkline(myvalues2, {
	    type: 'line',
	    lineColor: '#92c1dc',
	    fillColor: "#ebf4f9",
	    height: '50',
	    width: '100%'
	  }); 	
	});   
	  
}	  
  