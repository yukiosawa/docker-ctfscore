<?php echo Asset::js('Chart.min.js'); ?>
<?php echo Asset::js('ctfscore.js'); ?>

<div id="errmsg"></div>

<div class="row">
    <div class="col-md-10">
	<canvas id="myChart" width="1000" height="550"></canvas>
    </div>
    <div class="col-md-2">
	<div id="legend"></div>
    </div>
</div>


<script>
    $(function()
      {
	  print_chart();
      });
</script>

