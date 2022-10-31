#!/usr/bin/env python3
"""
    Manage the printing queue for local printers
"""

import argparse
import getpass
import time
import json
import requests

CLIENT = "ciab"

def connect(server, account, password):
    ''' Connect to a server '''
    url = server+"/api/token"
    param = {'grant_type':'password', 'username':account,
             'password':password, 'client_id': CLIENT}
    session = requests.post(url, data=param)
    access = session.json()['access_token']
    refresh = session.json()['refresh_token']
    return (access, refresh)

def renew(server, token):
    ''' renew a token '''
    url = server+"/api/token"
    param = {'grant_type' :'refresh_token',
             'refresh_token': token,
             'client_id' : CLIENT}
    session = requests.post(url, data=param)
    access = session.json()['access_token']
    refresh = session.json()['refresh_token']
    return (access, refresh)

def get_queue(server, access):
    '''  Get the current print queue '''
    data = requests.get(server + "/api/registration/ticket/printqueue",
                         headers={'Authorization': 'Bearer ' + access},
                         data={})
    output = data.json()
    return output


def claim_and_print(ticket, access):
    ''' Claim and ticket and print it '''
    url = ticket['claim']['href']
    print(url)
    data = requests.put(url,
                        headers={'Authorization': 'Bearer ' + access},
                        data={})
    output = data.json()

    ####################
    ## PRINTING WORK HERE
    ####################
    #
    print("TODO: DO THE WORK HERE")
    print(json.dumps(output, indent=4, sort_keys=True))
    #

if __name__ == "__main__":

    parser = argparse.ArgumentParser(
        description='This manages the print queue for badges in CIAB')
    parser.add_argument('--admin', action="store", dest="email",
                        help='User with admin privilages to the print queue.')
    parser.add_argument('--password', action="store", dest="password",
                        help='Password for the admin_user account.')
    parser.add_argument('--server', action="store", dest="server",
                        help='Server for CIAB in <server>:<port> format.')

    ARGS = parser.parse_args()

    if ARGS.server is None:
        ARGS.server = str(input("CIAB Server: "))
    if 'http' not in ARGS.server:
        ARGS.server = 'https://' + ARGS.server
    if ARGS.email is None:
        ARGS.email= str(input("Administrator email: "))
    if ARGS.password is None:
        ARGS.password = getpass.getpass("Administrator password: ")

    ACCESS, REFRESH = connect(ARGS.server, ARGS.email, ARGS.password)

    print ("<--  starting up -->")
    while True:
        queue = get_queue(ARGS.server, ACCESS)
        if 'error' in queue:
            print ("<--  token expired renewing -->")
            if queue['error'] == 'invalid_token':
                ACCESS, REFRESH = renew(ARGS.server, REFRESH)

        if 'data' in queue and len(queue['data']) > 0:
            claim_and_print(queue['data'][0], ACCESS)
            time.sleep(0.1)
        else:
            time.sleep(1)
