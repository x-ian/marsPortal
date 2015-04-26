#!/usr/local/bin/bash

# try to send all net yet send mails from backlog dir in case 1st send wasn't successful
# keep it in backlog if still not successful

for i in `ls -t /home/mail_backlog/*.sh`; do
	/usr/local/bin/bash -x $i
done