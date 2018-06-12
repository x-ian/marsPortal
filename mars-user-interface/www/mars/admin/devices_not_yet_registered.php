<? 
$HEADLINE = 'Devices not yet registered'; 
include '../common.php'; 
include '../menu.php'; 
?>

<!-- begin page-specific content ########################################### -->
    <div id="main">
      <div class="page-header">
  	    <h1>Devices with DHCP leases, but not registered in Captive Portal</h1>
	  </div>

			<?php
				exec("/home/marsPortal/misc/show_dhcp_leases_without_radius_userinfo.sh", $output, $exitCode);
			?>
		
	<table class='table table-striped'>
		<thead><tr>
			<th>Lease</th>
		</tr></thead>
		<tbody>
<?
        foreach ($output as $key => $item) {
			echo "<tr><td>" . $item . "<td></tr>";
        }
		?>
	</tbody>
</table>
	</div>
</body>
