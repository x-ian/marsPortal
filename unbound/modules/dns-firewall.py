#!/usr/bin/env python
# -*- coding: utf-8 -*-
'''
=========================================================================================
 dns-firewall.py: v6.123-20180422 Copyright (C) 2018 Chris Buijs <cbuijs@chrisbuijs.com>
=========================================================================================

DNS filtering extension for the unbound DNS resolver.

Based on dns_filter.py by Oliver Hitz <oliver@net-track.ch> and the python
examples providen by UNBOUND/NLNetLabs/Wijngaards/Wouters and others.

At start, it reads the following files:

- blacklist  : contains a domain, IP/CIDR or regex (between forward slashes) per line to block.
- whitelist  : contains a domain, IP/CIDR or regex (between forward slasges) per line to pass-thru.

Note: IP's will only be checked against responses (see 'checkresponse' below). 

For every query sent to unbound, the extension checks if the name is in the
lists and matches. If it is in the whitelist, processing continues
as usual (i.e. unbound will resolve it). If it is in the blacklist, unbound
stops resolution and returns the IP address configured in intercept_address,
or REFUSED reply if left empty.

Note: The whitelist has precedence over blacklist (see 'disablewhitelist' below).

The whitelist and blacklist domain matching is done with every requested domain
and includes it subdomains.

The regex versions will match whatever is defined. It will match sequentially
and stops processing after the first hit.

Caching: this module will cache all black or whitelisted results after processinging to speed
things up, see caching parameters below.

Install and configure:

- Make sure all modules used are availble (check 'from" and 'import" statements above).
- Copy dns-firewall.py to unbound directory. 
- If needed, change "intercept_address" below.
- Change unbound.conf as follows:

  server:
    module-config: "python validator iterator"

  python:
    python-script: "/unbound/directory/dns-firewall.py"

- Create the above lists as desired (filenames can be modified below).
- Restart unbound.

TODO:

- !!! Better Documentation / Remarks / Comments

=========================================================================================
'''

# Modules

# Make sure modules can be found
import sys
sys.path.append("/usr/local/lib/python2.7/dist-packages/")

# Standard/Included modules
import os, os.path, datetime, gc, subprocess
from thread import start_new_thread
from random import shuffle
from copy import deepcopy

# DNS Resolver (used for SafeDNS)
import dns.resolver

# Enable Garbage collection
gc.enable()

# Use requests module for downloading lists
import requests

# Use module regex instead of re, much faster less bugs
import regex

# Use module pytricia to find ip's in CIDR's dicts fast
import pytricia

# Use CacheTools TTLCache for cache
from cachetools import TTLCache

# Use cymruwhois for SafeDNS ASN lookups
from cymruwhois import Client

# Use IPSet from IPy to aggregate
from IPy import IP, IPSet

##########################################################################################

# Variables/Dictionaries/Etc ...

# logging tag
tag = 'DNS-FIREWALL INIT: '
tagcount = 0

# IP Address to redirect to, leave empty to generate REFUSED
#intercept_address = ''
intercept_address = '192.168.1.250'
intercept_host = 'sinkhole.'

# List files
# Per line you can specify:
# - An IP-Address, Like 10.1.2.3
# - A CIDR-Address/Network, Like: 192.168.1.0/24
# - A Regex (start and end with forward-slash), Like: /^ad[sz]\./
# - A Domain name, Like: bad.company.com

# Lists file to configure which lists to use, one list per line, syntax:
# <Identifier>,<black|white>,<filename|url>[,savefile[,maxlistage[,regex]]]
#lists = False
lists = '/etc/unbound/dns-firewall.lists'

# Lists
blacklist = dict() # Domains blacklist
whitelist = dict() # Domains whitelist
cblacklist4 = pytricia.PyTricia(32) # IPv4 blacklist
cwhitelist4 = pytricia.PyTricia(32) # IPv4 whitelist
cblacklist6 = pytricia.PyTricia(128) # IPv6 blacklist
cwhitelist6 = pytricia.PyTricia(128) # IPv6 whitelist
rblacklist = dict() # Regex blacklist (maybe replace with set()?)
rwhitelist = dict() # Regex whitelist (maybe replace with set()?)
excludelist = dict() # Domain excludelist
asnwhitelist = dict() # ASN Whitelist
asnblacklist = dict() # ASN Blacklist
safeblacklist = dict() # Safe listm anything is this list will not be touched
safewhitelist = dict() # Safe listm anything is this list will not be touched
safeunwhitelist = dict() # Keep unwhitelisted entries safe

# Cache
cachesize = 4096 # Entries
cachettl = 1800 # Seconds
blackcache = TTLCache(cachesize, cachettl)
whitecache = TTLCache(cachesize, cachettl)
asnscorecache = TTLCache(cachesize, cachettl * 8)
asncache4 = pytricia.PyTricia(32)
asncache6 = pytricia.PyTricia(128)
cachefile = '/etc/unbound/cache.file'

# Save
savelists = True
blacksave = '/etc/unbound/blacklist.save'
whitesave = '/etc/unbound/whitelist.save'

# regexlist
fileregex = dict()
fileregexlist = '/etc/unbound/listregexes'

# TLD file
#tldfile = False
tldfile = '/etc/unbound/tlds.list'
tldlist = dict()

# Forcing blacklist, use with caution
disablewhitelist = False

# Filtering on/off
filtering = True

# Unwhitelist domains, keep in mind this can remove whitelisted entries that are blocked by IP.
unwhitelist = False

# Keep state/lock on commands
command_in_progress = False

# Queries within bewlow TLD (commandtld) will be concidered commands to execute
# Only works from localhost (system running UNBOUND)
# Query will return NXDOMAIN or timeout, this is normal.
# Commands availble:
# dig @127.0.0.1 <number>.debug.commandtld - Set debug level to <Number>
# dig @127.0.0.1 save.cache.commandtld - Save cache to cachefile
# dig @127.0.0.1 reload.commandtld - Reload saved lists
# dig @127.0.0.1 update.commandtld - Update/Reload lists
# dig @127.0.0.1 force.update.commandtld - Force Update/Reload lists
# dig @127.0.0.1 force.reload.commandtld - Force fetching/processing of lists and reload
# dig @127.0.0.1 pause.commandtld - Pause filtering (everything passthru)
# dig @127.0.0.1 resume.commandtld - Resume filtering
# dig @127.0.0.1 maintenance.commandtld - Run maintenance
# dig @127.0.0.1 flush.cache.commandtld - Flush caches
# dig @127.0.0.1 <domain>.add.whitelist.commandtld - Add <Domain> to blacklist
# dig @127.0.0.1 <domain>.add.blacklist.commandtld - Add <Domain> to blacklist
# dig @127.0.0.1 <domain>.del.whitelist.commandtld - Remove <Domain> from whitelist
# dig @127.0.0.1 <domain>.del.blacklist.commandtld - Remove <Domain> from blacklist
commandtld = '.command'

# unbound-control, leave empty '' to disable
ucontrol = '/usr/local/sbin/unbound-control -c /etc/unbound/unbound.conf'

# Check answers/responses as well
checkresponse = True

# Maintenance after x queries
maintenance = 100000

# Automatic generated reverse entries for IP-Addresses that are listed
autoreverse = True

# Automatic add non-hits (both black or whitelists) to whitelist cache (only cache!)
autowhitelist = False # !!! Leave False

# Block IPv6 queries/responses
blockv6 = False

# CNAME Collapsing (note: whitelisted entries are not collapsed)
collapse = True

# Allow RFC 2606 TLD's
rfc2606 = False

# Allow common intranet TLD's
intranet = False

# Allow block internet domains
notinternet = False

# Aggregate IP lists, can be slow on large list (more then 5000 entries)
aggregate = True # if false, only child subnets will be removed

# Creaete automatic white-safelist entries that are unwhitelisted
autowhitesafelist = True

# Default maximum age of downloaded lists, can be overruled in lists file
maxlistage = 43200 # In seconds

# Debugging, Levels: 0=Minimal, 1=Default, show blocking, 2=Show all info/processing, 3=Flat out all
# The higher levels include the lower level informations
debug = 2

# Default file regex
defaultfregex = '^(?P<line>.*)$'

# Regex to match IPv4/IPv6 Addresses/Subnets (CIDR)
ip4regex = '((25[0-5]|2[0-4][0-9]|1[0-9]{2}|[1-9]?[0-9])(\.(25[0-5]|2[0-4][0-9]|1[0-9]{2}|[1-9]?[0-9])){3}(/(3[0-2]|[12]?[0-9]))*)'
ip6regex = '(((:(:[0-9a-f]{1,4}){1,7}|::|[0-9a-f]{1,4}(:(:[0-9a-f]{1,4}){1,6}|::|:[0-9a-f]{1,4}(:(:[0-9a-f]{1,4}){1,5}|::|:[0-9a-f]{1,4}(:(:[0-9a-f]{1,4}){1,4}|::|:[0-9a-f]{1,4}(:(:[0-9a-f]{1,4}){1,3}|::|:[0-9a-f]{1,4}(:(:[0-9a-f]{1,4}){1,2}|::|:[0-9a-f]{1,4}(::[0-9a-f]{1,4}|::|:[0-9a-f]{1,4}(::|:[0-9a-f]{1,4}))))))))|(:(:[0-9a-f]{1,4}){0,5}|[0-9a-f]{1,4}(:(:[0-9a-f]{1,4}){0,4}|:[0-9a-f]{1,4}(:(:[0-9a-f]{1,4}){0,3}|:[0-9a-f]{1,4}(:(:[0-9a-f]{1,4}){0,2}|:[0-9a-f]{1,4}(:(:[0-9a-f]{1,4})?|:[0-9a-f]{1,4}(:|:[0-9a-f]{1,4})))))):(25[0-5]|2[0-4][0-9]|1[0-9]{2}|[1-9]?[0-9])(\.(25[0-5]|2[0-4][0-9]|1[0-9]{2}|[1-9]?[0-9])){3})(/(12[0-8]|1[01][0-9]|[1-9]?[0-9]))*)'
ipregex = regex.compile('^(' + ip4regex + '|' + ip6regex +')$', regex.I)
#ipregex = regex.compile('^(([0-9]{1,3}\.){3}[0-9]{1,3}(/[0-9]{1,2})*|([0-9a-f]{1,4}|:)(:([0-9a-f]{0,4})){1,7}(/[0-9]{1,3})*)$', regex.I)

# Regex to match regex-entries in lists
isregex = regex.compile('^/.*/$')

# Regex for AS(N) number
asnregex = regex.compile('^AS[0-9]+$')

# Regex to match domains/hosts in lists
#isdomain = regex.compile('^[a-z0-9\.\-]+$', regex.I) # According RFC, Internet only
isdomain = regex.compile('^[a-z0-9_\.\-]+$', regex.I) # According RFC plus underscore, works everywhere

# Regex for excluded entries to fix issues
defaultexclude = '^(127\.0\.0\.1(/32)*|::1(/128)*|local(host|net[s]*))$'
exclude = regex.compile(defaultexclude, regex.I)

# Regex for www entries
wwwregex = regex.compile('^(https*|ftps*|www*)[0-9]*\..*$', regex.I)

# SafeDNS - HIGHLY EXPERIMENTAL AND WILL BREAK STUFF, USE AT OWN RISK !!!
# Based on idea/code of NavyTitanium: https://github.com/NavyTitanium/Dns-online-filter
safedns = False
safednsblock = True # When False, only monitoring/reporting
safescore = 50 # (percentage), start blocking when score is below this
nameservers = dict()
nameserverslist = '/etc/unbound/safenameservers'
#ipasnfile = False # When False, whois will be used solely to lookup ASN's
ipasnfile = '/etc/unbound/ipasn.dat'

##########################################################################################

# Check against lists
def in_list(name, bw, type, rrtype):
    tag = 'DNS-FIREWALL ' + type + ' FILTER: '
    if not filtering:
        if (debug >= 2): log_info(tag + 'Filtering disabled, passthru \"' + name + '\" (RR:' + rrtype + ')')
        return False

    if (bw == 'white') and disablewhitelist:
        return False

    if blockv6 and ((rrtype == 'AAAA') or name.endswith('.ip6.arpa')):
        if (bw == 'black'):
             if (debug >= 2): log_info(tag + 'HIT on IPv6 for \"' + name + '\" (RR:' + rrtype + ')')
             #add_to_cache(bw, name) # Do not cache, will block non-v6 queries if cached
             return True

    if not in_cache('white', name):
        if not in_cache('black', name):
            # Check for IP's
            if (type == 'RESPONSE') and rrtype in ('A', 'AAAA'):
                cidr = check_ip(name, bw)
                if cidr:
                    if (debug >= 2): log_info(tag + 'HIT on IP \"' + name + '\" in ' + bw + '-listed network ' + cidr)
                    add_to_cache(bw, name)
                    return True
                else:
                    return False

            else:
                # Check against tlds
                if (bw == 'black') and tldlist:
                    tld = name.split('.')[-1:][0]
                    if not tld in tldlist:
                        if (debug >= 2): log_info(tag + 'HIT on non-existant TLD \"' + tld + '\" for \"' + name + '\"')
                        add_to_cache(bw, name)
                        return True

                # Check against domains
                testname = name
                while True:
                    if (bw == 'black'):
                         found = (testname in blacklist)
                         if found:
                              id = blacklist[testname]
                         elif testname != name:
                             found = (testname in blackcache)
                             if found:
                                 id = 'CACHE'

                    else:
                         found = (testname in whitelist)
                         if found:
                             id = whitelist[testname]
                         elif testname != name:
                             found = (testname in whitecache)
                             if found:
                                 id = 'CACHE'
                          
                    if found:
                        if (debug >= 2): log_info(tag + 'HIT on DOMAIN \"' + name + '\", matched against ' + bw + '-list-entry \"' + testname + '\" (' + str(id) + ')')
                        add_to_cache(bw, name)
                        return True
                    elif testname.find('.') == -1:
                        break
                    else:
                        testname = testname[testname.find('.') + 1:]
                        if (debug >= 3): log_info(tag + 'Checking for ' + bw + '-listed parent domain \"' + testname + '\"')

            # Match against Regex-es
            foundregex = check_regex(name, bw, True)
            if foundregex:
                if (debug >= 2): log_info(tag + 'HIT on \"' + name + '\", matched against ' + bw + '-regex ' + foundregex +'')
                add_to_cache(bw, name)
                return True

        else:
            if (bw == 'black'):
                return True

    else:
        if (bw == 'white'):
            return True

    return False


# Check if entry is in cache
def in_cache(bw, name):
    tag = 'DNS-FIREWALL CACHE FILTER: '
    if (bw == 'black'):
        if name in blackcache:
            if (debug >= 2): log_info(tag + 'Found \"' + name + '\" in black-cache')
            return True
    else:
        if name in whitecache:
            if (debug >= 2): log_info(tag + 'Found \"' + name + '\" in white-cache')
            return True

    return False


# Add matched entry to cache
def add_to_cache(bw, name):
    tag = 'DNS-FIREWALL CACHE FILTER: '

    if autoreverse:
        addarpa = rev_ip(name)
    else:
        addarpa = False

    if (bw == 'black') and name not in blackcache:
       if (debug >= 2): log_info(tag + 'Added \"' + name + '\" to black-cache')
       blackcache[name] = True
       whitecache.pop(name, False)

       if addarpa:
           if (debug >= 2): log_info(tag + 'Auto-Generated/Added \"' + addarpa + '\" (' + name + ') to black-cache')
           blackcache[addarpa] = True
           whitecache.pop(addarpa, False)

    elif name not in whitecache:
       if (debug >= 2): log_info(tag + 'Added \"' + name + '\" to white-cache')
       whitecache[name] = True
       blackcache.pop(name, False)

       if addarpa:
           if (debug >= 2): log_info(tag + 'Auto-Generated/Added \"' + addarpa + '\" (' + name + ') to white-cache')
           whitecache[addarpa] = True
           blackcache.pop(addarpa, False)

    return True


# Check against IP lists (called from in_list)
def check_ip(ip, bw):
    if (bw == 'black'):
        if ip.find(':') == -1:
            if ip in cblacklist4:
                return cblacklist4[ip]
                #return cblacklist4.get_key(ip)
        else:
            if ip in cblacklist6:
                return cblacklist6[ip]
                #return cblacklist6.get_key(ip)
    else:
        if ip.find(':') == -1:
            if ip in cwhitelist4:
                return cwhitelist4[ip]
                #return cwhitelist4.get_key(ip)
        else:
            if ip in cwhitelist6:
                return cwhitelist6[ip]
                #return cwhitelist6.get_key(ip)

    return False

# Checke against REGEX lists (called from in_list)
def check_regex(name, bw, tld):
    tag = 'DNS-FIREWALL REGEX FILTER: '
    if (bw == 'black'):
        rlist = rblacklist
    else:
        rlist = rwhitelist

    for i in range(0,len(rlist)/3):
        checkregex = rlist[i,1]
        if (debug >= 3): log_info(tag + 'Checking ' + name + ' against regex \"' + rlist[i,2] + '\"')
        if checkregex.search(name):
            return '\"' + rlist[i,2] + '\" (' + rlist[i,0] + ')'
        
    return False


# Generate Reverse IP (arpa) domain
def rev_ip(ip):
    if ipregex.match(ip):
        if ip.find(':') == -1:
            arpa = '.'.join(ip.split('.')[::-1]) + '.in-addr.arpa'  # Add IPv4 in-addr.arpa
        else:
            a = ip.replace(':', '')
            arpa = '.'.join(a[i:i+1] for i in range(0, len(a), 1))[::-1] + '.ip6.arpa'  # Add IPv6 ip6.arpa

        return arpa
    else:
        return False


# Clear lists
def clear_lists():
    tag = 'DNS-FIREWALL LISTS: '

    global blacklist
    global whitelist
    global rblacklist
    global rwhitelist
    global cblacklist4
    global cwhitelist4
    global cblacklist6
    global cwhitelist6
    global excludelist

    log_info(tag + 'Clearing Lists')

    rwhitelist.clear()
    whitelist.clear()
    excludelist.clear()
    for i in cwhitelist4.keys():
        cwhitelist4.delete(i)
    for i in cwhitelist6.keys():
        cwhitelist6.delete(i)

    rblacklist.clear()
    blacklist.clear()
    for i in cblacklist4.keys():
        cblacklist4.delete(i)
    for i in cblacklist6.keys():
        cblacklist6.delete(i)

    clear_cache()

    return True


# Clear cache
def clear_cache():
    tag = 'DNS-FIREWALL CACHE: '

    log_info(tag + 'Clearing Cache')

    flush_dns_cache('.')

    blackcache.clear()
    whitecache.clear()
    asnscorecache.clear()
    for i in asncache4.keys():
        asncache4.delete(i)
    for i in asncache6.keys():
        asncache6.delete(i)

    return True


# Maintenance lists, check expiry, reload, etc...
def maintenance_lists(count):
    tag = 'DNS-FIREWALL MAINTENACE: '

    global command_in_progress

    if command_in_progress:
        log_info(tag + 'ALREADY PROCESSING')
        return True

    command_in_progress = True

    log_info(tag + 'Maintenance Started')

    age = file_exist(whitesave)
    if age and age < maxlistage:
        age = file_exist(blacksave)
        if age and age < maxlistage:
            log_info(tag + 'Nothing to do. Done')
            command_in_progress = False
            return False

    log_info(tag + 'Updating Lists')

    load_lists(False, True)

    log_info(tag + 'Maintenance Done')

    command_in_progress = False

    return True


# Load lists
def load_lists(force, savelists):
    tag = 'DNS-FIREWALL LISTS: '

    global blacklist
    global whitelist
    global rblacklist
    global rwhitelist
    global cblacklist4
    global cwhitelist4
    global cblacklist6
    global cwhitelist6
    global asnwhitelist
    global asnblacklist
    global tldfile
    global excludelist
    global exclude
    
    if lists == False:
        return True

    # Header/User-Agent to use when downloading lists, some sites block non-browser downloads
    headers = { 'User-Agent': 'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/63.0.3239.132 Safari/537.36' }

    # clear lists if already filled
    if (len(blacklist) > 0) or (len(whitelist) > 0):
        clear_lists()

    # Get top-level-domains
    if tldfile:
        tldlist.clear()
        age = file_exist(tldfile)
	if not age or age > maxlistage:
            log_info(tag + 'Downloading IANA TLD list to \"' + tldfile + '\"')
            r = requests.get('https://data.iana.org/TLD/tlds-alpha-by-domain.txt', headers=headers, allow_redirects=True)
            if r.status_code == 200:
                try:
                    with open(tldfile, 'w') as f:
                        f.write(r.text.encode('ascii', 'ignore').replace('\r', '').lower())

                except BaseException as err:
                    log_err(tag + 'Unable to write to file \"' + tldfile + '\": ' + str(err))
                    tldfile = False

        if tldfile:
            log_info(tag + 'Fetching TLD list from \"' + tldfile + '\"')
            try:
                with open(tldfile, 'r') as f:
                    for line in f:
                        entry = line.strip()
                        if not (entry.startswith("#")) and not (len(entry) == 0):
                            tldlist[entry] = True

            except BaseException as err:
                log_err(tag + 'Unable to read from file \"' + tldfile + '\": ' + str(err))
                tldfile = False

            if tldfile:
                if rfc2606:
                    tldlist['example'] = True
                    tldlist['invalid'] = True
                    tldlist['localhost'] = True
                    tldlist['test'] = True

                if notinternet:
                    tldlist['onion'] = True

                if intranet:
                    tldlist['corp'] = True
                    tldlist['home'] = True
                    tldlist['host'] = True
                    tldlist['lan'] = True
                    tldlist['local'] = True
                    tldlist['localdomain'] = True
                    tldlist['router'] = True
                    tldlist['workgroup'] = True

            log_info(tag + 'fetched ' + str(len(tldlist)) +  ' TLDs')


    #    if intercept_host:
    #        tldlist[intercept_host.strip('.').split('.')[-1:][0]] = True

    if fileregexlist:
            log_info(tag + 'Fetching list-regexes from \"' + fileregexlist + '\"')
            try:
                with open(fileregexlist, 'r') as f:
                    for line in f:
                        entry = line.strip()
                        if not (entry.startswith("#")) and not (len(entry) == 0):
                            elements = entry.split('\t')
                            if len(elements) > 1:
                                name = elements[0].strip().upper()
                                if (debug >= 2): log_info(tag + 'Fetching file-regex \"@' + name + '\"')
                                fileregex[name] = elements[1]
                            else:
                                log_err(tag + 'Invalid list-regex entry: \"' + entry + '\"')

            except BaseException as err:
                log_err(tag + 'Unable to read from file \"' + fileregexlist + '\": ' + str(err))
                tldfile = False

    # Read Lists
    readblack = True
    readwhite = True
    if savelists and not force:
        age = file_exist(whitesave)
        if age and age < maxlistage and not disablewhitelist:
            log_info(tag + 'Using White-Savelist, not expired yet (' + str(age) + '/' + str(maxlistage) + ')')
            read_lists('saved-whitelist', whitesave, rwhitelist, cwhitelist4, cwhitelist6, whitelist, asnwhitelist, safewhitelist, safeunwhitelist, True, 'white')
            readwhite = False

        age = file_exist(blacksave)
        if age and age < maxlistage:
            log_info(tag + 'Using Black-Savelist, not expired yet (' + str(age) + '/' + str(maxlistage) + ')')
            read_lists('saved-blacklist', blacksave, rblacklist, cblacklist4, cblacklist6, blacklist, asnblacklist, safeblacklist, False, True, 'black')
            readblack = False

    addtoblack = dict()
    addtowhite = dict()

    try:
        with open(lists, 'r') as f:
            for line in f:
                entry = line.strip().replace('\r', '')
                if not (entry.startswith("#")) and not (len(entry) == 0):
                    element = entry.split('\t')
                    if len(element) > 2:
                        id = element[0]
                        bw = element[1].lower()
                        if (bw == 'black' and readblack) or (bw == 'white' and readwhite) or (bw == 'exclude' and (readwhite or readblack)):
                            source = element[2]
                            downloadfile = False
                            listfile = False
                            force = False
                            url = False

                            if source.startswith('http://') or source.startswith('https://'):
                                url = source
                                if (debug >= 2): log_info(tag + 'Source for \"' + id + '\" is an URL: \"' + url + '\"')
                            else:
                                if (debug >= 2): log_info(tag + 'Source for \"' + id + '\" is a FILE: \"' + source + '\"')
                                
                            if source:
                                if len(element) > 3:
                                    listfile = element[3]
                                else:
                                    listfile = '/etc/unbound/' + id.strip('.').lower() + ".list"
    
                                if len(element) > 4:
                                    filettl = int(element[4])
                                else:
                                    filettl = maxlistage
    
                                fregex = defaultfregex
                                if len(element) > 5:
                                    r = element[5]
                                    if r.startswith('@'):
                                        r = r.split('@')[1].upper().strip()
                                        if r in fileregex:
                                            fregex = fileregex[r]
                                            if (debug >= 3): log_info(tag + 'Using \"@' + r + '\" regex/filter for \"' + id + '\" (' + fregex + ')')
                                        else:
                                            log_err(tag + 'Regex \"@' + r + '\" does not exist in \"' + fileregexlist + '\" using default \"' + defaultfregex +'\"')
                                    
                                    elif r.find('(?P<') == -1:
                                        log_err(tag + 'Regex \"' + r + '\" does not contain placeholder (e.g: \"(?P< ... )\")')
                                    else:
                                        fregex = r

                                exclude = regex.compile(defaultexclude, regex.I)
                                if len(element) > 6:
                                    r = element[6]
                                    if r.startswith('@'):
                                        r = r.split('@')[1].upper().strip()
                                        if r in fileregex:
                                            exclude = regex.compile(fileregex[r], regex.I)
                                            if (debug >= 3): log_info(tag + 'Using \"@' + r + '\" exclude regex/filter for \"' + id + '\" (' + r + ')')
                                        else:
                                            log_err(tag + 'Regex \"@' + r + '\" does not exist in \"' + fileregexlist + '\" using default \"' + defaultexclude +'\"')
                                    else:
                                        exclude = regex.compile(r, regex.I)

                                #if len(element) > 6:
                                #    exclude = regex.compile('(' + element[6] + '|' + defaultexclude + ')', regex.I)
                                #    if (debug >= 3): log_info(tag + id + ': Using \"' + element[6] + '\" exclude-regex/filter')

                                if url:
                                    age = file_exist(listfile)
                                    if not age or age > filettl or force:
                                        downloadfile = listfile + '.download'
                                        log_info(tag + 'Downloading \"' + id + '\" from \"' + url + '\" to \"' + downloadfile + '\"')
                                        try:
                                            r = requests.get(url, headers=headers, allow_redirects=True)
                                            if r.status_code == 200:
                                                try:
                                                    with open(downloadfile, 'w') as f:
                                                        f.write(r.text.encode('ascii', 'ignore').replace('\r', '').strip().lower())

                                                except BaseException as err:
                                                    log_err(tag + 'Unable to write to file \"' + downloadfile + '\": ' + str(err))

                                            else:
                                                log_err(tag + 'Error during downloading from \"' + url + '\"')

                                        except BaseException as err:
                                            log_err(tag + 'Error downloading from \"' + url + '\": ' + str(err))

                                    else:
                                        log_info(tag + 'Skipped download \"' + id + '\" previous list \"' + listfile + '\" is only ' + str(age) + ' seconds old')
                                        source = listfile

                                if url and downloadfile:
                                    sourcefile = downloadfile
                                else:
                                    sourcefile = source

                                if file_exist(sourcefile) >= 0:
                                    if sourcefile != listfile:
                                        try:
                                            log_info(tag + 'Creating \"' + id + '\" file \"' + listfile + '\" from \"' + sourcefile + '\"')
                                            with open(sourcefile, 'r') as f:
                                                try:
                                                    with open(listfile, 'w') as g:
                                                        for line in f:
                                                            line = line.replace('\r', '').lower().strip()
                                                            if line and len(line) >0:
                                                                if not exclude.match(line):
                                                                    matchentry = regex.match(fregex, line, regex.I)
                                                                    if matchentry:
                                                                        for placeholder in ['asn', 'domain', 'entry', 'ip', 'line', 'regex']:
                                                                            try:
                                                                                entry = matchentry.group(placeholder)
                                                                            except:
                                                                                entry = False

                                                                            if entry and len(entry) > 0:
                                                                                if not exclude.match(entry):
                                                                                    # !!! To do: use placholder to pre-process/validate/error-check type of entry via regex
                                                                                    #print placeholder, entry
                                                                                    g.write(entry)
                                                                                    g.write('\n')
                                                                                else:
                                                                                    if (debug >= 3): log_info(tag + id +': Skipping excluded entry \"' + line + '\" (' + entry + ')')

                                                                    else:
                                                                        if (debug >= 3): log_info(tag + id +': Skipping non-matched line \"' + line + '\"')

                                                                else:
                                                                    if (debug >= 3): log_info(tag + id +': Skipping excluded line \"' + line + '\"')

                                                except BaseException as err:
                                                    log_err(tag + 'Unable to write to file \"' + listfile + '\" (' + str(err) + ')')

                                        except BaseException as err:
                                            log_err(tag + 'Unable to read source-file \"' + sourcefile + '\" (' + str(err) + ')')

                                    else:
                                        log_info(tag + 'Skipped processing of \"' + id + '\", source-file \"' + sourcefile + '\" same as list-file')

                                else:
                                    log_info(tag + 'Skipped \"' + id + '\", source-file \"' + sourcefile + '\" does not exist')


                            if file_exist(listfile) >= 0:
                                if bw == 'black':
                                    read_lists(id, listfile, rblacklist, cblacklist4, cblacklist6, blacklist, asnblacklist, safeblacklist, False, force, bw)
                                elif bw == 'white':
                                    if not disablewhitelist:
                                        read_lists(id, listfile, rwhitelist, cwhitelist4, cblacklist6, whitelist, asnwhitelist, safewhitelist, safeunwhitelist, force, bw)
                                elif bw == 'exclude':
                                    excount = 0
                                    try:
                                        with open(listfile, 'r') as f:
                                            for line in f:
                                                elements = line.strip().replace('\r', '').split('\t')
                                                entry = elements[0]
                                                if (len(entry) > 0) and isdomain.match(entry):
                                                    if len(elements)>1:
                                                        action = elements[1]
                                                    else:
                                                        action = 'exclude'

                                                    if action == 'black':
                                                        addtoblack[entry] = id
                                                    elif action == 'white':
                                                        addtowhite[entry] = id

                                                    excludelist[entry] = id
                                                    excount += 1

                                        log_info(tag + 'Fetched ' + str(excount) + ' exclude entries from \"' + listfile + '\" (' + id + ')')

                                    except BaseException as err:
                                        log_err(tag + 'Unable to read list-file \"' + listfile + '\" (' + str(err) + ')')

                                else:
                                    log_err(tag + 'Unknow type \"' + bw + '\" for file \"' + listfile + '\"')
                            else:
                                log_err(tag + 'Cannot open \"' + listfile + '\"')
                        else:
                            log_info(tag + 'Skipping ' + bw + 'list \"' + id + '\", using savelist')
                    else:
                        log_err(tag + 'Not enough arguments: \"' + entry + '\"')

    except BaseException as err:
        log_err(tag + 'Unable to open file \"' + lists + '\": ' + str(err))

    # Redirect entry, we don't want to expose it
    blacklist[intercept_host.strip('.')] = 'Intercept_Host'

    # Excluding domains, first thing to do on "dirty" lists
    if excludelist and (readblack or readwhite):
        # Optimize excludelist
        excludelist = optimize_domlists(excludelist, 'ExcludeDoms')

        # Remove exclude entries from lists
        whitelist = exclude_domlist(whitelist, excludelist, 'WhiteDoms')
        blacklist = exclude_domlist(blacklist, excludelist, 'BlackDoms')
        
        # Add exclusion entries when requested
        whitelist = add_exclusion(whitelist, addtowhite, safewhitelist, 'WhiteDoms')
        blacklist = add_exclusion(blacklist, addtoblack, safeblacklist, 'BlackDoms')

    # Optimize/Aggregate white domain lists (remove sub-domains is parent exists and entries matchin regex)
    if readwhite:
        whitelist = optimize_domlists(whitelist, 'WhiteDoms')
        cwhitelist4 = aggregate_ip(cwhitelist4, 'WhiteIP4s')
        cwhitelist6 = aggregate_ip(cwhitelist6, 'WhiteIP6s')
        write_out('/etc/unbound/whitelist.full', False)
        whitelist = unreg_lists(whitelist, rwhitelist, safewhitelist, 'WhiteDoms')

    # Optimize/Aggregate black domain lists (remove sub-domains is parent exists and entries matchin regex)
    if readblack:
        blacklist = optimize_domlists(blacklist, 'BlackDoms')
        cblacklist4 = aggregate_ip(cblacklist4, 'BlackIP4s')
        cblacklist6 = aggregate_ip(cblacklist6, 'BlackIP6s')
        write_out(False, '/etc/unbound/blacklist.full')
        blacklist = unreg_lists(blacklist, rblacklist, safeblacklist, 'BlackDoms')

    # Remove whitelisted entries from blacklist
    if readblack or readwhite:
        blacklist = uncomplicate_lists(whitelist, rwhitelist, blacklist, safeblacklist)
        cblacklist4 = uncomplicate_ip_lists(cwhitelist4, cblacklist4, 'IPv4')
        cblacklist6 = uncomplicate_ip_lists(cwhitelist6, cblacklist6, 'IPv6')
        whitelist = unwhite_domain(whitelist, blacklist)
        cwhitelist4 = unwhite_ip(cwhitelist4, cblacklist4, 'IPv4 List')
        cwhitelist6 = unwhite_ip(cwhitelist6, cblacklist6, 'IPv6 List')

    # Reporting
    regexcount = str(len(rwhitelist)/3)
    ipcount = str(len(cwhitelist4) + len(cwhitelist6))
    domaincount = str(len(whitelist))
    asncount = str(len(asnwhitelist))
    log_info(tag + 'WhiteList Totals: ' + regexcount + ' REGEXES, ' + ipcount + ' IPs/CIDRs, ' + domaincount + ' DOMAINS and ' + asncount + ' ASNs')

    regexcount = str(len(rblacklist)/3)
    ipcount = str(len(cblacklist4) + len(cblacklist6))
    domaincount = str(len(blacklist))
    asncount = str(len(asnblacklist))
    log_info(tag + 'BlackList Totals: ' + regexcount + ' REGEXES, ' + ipcount + ' IPs/CIDRs, ' + domaincount + ' DOMAINS and ' + asncount + ' ASNs')

    # Save processed list for distribution
    write_out(whitesave, blacksave)

    # Clean-up after ourselfs
    gc.collect()

    return True


# Add exclusions to lists
def add_exclusion(dlist, elist, slist, listname):
    tag = 'DNS-FIREWALL LISTS: '

    before = len(dlist)

    for domain in dom_sort(elist.keys()):
        id = elist[domain]
        if (debug >= 2): log_info(tag + 'Adding excluded entry \"' + domain + '\" to ' + listname + ' (from ' + id + ')')
        if domain in dlist:
            if dlist[domain].find(id) == -1:
                dlist[domain] = dlist[domain] + ', ' + id
        else:
            dlist[domain] = id

        slist[domain] = dlist[domain]

    after = len(dlist)
    count = after - before

    if (debug >= 2): log_info(tag + 'Added ' + str(count) + ' new exclusion entries to \"' + listname + '\", went from ' + str(before) + ' to ' + str(after))

    return dlist


# Read file/list
def read_lists(id, name, regexlist, iplist4, iplist6, domainlist, asnlist, safelist, safewlist, force, bw):
    tag = 'DNS-FIREWALL LISTS: '

    orgid = id

    if (len(name) > 0):
        try:
            with open(name, 'r') as f:
                log_info(tag + 'Reading ' + bw + '-file/list \"' + name + '\" (' + id + ')')
         
                orgregexcount = (len(regexlist)/3-1)+1
                regexcount = orgregexcount
                ipcount = 0
                domaincount = 0
                asncount = 0
                skipped = 0
                total = 0

                for line in f:
                    entry = line.split('#')[0].strip().replace('\r', '')
                    if len(entry) > 0 and (not entry.startswith('#')):
                        id = orgid
                        elements = entry.split('\t')
                        if len(elements) > 1:
                            entry = elements[0]
                            if elements[1]:
                                id = elements[1]

                        safed = False
                        if (safelist != False) and entry.endswith('!'):
                            entry = entry[:-1]
                            #print "SAFE:", bw, entry
                            safed = True

                        unwhite = False
                        if (not safed) and (unwhitelist != False) and entry.endswith('&'):
                            entry = entry[:-1]
                            #print "UNWHITE:", bw, entry
                            unwhite = True

                        total += 1
                        if (isregex.match(entry)):
                            # It is an Regex
                            cleanregex = entry.strip('/')
                            try:
                                regexlist[regexcount,1] = regex.compile(cleanregex, regex.I)
                                regexlist[regexcount,0] = str(id)
                                regexlist[regexcount,2] = cleanregex
                                regexcount += 1
                            except:
                                log_err(tag + name + ': Skipped invalid line/regex \"' + entry + '\"')
                                pass

                        elif (asnregex.match(entry.upper())):
                            if checkresponse and safedns:
                                entry = entry.upper()
                                if entry in asnlist:
                                    if asnlist[entry].find(id) == -1:
                                        asnlist[entry] = asnlist[entry] + ', ' + id

                                    skipped += 1
                                else:
                                    asnlist[entry] = id
                                    asncount += 1

                        elif (ipregex.match(entry)):
                            # It is an IP
                            if add_cidr(iplist4, iplist6, entry, id):
                                ipcount += 1
                            else:
                                skipped += 1

                        elif (isdomain.match(entry)):
                                # It is a domain
                                domain = entry.strip('.').lower()

                                # Strip 'www." if appropiate
                                if wwwregex.match(domain) and domain.count('.') > 1:
                                    label = domain.split('.')[0]
                                    newdomain = '.'.join(domain.split('.')[1:])
                                    if (debug >= 2): log_info(tag + 'Stripped \"' + label + '\" from \"' + domain + '\" (' + newdomain + ')')
                                    domain = newdomain

                                if domain:
                                    if tldlist and (not force) and (not safed):
                                        tld = domain.split('.')[-1:][0]
                                        if not tld in tldlist:
                                            if (debug >= 2): log_info(tag + 'Skipped DOMAIN \"' + domain + '\", TLD (' + tld + ') does not exist')
                                            domain = False
                                            skipped += 1

                                    if domain:
                                        if unwhite:
                                            if (debug >= 2): log_info(tag + 'Added \"' + domain + '\" to ' + 'safe-unwhite-list')
                                            safewlist[domain] = id
                                            skipped += 1

                                        else:
                                            if safed:
                                                if (debug >= 2): log_info(tag + 'Added \"' + domain + '\" to ' + bw + '-safelist')
                                                safelist[domain] = 'Safelist'

                                            if domain in domainlist:
                                                if domainlist[domain].find(id) == -1:
                                                    domainlist[domain] = domainlist[domain] + ', ' + id

                                                skipped += 1

                                            else:
                                                domainlist[domain] = id
                                                domaincount += 1

                        else:
                            log_err(tag + name + ': Skipped invalid line \"' + entry + '\"')
                            skipped += 1

                if (debug >= 2): log_info(tag + 'Processed ' + bw + 'list ' + str(total) + ' entries and skipped ' + str(skipped) + ' (existing/invalid) ones from \"' + orgid + '\"')
                if (debug >= 1): log_info(tag + 'Fetched ' + bw + 'list ' + str(regexcount-orgregexcount) + ' REGEXES, ' + str(ipcount) + ' CIDRs, ' + str(domaincount) + ' DOMAINS and ' + str(asncount) + ' ASNs from ' + bw + '-file/list \"' + name + '\"')
                if (debug >= 2): log_info(tag + 'Total ' + bw + 'list ' + str(len(regexlist)/3) + ' REGEXES, ' + str(len(iplist4) + len(iplist6)) + ' CIDRs, ' + str(len(domainlist)) + ' DOMAINS and ' + str(len(asnlist)) + ' ASNs in ' + bw + '-list')

                return True

        except BaseException as err:
            log_err(tag + 'Unable to open file \"' + name + '\" (' + orgid + ') - ' + str(err))

    return False


# Add CIDR to iplist
def add_cidr(iplist4, iplist6, entry, id):
    tag = 'DNS-FIREWALL CIDR: '
    if checkresponse:
        if entry.find(':') == -1:
            ipv6 = False
            iplist = iplist4
        else:
            ipv6 = True
            iplist = iplist6

        if entry.find('/') == -1: # Check if Single IP or CIDR already
            if ipv6:
                cidr = entry.lower() + '/128' # Single IPv6 Address
            else:
                cidr = entry + '/32' # Single IPv4 Address
        else:
            cidr = entry.lower()

        if iplist.has_key(cidr):
            if iplist[cidr].find(id) == -1:
                oldid = iplist[cidr].split('(')[1].split(')')[0].strip()
                try:
                    iplist[cidr] = '\"' + cidr + '\" (' + str(oldid) + ', ' + str(id) + ')'
                except:
                    log_err(tag + name + ': Skipped invalid line/ip-address \"' + entry + '\"')
                return False
        else:
            try:
                iplist[cidr] = '\"' + cidr + '\" (' + str(id) + ')'
            except:
                log_err(tag + name + ': Skipped invalid line/ip-address \"' + entry + '\"')
                return False

    return True

# Decode names/strings from response message
def decode_data(rawdata, start):
    text = ''
    remain = ord(rawdata[2])
    for c in rawdata[3+start:]:
       if remain == 0:
           text += '.'
           remain = ord(c)
           continue
       remain -= 1
       text += c
    return text.strip('.').lower()


def flush_dns_cache(domain):
    if ucontrol:
        command = ucontrol +' flush_zone ' + domain

        print '\n#### FLUSHING', '\"' + domain + '\"'
        print 'Running:', command

        rc = 0
        try:
            rc = subprocess.call(command, shell=True)
        except BaseException as err:
            print "ERROR:", err

        print 'Return-Code:', rc

        if rc != 0:
            return False

    return True


# Generate response DNS message
def generate_response(qstate, rname, rtype, rrtype, newttl):
    if blockv6 and ((rtype == 'AAAA') or rname.endswith('.ip6.arpa')):
        if (debug >= 3): log_info(tag + 'RESPONSE: HIT on IPv6 for \"' + rname + '\" (RR:' + rtype + ')')
        return False

    if (len(intercept_address) > 0 and len(intercept_host) > 0) and (rtype in ('A', 'CNAME', 'MX', 'NS', 'PTR', 'SOA', 'SRV', 'TXT', 'ANY')):
        qname = False
        if rtype in ('CNAME', 'MX', 'NS', 'PTR', 'SOA', 'SRV'):
            if rtype == 'MX':
                fname = '0 ' + intercept_host
            elif rtype == 'SOA':
                serial = datetime.datetime.now().strftime('%Y%m%d%H')
                fname = intercept_host + ' hostmaster.' + intercept_host + ' ' + serial + ' 86400 7200 3600000 ' + str(newttl)
            elif rtype == 'SRV':
                fname = '0 0 80 ' + intercept_host
            else:
                fname = intercept_host

            rmsg = DNSMessage(rname, rrtype, RR_CLASS_IN, PKT_QR | PKT_RA )
            redirect = '\"' + intercept_host.strip('.') + '\" (' + intercept_address + ')'
            rmsg.answer.append('%s %d IN %s %s' % (rname, newttl, rtype, fname))
            qname = intercept_host
        elif rtype == 'TXT':
            rmsg = DNSMessage(rname, rrtype, RR_CLASS_IN, PKT_QR | PKT_RA )
            redirect = '\"Domain \'' + rname + '\' blocked by DNS-Firewall\"'
            rmsg.answer.append('%s %d IN %s %s' % (rname, newttl, rtype, redirect))
        else:
            rmsg = DNSMessage(rname, RR_TYPE_A, RR_CLASS_IN, PKT_QR | PKT_RA )
            redirect = intercept_address
            qname = rname + '.'

        if qname:
            rmsg.answer.append('%s %d IN A %s' % (qname, newttl, intercept_address))

        rmsg.set_return_msg(qstate)

        if not rmsg.set_return_msg(qstate):
            log_err(tag + 'GENERATE-RESPONSE ERROR: ' + str(rmsg.answer))
            return False

        if qstate.return_msg.qinfo:
            invalidateQueryInCache(qstate, qstate.return_msg.qinfo)

        qstate.no_cache_store = 0
        storeQueryInCache(qstate, qstate.return_msg.qinfo, qstate.return_msg.rep, 0)

        qstate.return_msg.rep.security = 2

        return redirect

    return False


# Domain aggregator, removes subdomains if parent exists
def optimize_domlists(name, listname):
    tag = 'DNS-FIREWALL LISTS: '

    log_info(tag + 'Unduplicating/Optimizing \"' + listname + '\"')

    domlist = dom_sort(name.keys())

    # Remove all subdomains
    parent = '.invalid'
    undupped = set()
    for domain in domlist:
        if not domain.endswith(parent):
            undupped.add(domain)
            parent = '.' + domain.strip('.')
        else:
            if (debug >= 3): log_info(tag + '\"' + listname + '\": Removed domain \"' + domain + '\" redundant by parent \"' + parent.strip('.') + '\"')

    # New/Work dictionary
    new = dict()

    # Build new dictionary preserving id/category
    for domain in undupped:
        new[domain] = name[domain]

    # Some counting/stats
    before = len(name)
    after = len(new)
    count = after - before

    if (debug >= 2): log_info(tag + '\"' + listname + '\": Number of domains went from ' + str(before) + ' to ' + str(after) + ' (' + str(count) + ')')

    return new


# Unwhitelist IP's, if whitelist entry is not blacklisted, remove it.
def unwhite_ip(wlist, blist, listname):
    if not unwhitelist:
        return wlist

    tag = "DNS-FIREWALL LISTS: "
    if (debug >= 2): log_info(tag + 'Un-Whitelisting IPs from ' + listname)
    # !!! TODO, placeholder
    return wlist


# Check if name exist in domain-list or is sub-domain in domain-list
def dom_find(name, dlist):
    testname = name
    while True:
        if testname in dlist:
            return testname
        elif testname.find('.') == -1:
            return False
        else:
            testname = testname[testname.find('.') + 1:]

    return False


# Unwhitelist domains, if whitelist entry is not blacklisted, remove it.
def unwhite_domain(wlist, blist):
    if not unwhitelist:
        return wlist

    tag = "DNS-FIREWALL LISTS: "
    if (debug >= 2): log_info(tag + 'Un-Whitelisting domains from whitelist')

    new = dict()

    for entry in dom_sort(wlist.keys()):
        testname = entry
        notfound = True
        nomatchtld = True

        while True:
            if dom_find(testname, safewhitelist):
                if (debug >= 2): log_info(tag + 'Skipped unwhitelisting \"' + entry + '\" due to being safelisted')
                break
            elif testname in blist:
                notfound = False
                if testname.find('.') == -1:
                    nomatchtld = False
                break
            elif testname.find('.') == -1:
                break
            else:
                testname = testname[testname.find('.') + 1:]

        legit = False
        if notfound and nomatchtld:
            if not check_regex(entry, 'black', False):
                if (debug >= 3): log_info(tag + 'Removed redundant white-listed domain \"' + entry + '\" (No blacklist hits)')
            else:
                legit = True

        if legit:
            new[entry] = wlist[entry]
        else:
            #print "UNWHITELIST", entry
            safeunwhitelist[entry] = 'Unwhitelist'

    before = len(wlist)
    after = len(new)
    count = before - after

    if (debug >= 2): log_info(tag + 'Number of white-listed domains went from ' + str(before) + ' to ' + str(after) + ' (Unwhitelisted ' + str(count) + ')')

    return new


# Uncomplicate lists, removed whitelisted domains from blacklist
def uncomplicate_lists(whitelist, rwhitelist, blacklist, safelist):
    tag = 'DNS-FIREWALL LISTS: '

    log_info(tag + 'Uncomplicating Domain black/whitelists')

    listw = dom_sort(whitelist.keys())
    listb = dom_sort(blacklist.keys())

    # Remove all 1-to-1/same whitelisted entries from blacklist
    # !!! We need logging on this !!!
    listb = dom_sort(list(set(listb).difference(listw)))

    # Create checklist for speed
    checklistb = '#'.join(listb) + '#'

    # loop through whitelist entries and find parented entries in blacklist to remove
    for domain in listw:
        if '.' + domain + '#' in checklistb:
            if (debug >= 3): log_info(tag + 'Checking against \"' + domain + '\"')
            for found in filter(lambda x: x.endswith('.' + domain), listb):
                if not dom_find(found, safelist):
                   if (debug >= 3): log_info(tag + 'Removed blacklist-entry \"' + found + '\" due to white-listed parent \"' + domain + '\"')
                   listb.remove(found)
                else:
                   if (debug >= 3): log_info(tag + 'Preserved white-listed/safe-black-listed blacklist-entry \"' + found + '\" due to white-listed parent \"' + domain + '\"')

            checklistb = '#'.join(listb) + "#"
        #else:
        #    # Nothing to whitelist (breaks stuff, do not uncomment)
        #    if (debug >= 2): log_info(tag + 'Removed whitelist-entry \"' + domain + '\", no blacklist hit')
        #    del whitelist[domain]

    # Remove blacklisted entries when matched against whitelist regex
    for i in range(0,len(rwhitelist)/3):
        checkregex = rwhitelist[i,1]
        if (debug >= 3): log_info(tag + 'Checking against white-regex \"' + rwhitelist[i,2] + '\"')
        for found in filter(checkregex.search, listb):
            if not dom_find(found, safelist):
                listb.remove(found)
                if (debug >= 3): log_info(tag + 'Removed \"' + found + '\" from blacklist, matched by white-regex \"' + rwhitelist[i,2] + '\"')
            else:
                if (debug >= 3): log_info(tag + 'Preserved safe-black-listed \"' + found + '\" from blacklist, matched by white-regex \"' + rwhitelist[i,2] + '\"')

    # New/Work dictionary
    new = dict()

    # Build new dictionary preserving id/category
    for domain in listb:
        new[domain] = blacklist[domain]

    before = len(blacklist)
    after = len(new)
    count = after - before

    if (debug >= 2): log_info(tag + 'Number of black-listed domains went from ' + str(before) + ' to ' + str(after) + ' (' + str(count) + ')')

    return new


# Remove excluded entries from domain-lists
def exclude_domlist(domlist, excludelist, listname):
    tag = 'DNS-FIREWALL LISTS: '

    log_info( tag + 'Excluding \"' + listname + '\"')

    newlist = deepcopy(domlist)
    checklist = '#'.join(newlist.keys()) + '#'

    for domain in dom_sort(excludelist.keys()):
        # Just the domain
        if domain in newlist:
            lname = newlist[domain]
            action = 'exclude'
            del newlist[domain]
            if (debug > 1): log_info(tag + 'Removed excluded entry \"' + domain + '\" from \"' + listname + '\" (' + lname + ')')
            checklist = '#'.join(newlist.keys()) + '#'

        # All domains ending in excluded domain (Breaks too much, leave commented out)
        #if '.' + domain + "#" in checklist:
        #    for found in filter(lambda x: x.endswith('.' + domain), domlist.keys()):
        #        lname = newlist.pop(found, False)
        #        if (debug > 1): log_info(tag + 'Removed excluded entry \"' + found + '\" (' + domain + ') from \"' + listname + '\" (' + lname + ')')
        #        checklist = '#'.join(newlist.keys()) + '#'
        #        deleted += 1

    before = len(domlist)
    after = len(newlist)
    deleted = before - after

    log_info(tag + '\"' + listname + '\" went from ' + str(before) + ' to ' + str(after) + ', after removing ' + str(deleted) + ' excluded entries')

    return newlist


# Uncomplicate IP lists, remove whitelisted IP's from blacklist
def uncomplicate_ip_lists(cwhitelist, cblacklist, listname):
    tag = 'DNS-FIREWALL LISTS: '

    log_info(tag + 'Uncomplicating ' + listname + ' black/whitelists')

    listw = cwhitelist.keys()
    listb = cblacklist.keys()

    # Remove all 1-to-1/same whitelisted entries from blacklist
    # !!! We need logging on this !!!
    listb = dom_sort(list(set(listb).difference(listw)))

    # loop through blacklist entries and find whitelisted entries to remove
    for ip in listb:
        if ip in listw:
            if (debug >= 3): log_info(tag + 'Removed blacklist-entry \"' + ip + '\" due to white-listed \"' + cwhitelist[ip] + '\"')
            listb.remove(ip)

    new = pytricia.PyTricia(128)

    # Build new dictionary preserving id/category
    for ip in listb:
        new[ip] = cblacklist[ip]

    before = len(cblacklist)
    after = len(new)
    count = after - before

    if (debug >= 2): log_info(tag + 'Number of black-listed ' + listname + ' went from ' + str(before) + ' to ' + str(after) + ' (' + str(count) + ')')

    return new


# Remove entries from domains already matching by a regex
def unreg_lists(dlist, rlist, safelist, listname):
    tag = 'DNS-FIREWALL LISTS: '

    log_info(tag + 'Unregging \"' + listname + '\"')

    before = len(dlist)

    for i in range(0,len(rlist)/3):
        checkregex = rlist[i,1]
        if (debug >= 3): log_info(tag + 'Checking against \"' + rlist[i,2] + '\"')
	for found in filter(checkregex.search, dlist):
            name = dlist[found]
            if not dom_find(name, safelist):
                del dlist[found]
                if (debug >= 3): log_info(tag + 'Removed \"' + found + '\" from \"' + name + '\" matched by regex \"' + rlist[i,2] + '\"')
            else:
                if (debug >= 3): log_info(tag + 'Preserved safelisted \"' + found + '\" from \"' + name + '\" matched by regex \"' + rlist[i,2] + '\"')

    after = len(dlist)
    count = after - before

    if (debug >= 2): log_info(tag + 'Number of \"' + listname + '\" entries went from ' + str(before) + ' to ' + str(after) + ' (' + str(count) + ')')

    return dlist


# Save lists to files
# !!!! NEEDS WORK AND SIMPLIFIED !!!!
def write_out(whitefile, blackfile):
    tag = 'DNS-FIREWALL LISTS: '

    if not savelists:
        return False

    if whitefile:
        log_info(tag + 'Saving processed whitelists to \"' + whitefile + '\"')
        try:
            with open(whitefile, 'w') as f:
                f.write('### SAFELIST DOMAINS ###\n')
                for line in dom_sort(safewhitelist.keys()):
                    f.write(line + '!\t' + safewhitelist[line])
                    f.write('\n')

                f.write('### SAFEUNWHITELIST DOMAINS ###\n')
                for line in dom_sort(safeunwhitelist.keys()):
                    f.write(line + '&\t' + safeunwhitelist[line])
                    f.write('\n')

                f.write('### WHITELIST REGEXES ###\n')
                for line in range(0,len(rwhitelist)/3):
                    f.write('/' + rwhitelist[line,2] + '/\t' + rwhitelist[line,0])
                    f.write('\n')

                f.write('### WHITELIST DOMAINS ###\n')
                for line in dom_sort(whitelist.keys()):
                    f.write(line + '\t' + whitelist[line])
                    f.write('\n')

                f.write('### WHITELIST ASN ###\n')
                for a in sorted(asnwhitelist.keys()):
                    f.write(a + '\t' + asnwhitelist[a])
                    f.write('\n')

                f.write('### WHITELIST IPv4 ###\n')
                for a in cwhitelist4.keys():
                    f.write(a + '\t' + cwhitelist4[a].split('(')[1].split(')')[0].strip())
                    f.write('\n')

                f.write('### WHITELIST IPv6 ###\n')
                for a in cwhitelist6.keys():
                    f.write(a + '\t' + cwhitelist6[a].split('(')[1].split(')')[0].strip())
                    f.write('\n')

                f.write('### WHITELIST EOF ###\n')

        except BaseException as err:
            log_err(tag + 'Unable to write to file \"' + whitefile + '\" (' + str(err) + ')')

    if blackfile:
        log_info(tag + 'Saving processed blacklists to \"' + blackfile + '\"')
        try:
            with open(blackfile, 'w') as f:
                f.write('### SAFELIST DOMAINS ###\n')
                for line in dom_sort(safeblacklist.keys()):
                    f.write(line + '!\t' + safeblacklist[line])
                    f.write('\n')

                f.write('### BLACKLIST REGEXES ###\n')
                for line in range(0,len(rblacklist)/3):
                    f.write('/' + rblacklist[line,2] + '/\t' + rblacklist[line,0])
                    f.write('\n')

                f.write('### BLACKLIST DOMAINS ###\n')
                for line in dom_sort(blacklist.keys()):
                    f.write(line + '\t' + blacklist[line])
                    f.write('\n')

                f.write('### BLACKLIST ASN ###\n')
                for a in sorted(asnblacklist.keys()):
                    f.write(a + '\t' + asnblacklist[a])
                    f.write('\n')

                f.write('### BLACKLIST IPv4 ###\n')
                for a in cblacklist4.keys():
                    f.write(a + '\t' + cblacklist4[a].split('(')[1].split(')')[0].strip())
                    f.write('\n')

                f.write('### BLACKLIST IPv6 ###\n')
                for a in cblacklist6.keys():
                    f.write(a + '\t' + cblacklist6[a].split('(')[1].split(')')[0].strip())
                    f.write('\n')

                f.write('### BLACKLIST EOF ###\n')

        except BaseException as err:
            log_err(tag + 'Unable to write to file \"' + blackfile + '\" (' + str(err) + ')')

    return True


# Domain sort
def dom_sort(domlist):
    newdomlist = list()
    for y in sorted([x.split('.')[::-1] for x in domlist]):
        newdomlist.append('.'.join(y[::-1]))

    return newdomlist


# Aggregate IP list
def aggregate_ip(iplist, listname):
    tag = 'DNS-FIREWALL LISTS: '

    log_info(tag + 'Aggregating \"' + listname + '\"')

    undupped = list(iplist.keys())

    if '#'.join(undupped).find(':') != -1:
        dictsize = 128
    else:
        dictsize = 32

    # Phase 1 - Removes child-subnets
    for ip in iplist.keys():
        bitmask = ip.split('/')[1]
        if not bitmask in ('32', '128'):
            try:
                children = iplist.children(ip)
                if children:
                   for child in children:
                        if child in undupped:
                            #print "CHILD:", child, ip
                            undupped.remove(child)
                            if (debug >= 3): log_info(tag + 'Removed ' + child + ', already covered by ' + ip + ' in \"' + iplist[ip] + '\"')

            except BaseException as err:
                log_err(tag + str(err))
                pass

    new = pytricia.PyTricia(dictsize)

    # Phase 2 - aggregate
    if aggregate:
        ipundupped = list()
        for ip in undupped:
             ipundupped.append(IP(ip))

        ipset = IPSet(ipundupped) # Here is the magic

        for ip in ipset:
            ip = ip.strNormal(1)

            if ip in iplist:
                new[ip] = iplist[ip]
            else:
                new[ip] = '\"' + ip + '\" (Aggregated)'

    else:
        for ip in undupped:
            new[ip] = iplist[ip]

    before = len(iplist)
    after = len(new)
    count = after - before

    if (debug >= 2): log_info(tag + '\"' + listname + '\": Number of IP-Entries went from ' + str(before) + ' to ' + str(after) + ' (' + str(count) + ')')

    return new


# Check if file exists and return age (in seconds) if so
def file_exist(file):
    if file:
        try:
            if os.path.isfile(file):
                fstat = os.stat(file)
                fsize = fstat.st_size
                if fsize > 0:
                    fexists = True
                    mtime = int(fstat.st_mtime)
                    currenttime = int(datetime.datetime.now().strftime("%s"))
                    age = int(currenttime - mtime)
                    return age
        except:
            return False

    return False


# Initialization
def init(id, cfg):
    tag = 'DNS-FIREWALL INIT: '

    global blacklist
    global whitelist
    global rblacklist
    global rwhitelist
    global cblacklist4
    global cwhitelist4
    global cblacklist6
    global cwhitelist6
    global excludelist
    global safedns
    global asncache4
    global asncache6

    log_info(tag + '######## DNS-FIREWALL Initializing ########')

    # Read Lists
    load_lists(False, savelists)
    #start_new_thread(load_lists, (savelists,)) # !!! EXPERIMENTAL !!!

    if safedns:
        log_info(tag + 'Loading SafeDNS nameservers')
        try:
            with open(nameserverslist, 'r') as f:
                for line in f:
                    entry = line.strip().replace('\r', '')
                    if not (entry.startswith("#")) and not (len(entry) == 0):
                        element = entry.split('\t')
			nameservers[element[0].upper()] = element[1].replace(' ', '')
                        if (debug >= 1): log_info(tag + 'Fetched Nameservers for \"' + element[0] + '\" (' + element[1] + ')')
                        for nsip in element[1].split(','):
                            #print "WHITELIST Added from SafeDNS List", nsip
                            if add_cidr(cwhitelist4, cwhitelist6, nsip, 'SafeDNS Servers'):
                                if (debug >= 3): log_info(tag + 'Auto-added ' + nsip + ' from SafeDNS list to whitelist')
                            else:
                                if (debug >= 3): log_info(tag + 'Skipped ' + nsip + ' from SafeDNS list')

        except BaseException as err:
            log_err(tag + 'Unable to open file \"' + nameserverlist + '\": ' + str(err))

        if ipasnfile:
            log_info(tag + 'Loading IP ASN Database from file \"' + ipasnfile + '\"')
            try:
                with open(ipasnfile, 'r') as f:
                    for line in f:
                        entry = line.strip().replace('\r', '')
                        if not (entry.startswith("#")) and not (len(entry) == 0):
                            element = entry.split('\t')
                            if len(element) > 1 and ipregex.match(element[0]):
                                prefix = element[0].lower()
                                asn = 'AS' + element[1]
                                try:
                                    if prefix.find(':') == -1:
                                        asncache4[prefix] = asn
                                    else:
                                        if not blockv6:
                                            asncache6[prefix] = asn

                                except BaseException as err:
                                    log_err(tag + 'Bad prefix ' + prefix + ' from \"' + ipasnfile + '\": ' + str(err))

            except BaseException as err:
                log_err(tag + 'Unable to open file \"' + ipasnfile + '\": ' + str(err))

            log_info(tag + 'ASN Cache: ' + str(len(asncache4)) + ' IPv4 and ' + str(len(asncache6)) + ' IPv6 prefixes')
        else:
            if (debug >= 1): log_info(tag + 'No IP-ASN database to load, using WHOIS for SafeDNS/ASN lookups')
     

    if len(intercept_address) == 0:
        if (debug >= 1): log_info(tag + 'Using REFUSED for matched queries/responses')
    else:
        if (debug >= 1): log_info(tag + 'Using REDIRECT to \"' + intercept_address + '\" for matched queries/responses')

    if blockv6:
        if (debug >= 1): log_info(tag + 'Blocking IPv6-Based queries')

    log_info(tag + 'READY FOR SERVICE')
    return True


# Get DNS client IP
def client_ip(qstate):
    reply_list = qstate.mesh_info.reply_list

    while reply_list:
        if reply_list.query_reply:
            return reply_list.query_reply.addr
        reply_list = reply_list.next

    return "0.0.0.0"


# Commands to execute based on commandtld query
def execute_command(qstate):
    tag = 'DNS-FIREWALL COMMAND: '

    global filtering
    global command_in_progress
    global debug

    if command_in_progress:
        log_info(tag + 'ALREADY PROCESSING COMMAND')
        return True

    command_in_progress = True

    qname = qstate.qinfo.qname_str.rstrip('.').lower().replace(commandtld,'',1)
    rc = False
    if qname:
        if qname == 'reload':
            rc = True
            log_info(tag + 'Reloading lists')
            load_lists(False, savelists)
        elif qname == 'force.reload':
            rc = True
            log_info(tag + 'FORCE Reloading lists')
            load_lists(True, savelists)
        elif qname == 'update':
            rc = True
            log_info(tag + 'Updating lists')
            load_lists(False, False)
        elif qname == 'force.update':
            rc = True
            log_info(tag + 'Force updating lists')
            load_lists(True, False)
        elif qname == 'pause':
            rc = True
            if filtering:
                log_info(tag + 'Filtering PAUSED')
                filtering = False
                flush_dns_cache('.')
            else:
                log_info(tag + 'Filtering already PAUSED')
        elif qname == 'resume':
            rc = True
            if not filtering:
                log_info(tag + 'Filtering RESUMED')
                clear_cache()
                filtering = True
            else:
                log_info(tag + 'Filtering already RESUMED or Active')
        elif qname == 'save.cache':
            rc = True
            save_cache()
        elif qname == 'flush.cache':
            rc = True
            log_info(tag + 'Flushing Cache')
            clear_cache()
        elif qname == 'save.list':
            rc = True
            write_out(whitesave, blacksave)
        elif qname == 'maintenance':
            rc = True
            maintenance_lists(True)
        elif qname.endswith('.debug'):
            rc = True
            debug = int('.'.join(qname.split('.')[:-1]))
            log_info(tag + 'Set debug to \"' + str(debug) + '\"')
        elif qname.endswith('.add.whitelist'):
            rc = True
            domain = '.'.join(qname.split('.')[:-2])
            if not domain in whitelist:
                log_info(tag + 'Added \"' + domain + '\" to whitelist')
                whitelist[domain] = 'Whitelisted'
                if domain in blacklist:
                    blacklist.pop(domain, False)
                flush_dns_cache(domain)
        elif qname.endswith('.add.blacklist'):
            rc = True
            domain = '.'.join(qname.split('.')[:-2])
            if not domain in blacklist:
                log_info(tag + 'Added \"' + domain + '\" to blacklist')
                blacklist[domain] = 'Blacklisted'
                if domain in whitelist:
                    whitelist.pop(domain, False)
                flush_dns_cache(domain)
        elif qname.endswith('.del.whitelist'):
            rc = True
            domain = '.'.join(qname.split('.')[:-2])
            if domain in whitelist:
                log_info(tag + 'Removed \"' + domain + '\" from whitelist')
                whitelist.pop(domain, False)
                clear_cache()
        elif qname.endswith('.del.blacklist'):
            rc = True
            domain = '.'.join(qname.split('.')[:-2])
            if domain in blacklist:
                log_info(tag + 'Removed \"' + domain + '\" from blacklist')
                blacklist.pop(domain, False)
                clear_cache()

    if rc:
        log_info(tag + 'DONE')

    command_in_progress = False
    return rc


# Save cache to file
def save_cache():
    tag = 'DNS-FIREWALL CACHE: '

    log_info(tag + 'Saving cache')
    try:
        with open(cachefile, 'w') as f:
	    for line in dom_sort(blackcache.keys()):
                f.write('BLACK:' + line)
                f.write('\n')
            for line in dom_sort(whitecache.keys()):
                f.write('WHITE:' + line)
                f.write('\n')

    except BaseException as err:
        log_err(tag + 'Unable to open file \"' + cachefile + '\": ' + str(err))

    return True


# Unload/Finish-up
def deinit(id):
    tag = 'DNS-FIREWALL DE-INIT: '
    log_info(tag + 'Shutting down')

    if savelists:
        save_cache()
	write_out('/etc/unbound/whitelist.exit','/etc/unbound/blacklist.exit')

    log_info(tag + 'DONE!')
    return True


# Sub-Query
def inform_super(id, qstate, superqstate, qdata):
    tag = 'DNS-FIREWALL INFORM-SUPER: '
    log_info(tag + 'HI!')
    return True


def get_asn(qname, ip):
    tag = 'DNS-FIREWALL ASN: '

    asn = False
    prefix = False

    # Check if we have ASN Prefix for baseip
    if ip.find(':') == -1:
        if ip in asncache4:
            asn = asncache4[ip]
            prefix = asncache4.get_key(ip)
    else:
        if ip in asncache6:
            asn = asncache6[ip]
            prefix = asncache6.get_key(ip)
  
    # If no ASN, do whois and get it
    if (not asn) or (not prefix):
        whois = Client()
        lookup = whois.lookup(ip)
        asn = lookup.asn

        if asn and asn != '' and asn != 'NA':
            prefix = lookup.prefix
            asn = 'AS' + str(asn)
        else:
            asn = 'AS-NONE'

        if asn and prefix and ipregex.match(prefix):
            if prefix.find(':') == -1:
                asncache4[prefix] = asn
            else:
                asncache6[prefix] = asn
        else:
            prefix = 'NO-PREFIX'

        source = "WHOIS"
    else:
        source = "CACHE/DATABASE"

    if (debug >= 2): log_info(tag + 'Got ASN \"' + asn + '\" from ' + source + ' for ' + qname + '/' + ip + ' (Prefix: ' + prefix + ')')

    return asn


# Check query-responses agains safe nameserves.
# Compare answered based on ASN
# Provides a score in percentage of safety
# when score is below "safescore", blocking happens (see main response section)
def safe_dns(query, qname, type, baseip):
    tag = 'DNS-FIREWALL SAFEDNS: '

    # Check if we already have a score, if so return it
    if qname in asnscorecache or query in asnscorecache:
        score = asnscorecache[qname]
        if (debug >= 2): log_info(tag + 'Found cached \"' + qname + '\" score (' + str(score) + '%%)') 
        return score

    # Check if whitelisted
    if dom_find(qname, whitelist) or dom_find(query, whitelist) or check_ip(baseip, 'white'):
        score = 100
        if (debug >= 2): log_info(tag + 'Found white-listed ' + qname + '/' + baseip + ' score (' + str(score) + '%%)') 
        asnscorecache[qname] = score
        return score

    # Check if blacklisted
    if dom_find(qname, blacklist) or dom_find(query, blacklist) or check_ip(baseip, 'black'):
        score = 0
        if (debug >= 2): log_info(tag + 'Found black-listed ' + qname + '/' + baseip + ' score (' + str(score) + '%%)') 
        asnscorecache[qname] = score
        return score

    score = False
    blockit = False
    baseasn = False

    # Check if we have ASN Prefix for baseip
    if (debug >= 2): log_info(tag + 'Checking ' + qname + ' against DEFAULT')
    if (debug >= 2): log_info(tag + 'DEFAULT returned ' + qname + ' = ' + baseip) 
    baseasn = get_asn(qname, baseip)

    # ASN Black/whitelisted? Score accordingly and break
    if baseasn in asnwhitelist:
        if (debug >= 2): log_info(tag + 'Found white-listed \"' + qname + '\" BASE-ASN: \"' + baseasn + '\" (' + asnwhitelist[baseasn] + ')') 
        score = 100
    elif baseasn in asnblacklist:
        if (debug >= 2): log_info(tag + 'Found black-listed \"' + qname + '\" BASE-ASN: \"' + baseasn + '\" (' + asnblacklist[baseasn] + ')') 
	blockit = True
        score = 0

    if (debug >= 2): log_info(tag + 'DEFAULT: ' + qname + '/' + baseip + ' belongs to ASN: \"' + baseasn + '\"')

    # Build up hits base
    hits = dict()
    hits[baseasn] = 1

    # Initiate DNS resolver
    resolver = dns.resolver.Resolver(configure=False)

    # Resolution should happen in a total of <lifetime> seconds, retry after <timeout> seconds
    resolver.lifetime = 2
    resolver.timeout = 0.75

    # Query nameservers, get ASN and compare
    if not score and not blockit:
        for ns in sorted(nameservers.keys()):
            if (debug >= 2): log_info(tag + 'Checking ' + qname + ' against ' + ns)
            nsip = nameservers[ns].split(',')
            if nsip:
                # Shuffle IP's to to randomization of nameservers used
                shuffle(nsip)

                # Update nameservers to use for query
                resolver.nameservers = nsip

                # Do query, process response and update accordingly
                response = False
                try:
                    response = resolver.query(qname, type)
                except (dns.resolver.NXDOMAIN):
                    response = 'NXDOMAIN'
                except (dns.resolver.NoAnswer):
                    response = 'NOANSWER'
                except (dns.resolver.Timeout):
                    response = 'TIMEOUT'
                except BaseException as err:
                    response = 'ERROR'
                    log_err(tag + 'ASN-DNS resolution error using ' + ns + ': ' + str(err))
                    #print "SAFEDNS ERROR:", qname, baseip, ns, nsip, err

                # Catch all response
                if not response:
                    response = 'NOANSWER'

                # When NXDOMAIN or NOANSWER, treat this as hits, all servers should do the same basically
                if response in ('NXDOMAIN', 'NOANSWER'):
                    if (debug >= 2): log_info(tag + ns + ' returned ' + qname + ' = ' + response) 
                    if response in hits:
                        hits[response] += 1
                    else:
                        hits[response] = 1
        
                # Get ASN for response and process
                elif response not in ('ERROR', 'TIMEOUT'):
                    # Loop through answers (IP's)
                    for answer in response:
                        ip = str(answer.address)
                        if ip and ipregex.match(ip):
                            if ip == '0.0.0.0' or ip.startswith('127.0.0.'):
                                if (debug >= 2): log_info(tag + '\"' + qname + '\" is DNS-black-listed by ' + ns + ' (' + ip + ')') 
                                blockit = True
                                score = 0
                                break

                            if (debug >= 2): log_info(tag + ns + ' returned ' + qname + ' = ' + ip) 

                            # Get ASN
                            asn = get_asn(qname, ip)

                            if (debug >= 2): log_info(tag + ns + ': ' + qname + '/' + ip + ' belongs to ASN: \"' + asn + '\"') 

                            # ASN Black/whitelisted? Score accordingly and break
                            if asn in asnwhitelist:
                                if (debug >= 2): log_info(tag + 'Found white-listed \"' + qname + '\" ASN: \"' + asn + '\" (' + asnwhitelist[asn] + ')') 
                                score = 100
                                break
                            elif asn in asnblacklist:
                                if (debug >= 2): log_info(tag + 'Found black-listed \"' + qname + '\" ASN: \"' + asn + '\" (' + asnblacklist[asn] + ')') 
                                score = 0
                                blockit = True
                                break

                            # Update hits
                            if asn in hits:
                                hits[asn] += 1
                            else:
                                hits[asn] = 1

            if blockit:
                break
   
        # Process score/hits
        if blockit:
            if (debug >= 2): log_info(tag + '\"' + qname + '\" score: ' + str(score) + '%%')
            score = 0
        elif not score:
            if hits:
                # Get ASN with most hits and make that the base ASN
                baseasn = max(hits, key=hits.get)

                # calculate counts/totals
                total = sum(hits.values())
                count = hits[baseasn]

                # calculate score
                if count < total:
                    score = int('{0:.0f}'.format((float(count) / float(total) * 100)))
                else:
                    score = 100
            else:
                score = 100

            if (debug >= 2): log_info(tag + '\"' + qname + '\" Base-ASN \"' + baseasn + '\" score: ' + str(score) + '%%')


    # Catch all if everything fails. Use 0 to block to enfore and rely on stable access, use 100 to pass anyway
    if not score and not blockit:
        score = 100
        if (debug >= 2): log_info(tag + '\"' + qname + '\" Base-ASN \"' + baseasn + '\" CATCH-ALL score: ' + str(score) + '%%')

    # Update cache
    asnscorecache[qname] = score
    return score


# Main beef/process
def operate(id, event, qstate, qdata):
    tag = 'DNS-FIREWALL INIT: '

    global tagcount

    tagcount += 1

    if maintenance and ((tagcount) % maintenance == 0):
        start_new_thread(maintenance_lists, (True,))

    cip = client_ip(qstate)

    # New query or new query passed by other module
    if event == MODULE_EVENT_NEW or event == MODULE_EVENT_PASS:

	if cip == '0.0.0.0':
            qstate.ext_state[id] = MODULE_WAIT_MODULE
            return True

        tag = 'DNS-FIREWALL ' + cip + ' QUERY (#' + str(tagcount) + '): '

        # Get query name
        qname = qstate.qinfo.qname_str.rstrip('.').lower()
        if qname:
            if cip == '127.0.0.1' and (qname.endswith(commandtld)):
                start_new_thread(execute_command, (qstate,))

                qstate.return_rcode = RCODE_NXDOMAIN
                qstate.ext_state[id] = MODULE_FINISHED
                return True

            qtype = qstate.qinfo.qtype_str.upper()

            if (debug >= 2): log_info(tag + 'Started on \"' + qname + '\" (RR:' + qtype + ')')

            blockit = False

            # Check if whitelisted, if so, end module and DNS resolution continues as normal (no filtering)
            if not in_list(qname, 'white', 'QUERY', qtype):

                # Check if blacklisted, if so process and block
                if in_list(qname, 'black', 'QUERY', qtype):
                    blockit = True

                    # Create response
                    target = generate_response(qstate, qname, qtype, qstate.qinfo.qtype, cachettl)
                    if target:
                        if (debug >= 1): log_info(tag + 'REDIRECTED \"' + qname + '\" (RR:' + qtype + ') to ' + target)
                        qstate.return_rcode = RCODE_NOERROR
                    else:
                        if (debug >= 1): log_info(tag + 'REFUSED \"' + qname + '\" (RR:' + qtype + ')')
                        qstate.return_rcode = RCODE_REFUSED

            if (debug >= 2): log_info(tag + 'Finished on \"' + qname + '\" (RR:' + qtype + ')')

            if blockit:
                qstate.ext_state[id] = MODULE_FINISHED
                return True

        # Not blacklisted, Nothing to do, all done
        qstate.ext_state[id] = MODULE_WAIT_MODULE
        return True

    if event == MODULE_EVENT_MODDONE:

	if cip == '0.0.0.0':
            qstate.ext_state[id] = MODULE_FINISHED
            return True

        tag = 'DNS-FIREWALL ' + cip + ' RESPONSE (#' + str(tagcount) + '): '

        if checkresponse:
            # Do we have a message
            msg = qstate.return_msg
            if msg:
                # Response message
                rep = msg.rep
                rc = rep.flags & 0xf
                if (rc == RCODE_NOERROR) or (rep.an_numrrsets > 0):
                    # Initialize base variables
                    name = False
                    dname = False
                    blockit = False
                    newttl = cachettl

                    # Get query-name and type and see if it is in cache already
                    qname = qstate.qinfo.qname_str.rstrip('.').lower()
                    if qname:
                        # catchall if it is a command
                        if cip == '127.0.0.1' and (qname.endswith(commandtld)):
                            qstate.return_rcode = RCODE_NXDOMAIN
                            qstate.ext_state[id] = MODULE_FINISHED
                            return True

                        qtype = qstate.qinfo.qtype_str.upper()
                        if (debug >= 2): log_info(tag + 'Starting on RESPONSE for QUERY \"' + qname + '\" (RR:' + qtype + ')')

                        # If query was already whitelisted, bail.
                        if not in_cache('white', qname):
                            if not in_cache('black', qname):
                                # Pre-set some variables for cname collapsing
                                if collapse:
                                    firstname = False
                                    firstttl = False
                                    firsttype = False
                                    lastname = dict()

                                # Loop through RRSets
                                for i in range(0,rep.an_numrrsets):
                                    rk = rep.rrsets[i].rk
                                    type = rk.type_str.upper()
                                    dname = rk.dname_str.rstrip('.').lower()

                                    if collapse and i == 0 and type == 'CNAME':
                                        firstname = dname
                                        firstttl = rep.ttl
                                        firsttype = type

                                    # Start checking if black/whitelisted
                                    if dname:
                                        if not in_list(dname, 'white', 'RESPONSE', type):
                                            if not in_list(dname, 'black', 'RESPONSE', type):

                                                # Not listed yet, lets get data
                                                data = rep.rrsets[i].entry.data

                                                # Loop through data records
                                                for j in range(0,data.count):

                                                    # get answer section
                                                    answer = data.rr_data[j]

                                                    # Check if supported ype to record-type
                                                    if type in ('A', 'AAAA', 'CNAME', 'MX', 'NS', 'PTR', 'SOA', 'SRV'):
                                                        # Fetch Address or Name based on record-Type
                                                        if type == 'A':
                                                            name = "%d.%d.%d.%d"%(ord(answer[2]),ord(answer[3]),ord(answer[4]),ord(answer[5]))
                                                        elif type == 'AAAA':
                                                            name = "%02x%02x:%02x%02x:%02x%02x:%02x%02x:%02x%02x:%02x%02x:%02x%02x:%02x%02x"%(ord(answer[2]),ord(answer[3]),ord(answer[4]),ord(answer[5]),ord(answer[6]),ord(answer[7]),ord(answer[8]),ord(answer[9]),ord(answer[10]),ord(answer[11]),ord(answer[12]),ord(answer[13]),ord(answer[14]),ord(answer[15]),ord(answer[16]),ord(answer[17]))
                                                        elif type in ('CNAME', 'NS'):
                                                            name = decode_data(answer,0)
                                                        elif type == 'MX':
                                                            name = decode_data(answer,1)
                                                        elif type == 'PTR':
                                                            name = decode_data(answer,0)
                                                        elif type == 'SOA':
                                                            name = decode_data(answer,0).split(' ')[0][0].strip('.')
                                                        elif type == 'SRV':
                                                            name = decode_data(answer,5)
                                                        else:
                                                            # Not supported
                                                            name = False

                                                        # If we have a name, process it
                                                        if name:
                                                            if (debug >= 2): log_info(tag + 'Checking \"' + dname + '\" -> \"' + name + '\" (RR:' + type + ') (TTL:' + str(rep.ttl) + ')')

                                                            # Not Whitelisted?
                                                            notwhitelisted = True
                                                            if not in_list(name, 'white', 'RESPONSE', type):
                                                                # Blacklisted?
                                                                if in_list(name, 'black', 'RESPONSE', type):
                                                                    blockit = True
                                                                    break
                                                            else:
                                                                notwhitelisted = False

                                                            if safedns and notwhitelisted and type in ('A', 'AAAA'):
                                                                # SafeDNS ASN Check
                                                                if (debug >= 2): log_info(tag + 'Starting SafeDNS-Check on \"' + dname + '\"')

                                                                score = safe_dns(qname, dname, type, name)

								if (debug >= 1): log_info(tag + 'SafeDNS-Score for \"' + dname + '\" (' + name + ') is ' + str(score) + '%%')
                                                                
                                                                if score < safescore:
                                                                    newttl = 10
                                                                    if safednsblock:
                                                                        if (debug >= 1): log_info(tag + 'SafeDNS HIT on \"' + dname + '\", score below '+ str(safescore) + '%%, BLOCKING!')
                                                                        blockit = True
                                                                        break
                                                                    else:
                                                                        if (debug >= 1): log_info(tag + 'SafeDNS HIT on \"' + dname + '\", score below '+ str(safescore) + '%%, MONITORING!')

                                                                add_to_cache('white', dname) # !!! Need to check impact

                                                                if (debug >= 2): log_info(tag + 'Finished SafeDNS-Check on \"' + dname + '\"')

                                                            if collapse and firstname and type in ('A', 'AAAA'):
                                                                lastname[name] = type

                                                            if notwhitelisted and autowhitelist:
                                                                if (debug >= 2): log_info(tag + 'Auto-Whitelisted \"' + qname + '\"')
                                                                add_to_cache('white', name) # !!!! Maybe not, maybe have a "validated" cache instead

                                                    else:
                                                        # If not an A, AAAA, CNAME, MX, PTR, SOA or SRV we stop processing and passthru
                                                        if (debug >= 2): log_info(tag + 'Ignoring RR-type ' + type)
                                                        blockit = False
                                                        break

                                            else:
                                                # dname Response Blacklisted
                                                blockit = True
                                                break

                                        else:
                                            # dname Response Whitelisted
                                            blockit = False
                                            break

                                    else:
                                        # Nothing to process
                                        blockit = False
                                        break

                            else:
                                # qname in black cache
                                blockit = True
    
                            if blockit and autowhitesafelist and qname in safeunwhitelist:
                                if qname not in safewhitelist:
                                        log_info(tag + 'Auto White-Safelisted \"' + qname + '\"')
					safewhitelist[qname] = 'Auto Safelisted'

                                blockit = False

                            # Block it and generate response accordingly, other wise DNS resolution continues as normal
                            if blockit:
                                if name and dname:
                                    # Block based on response
                                    rname = name
                                    lname = dname + " -> " + name
                                    rtype = type

                                    # Add query-name to black-cache
                                    if not in_cache('black', qname):
                                        add_to_cache('black', qname)
 
                                else:
                                    # Block based on query
                                    rname = qname
                                    lname = qname
                                    rtype = qtype

                                # Add response-name to the black-cache
                                if not in_cache('black', rname):
                                    add_to_cache('black', rname)

                                # Generate response based on query-name
                                target = generate_response(qstate, qname, qtype, qstate.qinfo.qtype, newttl)
                                if target:
                                    if (debug >= 1): log_info(tag + 'REDIRECTED \"' + lname + '\" (RR:' + rtype + ') to ' + target)
                                    qstate.return_rcode = RCODE_NOERROR
                                else:
                                    if (debug >= 1): log_info(tag + 'REFUSED \"' + lname + '\" (RR:' + rtype + ')')
                                    qstate.return_rcode = RCODE_REFUSED

                            elif collapse and lastname:
                                rmsg = DNSMessage(firstname, RR_TYPE_A, RR_CLASS_IN, PKT_QR | PKT_RA )
                                for lname in lastname.keys():
                                    if (debug >= 2): log_info (tag + 'COLLAPSE CNAME \"' + firstname + '\" -> ' + lastname[lname] + ' \"' + lname + '\"')
                                    rmsg.answer.append('%s %d IN %s %s' % (firstname, firstttl, lastname[lname], lname))

                                rmsg.set_return_msg(qstate)
                                if not rmsg.set_return_msg(qstate):
                                    log_err(tag + 'CNAME COLLAPSE ERROR: ' + str(rmsg.answer))
                                    return False

                                if qstate.return_msg.qinfo:
                                    invalidateQueryInCache(qstate, qstate.return_msg.qinfo)

                                qstate.no_cache_store = 0
                                storeQueryInCache(qstate, qstate.return_msg.qinfo, qstate.return_msg.rep, 0)

                                qstate.return_msg.rep.security = 2

                                qstate.return_rcode = RCODE_NOERROR

                            if autowhitelist:
                                if (debug >= 2): log_info(tag + 'Auto-Whitelisted \"' + qname + '\"')
                                add_to_cache('white', qname) # !!!! Maybe not, maybe have a "validated" cache instead

                        if (debug >= 2): log_info(tag + 'Finished on RESPONSE for QUERY \"' + qname + '\" (RR:' + qtype + ')')

        # All done
        qstate.ext_state[id] = MODULE_FINISHED
        return True

    # Oops, non-supported event
    log_err('pythonmod: BAD Event')
    qstate.ext_state[id] = MODULE_ERROR
    return False

# <EOF>
