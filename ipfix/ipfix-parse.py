#!/usr/local/bin/python2.7

import time

def is_local_ip(ip):
    return ip.startswith('192.168.') or ip.startswith('172.16.')
    
def is_remote_ip(ip):
    return not is_local_ip(ip)
    
outputfile = open('test.csv', 'w')

# start-time             |end-time               |duration|rtt     |proto|sip                                   |sp   |dip                                     |dp   |srcMacAddress    |destMacAddress   |iflags  |uflags  |riflags |ruflags |isn     |risn    |tag|rtag|pkt    |oct     |rpkt    |roct    |app  |end-reason
# 2015-04-23 07:26:03.861|2015-04-23 07:26:03.873|   0.012|   0.000|  6|                         173.194.116.163|  443|                           192.168.10.11|62911|00:22:4d:b3:82:a4|00:25:00:48:60:10|      AP|      AF|      AF|       A|2cf1bdb5|c0f4ad43|000|000|       3|     211|       3|     156|    0|

with open('b') as fp:
    fp.readline()
    day = time.strftime('%Y-%m-%d')
    for line in fp:
        t = line.split('|')
        sip=t[5].strip()
        dip=t[7].strip()
        srcMac=t[9].strip()
        destMac=t[10].strip()
        toct=t[20].strip()
        roct=t[22].strip()

        remote_ip = ''
        mac = ''
        outgoing = ''
        incoming = ''
        
        if (is_local_ip(sip) and is_remote_ip(dip)):
            remote_ip = dip
            mac = srcMac
            outgoing = toct
            incoming = roct
        elif (is_remote_ip(sip) and is_local_ip(dip)):
            remote_ip = sip
            mac = destMac
            outgoing = roct
            incoming = toct
        
        outputfile.write(day + '\t' + mac + '\t' + remote_ip + '\t' + outgoing + '\t' + incoming + '\n')

fp.close()
outputfile.close()