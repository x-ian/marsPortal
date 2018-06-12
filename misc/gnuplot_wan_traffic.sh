#!/bin/bash

# tweaking gnuplot output
# http://www.gnuplotting.org/attractive-plots/
# https://stackoverflow.com/questions/41602351/how-to-make-gnuplot-charts-look-more-visually-appealing
# https://www.electricmonk.nl/log/2014/07/12/generating-good-looking-charts-with-gnuplot/

### current queries

rm /var/db/mysql_secure/gnuplot_wan_traffic.csv 

# traffic each minute for last 60 minutes
mysql -u radius -pradpass radius --skip-column-names <<EOF
SELECT
    t1.when2,
    ((t1.tx - IFNULL(t2.tx, 0)) * -1) AS tx_diff,
    t1.rx - IFNULL(t2.rx, 0) AS rx_diff
FROM
    log_wan_traffic t1
    LEFT JOIN log_wan_traffic t2
        ON t2.when2 = (
            SELECT MAX(when2)
            FROM log_wan_traffic t3
            WHERE t3.when2 < t1.when2
        )
WHERE
    t1.when2 >= now() - INTERVAL 1 HOUR
ORDER BY t1.when2
into outfile '/var/db/mysql_secure/gnuplot_wan_traffic.csv' fields terminated by ' ' lines terminated by '\n';
EOF

# average traffic per minute of last 60 minutes yesterday
AVG=$(mysql -u radius -pradpass radius --skip-column-names <<EOF
select 
    (((max(tx)-min(tx)) / (select count(*) from log_wan_traffic where when2 >= now() - INTERVAL 1 HOUR - INTERVAL 1 DAY and when2 <= now() - INTERVAL 1 DAY)) * -1) as tx_avg,
    ((max(rx)-min(rx)) / (select count(*) from log_wan_traffic where when2 >= now() - INTERVAL 1 HOUR - INTERVAL 1 DAY and when2 <= now() - INTERVAL 1 DAY)) as rx_avg
from log_wan_traffic where when2 >= now() - INTERVAL 1 HOUR - INTERVAL 1 DAY and when2 <= now() - INTERVAL 1 DAY;
EOF
)

# max and min of each minute for last 60 minutes
MAX_MIN=$(mysql -u radius -pradpass radius --skip-column-names <<EOF
select max(t.tx_diff) as tx_max, min(t.tx_diff) as tx_min,  max(t.rx_diff) as rx_max, min(t.rx_diff) as rx_min from (
SELECT
    t1.when2,
    ((t1.tx - IFNULL(t2.tx, 0)) * -1) AS tx_diff,
    t1.rx - IFNULL(t2.rx, 0) AS rx_diff
FROM
    log_wan_traffic t1
    LEFT JOIN log_wan_traffic t2
        ON t2.when2 = (
            SELECT MAX(when2)
            FROM log_wan_traffic t3
            WHERE t3.when2 < t1.when2
        )
WHERE
    t1.when2 >= now() - INTERVAL 1 HOUR - INTERVAL 1 DAY and t1.when2 <= now() - INTERVAL 1 DAY
) as t;
EOF
)

### patch queries into csv file
sed -e "s/$/ $AVG $MAX_MIN/" /var/db/mysql_secure/gnuplot_wan_traffic.csv > /tmp/gnuplot_wan_traffic_1.csv
sed -e 's/\\//g' /tmp/gnuplot_wan_traffic_1.csv > /tmp/gnuplot_wan_traffic.csv

### create SVG
/home/marsPortal/misc/gnuplot_wan_traffic.gp > /home/marsPortal/mars-user-interface/www/mars/wan.svg

sleep 60

/home/marsPortal/misc/gnuplot_wan_traffic.sh




### OLD STUFF

exit 0

mysql -u user -p yourdatabase -e "select ((hour(createdOn) + 5) % 24), round(count(id)/"$days") from table_name where createdOn between date_sub(now(), interval "$days" day) and now() group by hour(createdOn) into outfile '$filename' fields terminated by ',' lines terminated by '\n';"	

log_Wan_traffic_3.sh


### old queries

-- traffic every 5 min
select min(when2), max(rx)-min(rx), sum(rx) from log_wan_traffic group by when2 div 500;

select min(when2), max(rx)-min(rx), sum(rx) from log_wan_traffic where when2 >= date_sub(now(), interval 8 hour);

SET @diff=0;
SELECT
    when2,
    rx - @diff AS rx_diff,
    @diff := rx
FROM log_wan_traffic
ORDER BY when2

-- avg traffic 5 min
--select ((max(rx)-min(rx)) / (select count(*) from log_wan_traffic)) from log_wan_traffic;
-- select ((max(rx)-min(rx)) / (select count(*) from log_wan_traffic where when2 >= now() - INTERVAL 1 HOUR)) from log_wan_traffic where when2 >= now() - INTERVAL 1 HOUR;


-- min & max
select max(t.rx), min(t.rx) from (select max(rx)-min(rx) as rx from log_wan_traffic group by when2 div 500) as t;
