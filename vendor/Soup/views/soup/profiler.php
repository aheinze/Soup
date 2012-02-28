<?php


$data           = $this->app["cache"]->read("soup.profiler", array());
$selected_route = $this->app["request"]->get("route", null);

//helpers

function formatTime($time) {
	$ret = $time;
	$formatter = 0;
	$formats = array('ms', 's', 'm');
	if($time >= 1000 && $time < 60000) {
		$formatter = 1;
		$ret = ($time / 1000);
	}
	if($time >= 60000) {
		$formatter = 2;
		$ret = ($time / 1000) / 60;
	}
	$ret = number_format($ret,5,'.','') . ' ' . $formats[$formatter];
	return $ret;
}

?>

<style type="text/css">
	.box{
		padding: 20px;
	}
</style>

<div class="mb">
	<strong class="left">Profiler</strong>
	<form class="right" action="<?php $this->url("/--soup/profiler");?>" method="get">
		<select name="route" id="" onchange="this.form.submit();">
			<option value="">Please select a Request...</option>
			<?php foreach(array_keys($data) as $route): ?>
			<option value="<?php echo $route;?>" <?php if($selected_route==$route) echo "selected";?>><?php echo $route;?></option>
			<?php endforeach; ?>
		</select>
	</form>
	<div style="clear:both;"></div>
</div>

<?php if(count($data) && isset($data[$selected_route])): ?>
	
	<h1><?php echo $selected_route;?></h1>

	<?php foreach($data[$selected_route] as $info): ?>
	<div class="box">
		<h4><?php echo date("d.m.Y H:m:s", $info["start"]);?></h4>
		<table>
			<?php foreach($info as $key=>$value):?>
			<tr>
				<td><?php echo $key;?></td>
				<td><?php echo $value;?></td>
			</tr>
			<?php endforeach; ?>
		</table>
	</div>
	<?php endforeach; ?>
<?php else: ?>

<?php endif; ?>