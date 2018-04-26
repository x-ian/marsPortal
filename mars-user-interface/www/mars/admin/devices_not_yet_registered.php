<? 
$HEADLINE = 'Devices not yet registered'; 
include '../common.php'; 
include '../menu.php'; 
?>

<!-- begin page-specific content ########################################### -->
    <div id="main">

Devices with DHCP leases, but not registered in Captive Portal:
		<pre>
			<?php
				exec("/home/marsPortal/misc/show_dhcp_leases_without_radius_userinfo.sh", $output, $exitCode);
		
				echo json_encode($output);
			?>
		</pre>

	</div>
</body>
