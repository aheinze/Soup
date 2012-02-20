<?php

use \Soup\Spec;

$app = $this->app;

Spec::persistent("app", $app);
Spec::load_from_folder($app["path"]->get("tests:"));

$selected_spec = $app["request"]->get("spec", null);

if(strlen($selected_spec)){

	$result   = array(
		"failed" => 0,
		"passed" => 0,
		"total"  => 0,
		"duration" => 0,
		"specs"  => array()
	);

	$callback = function($output) use(&$result){
		
		$result["duration"] += $output["duration"];
		$result["failed"]   += $output["failed"];
		$result["passed"]   += $output["passed"];
		$result["total"]    += $output["total"];

		$result["specs"][$output["name"]] = $output;
	};

	if($selected_spec=="-all"){

		Spec::run_all($callback);

	}else{
		Spec::run($selected_spec, $callback);
	}
}

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
.failed { background: red; color: #fff;}
.passed { background: green; color: #fff;}
.testitems { display: none; }
.spec:hover { box-shadow: 0px 0px 20px rgba(0,0,0,0.5); }
.spec:hover .testitems { display: block; }
.title {padding: 8px 10px; border-radius: 4px;}
.description { padding: 8px; }
.test{
	padding: 6px 8px;
	margin-bottom: 1px;
}
.info{
	padding: 8px;
	font-family: Courier;
}
</style>

<div class="mb">
	<strong class="left">Test suite</strong>
	<form class="right" action="<?php $this->url("/--soup/tests");?>" method="get">
		<select name="spec" id="" onchange="this.form.submit();">
			<option value="">Please select a Spec...</option>
			<option value="">-----------------------</option>
			<option value="-all" <?php if($selected_spec=="-all") echo "selected";?>>ALL</option>
			<option value="">-----------------------</option>
			<?php foreach (Spec::$specs as $name => $spec): ?>
				<option value="<?php echo $name;?>" <?php if($selected_spec==$name) echo "selected";?>><?php echo $name;?></option>
			<?php endforeach;?>
		</select>
	</form>
	<div style="clear:both;"></div>
</div>


<?php if(isset($result)): ?>

	<div class="title <?php echo $result["failed"] ? "failed":"passed";?> mb">
		<span class="label"><?php echo count(array_keys($result["specs"]));?> spec(s) tested</span>: 
		Of <span class="label"><?php echo $result["total"];?> tests in total</span> 
		have <span class="label"><?php echo $result["passed"];?> passed</span> and <span class="label"><?php echo $result["failed"];?> failed</span>. 
		<span class="label">Duration: <?php echo formatTime($result["duration"]);?></span>
	</div>

	<h1>Specs:</h1>

	<?php foreach ($result["specs"] as $spec => $info): ?>
		<div class="spec">
			<div class="title  <?php echo $info["failed"] ? "failed":"passed";?>">
				<h2><?php echo $spec;?></h2> 
				Of <span class="label"><?php echo $info["total"];?> tests</span> 
				have <span class="label"><?php echo $info["passed"];?> passed</span> and <span class="label"><?php echo $info["failed"];?> failed</span>. 
				<span class="label">Duration: <?php echo formatTime($info["duration"]);?></span>
			</div>
			<div class="description">
				<div class="mb"><strong>Description:</strong></div>
				<?php echo $info["description"];?>
			</div>
			<div class="testitems">
			<?php foreach ($info["tests"] as $test => $data): ?>
				<div class="test <?php echo $data["passed"] ? "passed":"failed";?> ">
					<?php echo $test;?>
				</div>
				<?php if(!$data["passed"]): ?>
				<div class="info">
					<div>
						<strong>Message:</strong><br />
						<?php echo $data["message"];?>
					</div>
					<div>
						<strong>File:</strong><br />
						<?php echo $data["file"];?>
					</div>
					<div>
						<strong>Line:</strong><br />
						<?php echo $data["line"];?>
					</div>
				</div>
				<?php endif; ?>
			<?php endforeach;?>
			</div>
		</div>
	<?php endforeach;?>

<?php else: ?>
	<div class="center please-select">Please select a Test...</div>
<?php endif; ?>