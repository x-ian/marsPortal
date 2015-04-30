<? 
include '../menu.php'; 
?>

<!-- begin page-specific content ########################################### -->
    <div id="main">

<? 
include('../config.php'); 

function top_upordown($startday, $endday, $topX, $upordown) {
/*
select da.username, snap.datetime, da.day_total_input - snap.input, da.day_total_output - snap.output
from accounting_snapshot snap, daily_accounting_v2 da
where da.username = snap.username and da.day = '2015-04-30' and snap.datetime = '2015-04-30 18:14:35';
*/
return "
	SELECT 
		distinct(radacct.UserName) as username, 
		radusergroup.groupname as groupname, 
		CONCAT_WS(' ', userinfo.firstname, userinfo.lastname) as name, 
		userinfo.department as department, 
		userinfo.email as email, 
		userinfo.company as company, 
		userinfo.address as address, 
		userinfo.city as city,
		ROUND((sum(radacct.AcctOutputOctets)/1000000)) as Download,
		ROUND((sum(radacct.AcctInputOctets)/1000000)) as Upload
	FROM radacct     
		LEFT JOIN radusergroup ON radacct.username=radusergroup.username 
		LEFT JOIN userinfo ON radacct.username=userinfo.username    
	WHERE (AcctStopTime > \"0000-00-00 00:00:01\" AND AcctStartTime>\"" . $startday . "\" AND AcctStartTime<date(date_add(\"" . $endday . "\", INTERVAL +1 DAY))) 
		OR 
		((radacct.AcctStopTime IS NULL OR radacct.AcctStopTime = \"0000-00-00 00:00:00\") AND AcctStartTime<date(date_add(\"" . $endday . "\", INTERVAL +1 DAY))) 
		group by UserName order by " . $upordown . " desc limit " . $topX . ";";  
}



</div>
</body>



