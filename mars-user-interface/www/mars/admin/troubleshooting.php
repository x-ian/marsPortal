<? 
include '../common.php'; 
include '../menu.php'; 
?>

<!-- begin page-specific content ########################################### -->
    <div id="main">
<ul>
<li>Ping an external server from shell/terminal/command line, like 'ping 8.8.8.8' (and keep it running)</li>
<li>Open an external static IP webpage, like http://212.227.88.109</li>
<li>Open an external web page, like http://http://www.marsgeneral.com/</li>
<li>Keep an eye on bridged virtual machines. They might either fool their MAC address or 'reuse' the one from the host system (which will cause constant switching on Captive Portal).</li>
<li>Do not hardcode DNS servers in device. The Captive Portal won't let the DNS queries pass and therefore might not be able to intercept the traffic to establish a Captive Portal connection first (the request might simply timeout instead).</li>
</ul>

</div>
</body>
