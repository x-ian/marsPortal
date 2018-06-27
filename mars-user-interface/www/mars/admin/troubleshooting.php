<? 
include '../common.php'; 
include '../menu.php'; 
?>

<!-- begin page-specific content ########################################### -->
    <div id="main">
<ul>
<li>Keep an eye on bridged virtual machines. They might either fool their MAC address or 'reuse' the one from the host system (which will cause constant switching on Captive Portal).</li>
<li>Do not hardcode DNS servers in device. The Captive Portal won't let the DNS queries pass and therefore might not be able to intercept the traffic to establish a Captive Portal connection first (the request might simply timeout instead).</li>
</ul>

</div>
</body>
