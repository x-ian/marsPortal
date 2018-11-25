'''
'''
import datetime
import mysql.connector
from subprocess import Popen, PIPE
import os.path

restricted_block_domains = []
user_group_mapping = {}

def init(id, cfg): 
    
    cnx = mysql.connector.connect(user='radius', password='radpass', database='radius')
    cursor = cnx.cursor()

    # figure out which block file(s) to use base don work vs non-work mode
    #restricted_block_file = "/home/marsPortal/unbound/restricted-block.txt"
    restricted_block_file = ''
    query = ("select count(*) as nonwork from radusergroup where groupname like \'\%-non-work-hours\';")
    cursor.execute(query)
    nonwork = cursor.fetchone()
    if nonwork[0] == 0:
        log_info("marsmod: work mode")
        restricted_block_file = "/home/marsPortal/unbound/block-files/restricted-block.txt"
    else:
        log_info("marsmod: nonwork mode")
        restricted_block_file = "/home/marsPortal/unbound/block-files/restricted-block-non-work.txt"
    cursor.close()


    if os.path.isfile(restricted_block_file):
        log_info("marsmod: loading blockfile: {}".format(restricted_block_file))
        # Reading domains to lookup into the file
        with open(restricted_block_file) as file:
            for line in file: 
                restricted_block_domains.append(line.rstrip('\n'))

    # get user - group mapping from DB
    cursor = cnx.cursor()
    query = ("SELECT lcase(username), lcase(groupname) FROM radusergroup")
    cursor.execute(query)
    for (username, groupname) in cursor:
        log_info("marsmod: register user for group: {} {}".format(username, groupname))
        user_group_mapping[username] = groupname

    cursor.close()
    cnx.close()
    
    return True
    
def deinit(id): return True

def inform_super(id, qstate, superqstate, qdata): return True

def operate(id, event, qstate, qdata):
    #print "Operate", event,"state:",qstate

    # Please note that if this module blocks, by moving to the validator
    # to validate or iterator to lookup or spawn a subquery to look up,
    # then, other incoming queries are queued up onto this module and
    # all of them receive the same reply.
    # You can inspect the cache.

    if (event == MODULE_EVENT_NEW) or (event == MODULE_EVENT_PASS):
#        if any(qstate.qinfo.qname_str in s for s in users_block_domains):
        if any(qstate.qinfo.qname_str.endswith(s) for s in restricted_block_domains):

            # get client IP
            rl = qstate.mesh_info.reply_list
            client_ip = ""
            while(rl):
                if rl.query_reply:
                    q = rl.query_reply
                    client_ip = q.addr
                rl = rl.next
            if client_ip:
                # get mac from IP
                pid = Popen(["/home/marsPortal/misc/resolve_mac_address.sh", client_ip], stdout=PIPE)
                client_mac = pid.communicate()[0].rstrip('\n')
                # get marsPortal group from user
                client_group = user_group_mapping[client_mac]
                if (client_group == "restricted"):
                    # do something to block
                    #create instance of DNS message (packet) with given parameters
                    msg = DNSMessage(qstate.qinfo.qname_str, RR_TYPE_TXT, RR_CLASS_IN, PKT_QR | PKT_RA | PKT_AA)
                    msg.answer.append("%s 0 IN TXT \"%s %d (%s)\"" % (qstate.qinfo.qname_str, q.addr,q.port,q.family))
                    #msg.answer.append('%s 0 IN A %s' % (qstate.qinfo.qname_str, '192.168.10.49'))
                    log_info('marsmod: blocking %s for %s / %s' % (qstate.qinfo.qname_str, client_mac, client_ip))

                    #set qstate.return_msg 
                    if not msg.set_return_msg(qstate):
                        qstate.ext_state[id] = MODULE_ERROR 
                        log_err("marsmod: bad event1")
                        return True

                    #we don't need validation, result is valid
                    qstate.return_msg.rep.security = 2

                    qstate.return_rcode = RCODE_NOERROR
                    qstate.ext_state[id] = MODULE_FINISHED 
                    return True
                else:
                    #pass the query to validator
                    #log_info("marsmod 1: allowing %s " % qstate.qinfo.qname_str)
                    qstate.ext_state[id] = MODULE_WAIT_MODULE
                    return True
            else:
                #pass the query to validator
                #log_info("marsmod 2: allowing %s " % qstate.qinfo.qname_str)
                qstate.ext_state[id] = MODULE_WAIT_MODULE
                return True
        else:
            #pass the query to validator
            #log_info("marsmod 3: allowing %s " % qstate.qinfo.qname_str)
            qstate.ext_state[id] = MODULE_WAIT_MODULE 
            return True

    if event == MODULE_EVENT_MODDONE:
        #log_info("marsmod: iterator module done")
        #log_info("marsmod 4: allowing %s " % qstate.qinfo.qname_str)
        qstate.ext_state[id] = MODULE_FINISHED 
        return True
      
    log_err("marsmod: bad event2")
    qstate.ext_state[id] = MODULE_ERROR
    return True
