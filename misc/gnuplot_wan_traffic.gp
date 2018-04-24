#!/usr/local/bin/gnuplot
reset
set terminal svg size 1000, 400

#set size ratio 0.75

set xdata time
set timefmt "%Y-%m-%d\ %H:%M"
set format x "%H:%M"
#set xlabel "Time"

set ylabel "MB per Minute"
#set yrange [-100:250]

#set title "Traffic last hour"
set key reverse Left outside
set grid

set style data lines

plot "/tmp/gnuplot_wan_traffic.csv" using 1:($3/60*1000):(0) title "Up" lt rgb "coral" with filledcurves, \
"" using 1:($4/60*1000):(0) title "Down" lt rgb "green" with filledcurves, \
"" using 1:($5/60*1000) title "Average (yesterday)" lt rgb "black", \
"" using 1:($6/60*1000) notitle lt rgb "black", \
"" using 1:($7/60*1000) title "Min (yesterday)" lt rgb "blue", \
"" using 1:($8/60*1000) title "Max (yesterday)" lt rgb "red", \
"" using 1:($9/60*1000) notitle lt rgb "red", \
"" using 1:($10/60*1000) notitle lt rgb "blue"
#
