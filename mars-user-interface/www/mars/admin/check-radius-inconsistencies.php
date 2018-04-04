<? 
$HEADLINE = 'Check for RADIUS inconsistencies'; 
include '../menu.php'; 
?>

<!-- begin page-specific content ########################################### -->
    <div id="main">

Checking for RADIUS inconsistencies:
		<pre>
			<?php
				exec("/home/marsPortal/misc/check-radius-inconsistencies.sh", $output, $exitCode);
		
				echo json_encode($output);
			?>
		</pre>

	</div>
</body>
