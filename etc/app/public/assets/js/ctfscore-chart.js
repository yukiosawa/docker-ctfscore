// ctfscore.js


// グラフを表示する
function print_chart()
{
    $.get('/chart/list', function(data)
	  {
	      if (data)
	      {
		  var chart_data = get_chartjs_data(data);
		  draw_chartjs(chart_data);
	      }
	      else
	      {
		  $("#errmsg").text("データがありません。");
	      }
	  }, 'json'
	 );
}


// Chart.js用のデータを生成する
function get_chartjs_data(data)
{
    var chart_data = [];

    // Chart.js labels
    var labels = data.labels;
    chart_data['labels'] = labels;

    // Chart.js datasets
    chart_data['datasets'] = [];
    var datasets = [];
    // username一覧を取得
    var users = [];
    for (key in data.userlist)
    {
	users.push(key);
    }
    // usernameごとにdatasetを作成
    for (var i=0; i<users.length; i++)
    {
	var dataset = [];
	// Chart.js datasets.label
	dataset['label'] = users[i];
	// Chart.js datasets.strokeColor
	dataset['strokeColor'] = data.userlist[users[i]];
	// Chart.js datasets.pointColor
	dataset['pointColor'] = data.userlist[users[i]];
	// 該当ユーザのレコードを抽出
	var tmp1 = $.grep(data.pointlist, function(item, index)
			  {
			      return item.username == users[i];
			  });
	// labelsに対応する得点のリスト
	dataset['data'] = [];
	for (var j=0; j<labels.length; j++)
	{
	    // labelsの時刻より前のレコードを抽出
	    var tmp2 = $.grep(tmp1, function(item, index)
			      {
				  return item.gained_at <= labels[j];
			      });
	    // 最大値をlabelsの時刻時点での得点とする
	    var points = [];
	    for (var k=0; k<tmp2.length; k++)
	    {
		points.push(tmp2[k].totalpoint);
	    }
	    if (points.length > 0) {
		var maxpoint = Math.max.apply(null, points);
		dataset['data'].push(maxpoint);
	    }
	    else
	    {
		dataset['data'].push('0');
	    }
	}
	chart_data['datasets'].push(dataset);
    }
    // console.log(chart_data);
    return chart_data;
}


// Chart.jsでグラフ描画する
function draw_chartjs(chart_data)
{
    // Boolean - whether or not the chart should be responsive and resize when the browser does.
    Chart.defaults.global.responsive = true;

    var options =
	{
	    //Boolean - Whether grid lines are shown across the chart
	    scaleShowGridLines : true,

	    //String - Colour of the grid lines
	    scaleGridLineColor : "rgba(0,0,0,.05)",

	    //Number - Width of the grid lines
	    scaleGridLineWidth : 1,

	    //Boolean - Whether the line is curved between points
	    //bezierCurve : true,
	    bezierCurve : false,

	    //Number - Tension of the bezier curve between points
	    bezierCurveTension : 0.4,

	    //Boolean - Whether to show a dot for each point
	    //pointDot : true,
	    pointDot : false,

	    //Number - Radius of each point dot in pixels
	    pointDotRadius : 4,

	    //Number - Pixel width of point dot stroke
	    pointDotStrokeWidth : 1,

	    //Number - amount extra to add to the radius to cater for hit detection outside the drawn point
	    pointHitDetectionRadius : 20,

	    //Boolean - Whether to show a stroke for datasets
	    datasetStroke : true,

	    //Number - Pixel width of dataset stroke
	    datasetStrokeWidth : 2,

	    //Boolean - Whether to fill the dataset with a colour
	    //datasetFill : true,
	    datasetFill : false,

	    //String - A legend template
	    legendTemplate : "<ul style=\"list-style:none;\" class=\"<%=name.toLowerCase()%>-legend\"><% for (var i=0; i<datasets.length; i++){%><li><span style=\"background-color:<%=datasets[i].strokeColor%>\">&nbsp;&nbsp;&nbsp;&nbsp;</span><%if(datasets[i].label){%><%=datasets[i].label%><%}%></li><%}%></ul>"
	};
    
    var ctx = document.getElementById("myChart").getContext("2d");
    var myLineChart = new Chart(ctx).Line(chart_data, options);
    document.getElementById("legend").innerHTML = myLineChart.generateLegend();
}

