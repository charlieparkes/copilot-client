<!DOCTYPE html>
<html>

	<head>
		<title>Copilot API Tester</title>

		<style>
			a {
				text-decoration: underline ;
				color: #325B75 ;
				line-height: 1.8em ;
			}
			.method {
				float: left ;
				clear: left ;
				width: 40px ;
				text-align: right ;
				padding-right: 8px ;
				border-right: 1px solid #000000 ;
			}
			.route {
				float: left ;
				clear: right ;
				text-align: left ;
				display: block ;
				padding-left: 8px ;
			}
		</style>
	</head>

	<body>
		<div style="position:fixed;top:0px;right:0px;background:#000000;color:#FFFFFF;">Script Time: <?= $totaltime ?></div>
		<div>
			<div>
				<?php 
				foreach($result as $route)
				{
					echo '<div class="method">' . $route['httpMethod'] . '</div><a class="route" target="cptester" href="' . $_SERVER['PHP_SELF'] . "?method=" . urlencode($route['httpMethod']) . "&route=" . urlencode($route['requestRoute']) . '">' . $route['requestRoute'] . '</a>';
				}
				?>
			</div>

			<div>
				<!-- show iframe here? -->
			</div>
		</div>
	</body>

</html>
