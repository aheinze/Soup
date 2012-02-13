<?php

require_once(__DIR__."/app/app.php");

use \Raww\Spec;

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
<!DOCTYPE HTML>
<html lang="en-US">
<head>
	<meta charset="UTF-8">
	<title>Test Suite</title>
	<style type="text/css">
		body {
			background-color: #eee;
			margin:0px;
			padding: 0px;
			font-family: "Helvetica Neue", "HelveticaNeue", Helvetica, Arial, "Lucida Grande", sans-serif;
			font-size: 14px;
		}
		#header{
			background: #fff;
			padding: 20px 0px;
			margin-bottom: 20px;
		}
		.wrapper{
			width: 800px;
			margin: 0px auto;
		}
		.label {
			display: inline-block;
			padding:2px 4px;
			border-radius:2px;
			background: rgba(0,0,0,0.2);
			color:#fff;
			font-size: 80%;
			text-transform: uppercase;
		}
		.left{ float:left;}
		.right{ float:right;}
		.mb{ margin-bottom: 10px;}
		.failed { background: red; color: #fff;}
		.passed { background: green; color: #fff;}
		.spec {
			border-radius: 4px;
			border: 2px #fff solid;
			margin: 20px 0px;
			box-shadow: 0px 0px 10px #ccc;
			background: #fff;
		}
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
		.info > div { margin-bottom: 10px;}
	</style>
</head>
<body>
	<div id="header">
		<div class="wrapper">
			<strong class="left">Test suite</strong>
			<form class="right" action="tests.php" method="get">
				<select name="spec" id="">
					<option value="">Please select a Spec...</option>
					<option value="">-----------------------</option>
					<option value="-all" <?php if($selected_spec=="-all") echo "selected";?>>ALL</option>
					<option value="">-----------------------</option>
					<?php foreach (Spec::$specs as $name => $spec): ?>
						<option value="<?php echo $name;?>" <?php if($selected_spec==$name) echo "selected";?>><?php echo $name;?></option>
					<?php endforeach;?>
				</select>

				<button>Execute</button>
			</form>
			<div style="clear:both;"></div>
		</div>
	</div>

	<div class="wrapper">
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
						<?php echo $spec;?>: 
						Of <span class="label"><?php echo $info["total"];?> tests</span> 
						have <span class="label"><?php echo $info["passed"];?> passed</span> and <span class="label"><?php echo $info["failed"];?> failed</span>. 
						<span class="label">Duration: <?php echo formatTime($info["duration"]);?></span>
					</div>
					<div class="description">
						<div class="mb"><strong>Description:</strong></div>
						<?php echo $info["description"];?>
					</div>
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
			<?php endforeach;?>

		<?php endif; ?>
	</div>
</body>
</html>