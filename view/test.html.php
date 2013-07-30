		<div style="position:fixed;top:0px;right:0px;background:#000000;color:#FFFFFF;">Script Time: <?= $totaltime ?></div>
		<div>
			<?php
			if($result !== NULL) foreach($result as $route)
			{
				echo '<div class="method">' . $route['httpMethod'] . '</div><a class="route" target="cptester" href="' . $_SERVER['PHP_SELF'] . "?method=" . $route['httpMethod'] . "&route=" . urlencode($route['requestRoute']) . '">' . $route['requestRoute'] . '</a>';
			}
			else
			{
				echo "No API routes found." ;
			}
			?>
		</div>
