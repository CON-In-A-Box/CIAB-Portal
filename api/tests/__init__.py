""" Base setting for test runs """
import json
from os import path
import requests

CONFIG = None
SERVER = "https://localhost:8080"

BASE = path.dirname(__file__)

if path.exists(path.join(BASE, '../../test_configuration.json')):
    CONFIG = path.join(BASE, '../../test_configuration.json')
elif path.exists(path.join(BASE,'test_configuration.json')):
    CONFIG = path.join(BASE, 'test_configuration.json')
if CONFIG is not None:
    with open(CONFIG) as json_file:
        data = json.load(json_file)
        SERVER = data['server']

CLIENT = "ciab"
URL = SERVER+"/api/token"
PARAM = {'grant_type':'password', 'username':data['admin_email'],
         'password':data['admin_password'], 'client_id': CLIENT}

SESSION = requests.post(URL, data=PARAM)
ADMIN_ACCESS = SESSION.json()['access_token']
ADMIN_REFRESH = SESSION.json()['refresh_token']

URL = SERVER+"/api/token"
PARAM = {'grant_type':'password', 'username':data['base_email'],
         'password':data['base_password'], 'client_id': CLIENT}

SESSION = requests.post(URL, data=PARAM)
BASE_ACCESS = SESSION.json()['access_token']
BASE_REFRESH = SESSION.json()['refresh_token']
