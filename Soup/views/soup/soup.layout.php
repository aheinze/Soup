<!DOCTYPE HTML>
<html lang="en-US">
<head>
	<meta charset="UTF-8">
	<title>Soup</title>
	<style type="text/css">
		body {
			background-color: #eee;
			margin:0px;
			padding: 0px;
			font-family: "Helvetica Neue", "HelveticaNeue", Helvetica, Arial, "Lucida Grande", sans-serif;
			font-size: 14px;
		}
		#header{
			border-bottom: 1px #ccc solid;
			background: #fff;
			box-shadow: 0px 0px 10px rgba(0,0,0,0.3);
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
		.box {
			border-radius: 4px;
			border: 2px #fff solid;
			margin: 20px 0px;
			box-shadow: 0px 0px 10px #ccc;
			background: #fff;
		}
		.left{ float:left;}
		.right{ float:right;}
		.center { text-align: center;}
		.mb{ margin-bottom: 10px;}
		.info > div { margin-bottom: 10px;}
		.please-select { margin: 80px 0px; font-size: 40px; color: #ccc;}
	</style>
</head>
<body>
	<div id="header">
		<div class="wrapper">
			<strong class="left">&#9832; Soup</strong>
			<div class="right">
				<?php $this->link("Tests", "/--soup/tests"); ?> 
				<?php $this->link("Profiler", "/--soup/profiler"); ?> 
			</div>
			<div style="clear:both;"></div>
		</div>
	</div>

	<div class="wrapper">
		<?php echo $content_for_layout;?>

	</div>
</body>
</html>