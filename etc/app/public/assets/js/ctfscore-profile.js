// ctfscore.js


// 複数ユーザでグラフの比較
function update_chart() {
    var usernames = [];
    usernames[0] = $('#my_username').text();
    usernames[1] = $('#username').val();
    print_profile_chart(usernames);
}


// グラフを表示する
function print_profile_chart(usernames)
{
    var chart_data;
    for (var key in usernames) {
	console.log('url:' + '/chart/profile/' + usernames[key]);
	$.get('/chart/profile/' + usernames[key], function(res) {
	    if (res) {
                var data = get_chartjs_profile_data(res);
//                draw_chartjs_profile(chart_data, update);

		console.log(data.labels);
		console.log(data.datasets[0].label);
		console.log(data.datasets[0].data);
		console.log(data.datasets[0].data2);


		if (!chart_data) {
		    data.datasets[0].fillColor = "rgba(151,187,205,0.2)";
		    data.datasets[0].strokeColor = "rgba(151,187,205,1)";
		    data.datasets[0].pointColor = "rgba(151,187,205,1)";
		    data.datasets[0].pointStrokeColor = "#fff";
		    data.datasets[0].pointHighlightFill = "#fff";
		    data.datasets[0].pointHighlightStroke = "rgba(151,187,205,1)";
		    chart_data = data;
		} else {
		    var datasets = [];
		    datasets['label'] = data.datasets[0].label;
		    datasets['data'] = data.datasets[0].data;
		    datasets['data2'] = data.datasets[0].data2;
		    datasets['fillColor'] = "rgba(220,220,220,0.2)";
		    datasets['strokeColor'] = "rgba(220,220,220,1)";
		    datasets['pointColor'] = "rgba(220,220,220,1)";
		    datasets['pointStrokeColor'] = "#fff";
		    datasets['pointHighlightFill'] = "#fff";
		    datasets['pointHighlightStroke'] = "rgba(220,220,220,1)";


		    chart_data.datasets.push(datasets);
//		    console.log(chart_data.datasets);
//		    chart_data.datasets[].label = data.datasets[0].label;
//		    chart_data.datasets[].data = data.datasets[0].data;
		}
            } else {
                $("#errmsg").text("データがありません。");
            }
	    draw_data_table(chart_data);
	    draw_chartjs_profile(chart_data);
        }, 'json'
	);
    }
}


function draw_data_table(chart_data) {
    var labels = chart_data.labels;
    var datasets = chart_data.datasets;

//    console.log("start table");
//    console.log(chart_data);
//    console.log(labels);
//    console.log(datasets);

    var table = $('<table>').addClass("table table-hover");
    var th = $('<tr>').append('<th></th>');
    for (var j=0; j < datasets.length; j++) {
	// ユーザ名
	th.append('<th>' + datasets[j].label + '</th>');
    }
    table.append($('<thead>').append(th));

    var tbody = $('<tbody>');
    for (var i=0; i < labels.length; i++) {
	var tr = $('<tr>');
	// カテゴリ名
	tr.append('<td>' + labels[i] + '</td>');
	// 正答率
	for (var j=0; j < datasets.length; j++) {
	    //tr.append('<td>' + datasets[j].data[i] + '</td>');
	    tr.append('<td>' + datasets[j].data[i] + '% (' + datasets[j].data2[i].point + ' / ' + datasets[j].data2[i].totalpoint + ')</td>');
	}
	tbody.append(tr);
    }

    table.append(tbody);
    $('#chart-data').children().replaceWith(table);


    /* <table class="table table-hover" id="percent-table">
       <tbody>
       <?php
       foreach ($profile['categories'] as $key => $val)
       {
       echo "<tr class='percent-data'>";
       echo "<td class='category'>".$key."</td>";
       $percent = round($val['point'] / $val['totalpoint'] * 100);
       echo "<td><span class='percentage'>".$percent."</span><span>% (".$val['point']."/".$val['totalpoint'].")</span></td>";
       echo "</tr>\n";
       }
       ?>
       </tbody> */

}


// Chart.js用のデータを生成する
function get_chartjs_profile_data(d)
{

//    console.log("get_chartjs_profile");

    var chart_data = [];

    // Chart.js labels
    // Chart.js datasets
    var labels = [];
    chart_data['datasets'] = [];
    var datasets = [];
    var data = [];
    var data2 = [];
    var categories = d['categories'];
    for (var category in categories) {
	labels.push(category);
	var point = categories[category].point;
	var totalpoint = categories[category].totalpoint;
	var percentage = Math.round(point / totalpoint * 100);
	data.push(percentage);

//	console.log(d['username'] + ' data2-0:' + point);
//	console.log(d['username'] + ' data2-1:' + totalpoint);

	data2.push({point, totalpoint});
    }
    chart_data['labels'] = labels;
    datasets['label'] = d['username'];
    datasets['data'] = data;
    datasets['data2'] = data2;
    chart_data['datasets'].push(datasets);

//    console.log(chart_data);

    return chart_data;
}


// Chart.jsでグラフ描画する
function draw_chartjs_profile(chart_data)
{
    // Boolean - whether or not the chart should be responsive and resize when the browser does.
    Chart.defaults.global.responsive = true;

    var options =
	{
	    //Boolean - Whether to show lines for each scale point
	    scaleShowLine : true,

	    //Boolean - Whether we show the angle lines out of the radar
	    angleShowLineOut : true,

	    //Boolean - Whether to show labels on the scale
	    scaleShowLabels : false,

	    // Boolean - Whether the scale should begin at zero
	    scaleBeginAtZero : true,

	    //String - Colour of the angle line
	    angleLineColor : "rgba(0,0,0,.1)",

	    //Number - Pixel width of the angle line
	    angleLineWidth : 1,

	    //String - Point label font declaration
	    pointLabelFontFamily : "'Arial'",

	    //String - Point label font weight
	    pointLabelFontStyle : "normal",

	    //Number - Point label font size in pixels
	    pointLabelFontSize : 18,

	    //String - Point label font colour
	    pointLabelFontColor : "#666",

	    //Boolean - Whether to show a dot for each point
	    pointDot : true,

	    //Number - Radius of each point dot in pixels
	    pointDotRadius : 3,

	    //Number - Pixel width of point dot stroke
	    pointDotStrokeWidth : 1,

	    //Number - amount extra to add to the radius to cater for hit detection outside the drawn point
	    pointHitDetectionRadius : 20,

	    //Boolean - Whether to show a stroke for datasets
	    datasetStroke : true,

	    //Number - Pixel width of dataset stroke
	    datasetStrokeWidth : 2,

	    //Boolean - Whether to fill the dataset with a colour
	    datasetFill : true,

	    //String - A legend template
	    legendTemplate : "<ul style=\"list-style:none;\" class=\"<%=name.toLowerCase()%>-legend\"><% for (var i=0; i<datasets.length; i++){%><li><span style=\"background-color:<%=datasets[i].strokeColor%>\">&nbsp;&nbsp;&nbsp;&nbsp;</span><%if(datasets[i].label){%><%=datasets[i].label%><%}%></li><%}%></ul>"
	};

    var ctx = document.getElementById("myChart").getContext("2d");
    var myRadarChart = new Chart(ctx).Radar(chart_data, options);
    document.getElementById("legend").innerHTML = myRadarChart.generateLegend();
}

