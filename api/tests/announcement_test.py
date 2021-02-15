""" Test for announcement APIs """
import requests

from tests import SERVER, ADMIN_ACCESS, BASE_ACCESS
from tests.framework import assert_status_code

def add_announcement():
    """ Add a new announcement """
    response = requests.get(SERVER+"/api/department/1/announcements",
                   headers={'Authorization': 'Bearer '+ADMIN_ACCESS},
                   data={})
    assert_status_code(response, 200)
    data = response.json()
    initial_ids = []
    for item in data['data']:
        initial_ids.append(item['id'])

    response = requests.post(SERVER+"/api/announcement/1",
                   headers={'Authorization': 'Bearer '+ADMIN_ACCESS},
                   data={'Scope':'2',
                         'Text': 'testing',
                         'Email': '0'})
    assert_status_code(response, 201)

    response = requests.get(SERVER+"/api/department/1/announcements",
                   headers={'Authorization': 'Bearer '+ADMIN_ACCESS},
                   data={})
    assert_status_code(response, 200)
    data = response.json()
    # Find New Index
    target = None
    for item in data['data']:
        if target is None and item['id'] not in initial_ids:
            target = item['id']

    response = requests.get(SERVER+"/api/announcement/"+target,
                   headers={'Authorization': 'Bearer '+ADMIN_ACCESS},
                   data={})
    assert_status_code(response, 200)
    item = response.json()
    assert 'type' in item
    assert item['type'] == 'announcement'
    assert 'id' in item
    assert item['id'] == target
    assert 'departmentId' in item
    assert item['departmentId'] == '1'
    assert 'postedOn' in item
    assert 'postedBy' in item
    assert 'scope' in item
    assert item['scope'] == '2'
    assert 'text' in item
    assert item['text'] == 'testing'

    return target

def test_announce_priv():
    """ Getting the announcments as a privilaged user """

    target = add_announcement()

    response = requests.get(SERVER+"/api/department/1/announcements",
                   headers={'Authorization': 'Bearer '+ADMIN_ACCESS},
                   data={})
    assert_status_code(response, 200)
    data = response.json()
    assert 'data' in data
    assert 'type' in data
    assert data['type'] == 'announcement_list'
    item = data['data'][0]
    assert 'type' in item
    assert item['type'] == 'announcement'
    assert 'id' in item
    assert 'departmentId' in item
    assert item['departmentId'] == '1'
    assert 'postedOn' in item
    assert 'postedBy' in item
    assert 'scope' in item
    assert 'text' in item

    response = requests.put(SERVER+"/api/announcement/"+target,
                   headers={'Authorization': 'Bearer '+ADMIN_ACCESS},
                   data={'Text': 'New Message'})
    assert_status_code(response, 200)

    response = requests.get(SERVER+"/api/announcement/"+target,
                   headers={'Authorization': 'Bearer '+ADMIN_ACCESS},
                   data={})
    assert_status_code(response, 200)
    item = response.json()
    assert 'type' in item
    assert item['type'] == 'announcement'
    assert 'id' in item
    assert item['id'] == target
    assert 'departmentId' in item
    assert item['departmentId'] == '1'
    assert 'postedOn' in item
    assert 'postedBy' in item
    assert 'scope' in item
    assert item['scope'] == '2'
    assert 'text' in item
    assert item['text'] == 'New Message'

    response = requests.delete(SERVER+"/api/announcement/"+target,
                   headers={'Authorization': 'Bearer '+ADMIN_ACCESS},
                   data={})
    assert_status_code(response, 204)
    assert response.text == ''


def test_announce_unpriv():
    """ Getting the announcments as a unprivilaged user """
    response = requests.get(SERVER+"/api/department/1/announcements",
                   headers={'Authorization': 'Bearer '+BASE_ACCESS},
                   data={})
    assert_status_code(response, 200)
    data = response.json()
    assert 'data' in data
    assert 'type' in data
    assert data['type'] == 'announcement_list'
    if len(data['data']) > 0:
        item = data['data'][0]
        assert 'type' in item
        assert item['type'] == 'announcement'
        assert 'id' in item
        assert 'departmentId' in item
        assert item['departmentId'] == '1'
        assert 'postedOn' in item
        assert 'postedBy' in item
        assert 'scope' in item
        assert 'text' in item
    initial_ids = []
    for item in data['data']:
        initial_ids.append(item['id'])

    if len(initial_ids) > 0 :
        response = requests.post(SERVER+"/api/announcement/1",
                       headers={'Authorization': 'Bearer '+BASE_ACCESS},
                       data={'Scope':'2',
                             'Text': 'testing',
                             'Email': '0'})
        assert_status_code(response, 403)

        response = requests.put(SERVER+"/api/announcement/"
                                +initial_ids[0],
                       headers={'Authorization': 'Bearer '+BASE_ACCESS},
                       data={'Text': 'New Message'})
        assert_status_code(response, 403)

        response = requests.delete(SERVER+"/api/announcement/"
                                   +initial_ids[0],
                       headers={'Authorization': 'Bearer '+BASE_ACCESS},
                       data={})
        assert_status_code(response, 403)

def test_announce_not_found():
    """ Getting the announcments with invalid id"""
    response = requests.get(SERVER+"/api/department/1/announcements",
                   headers={'Authorization': 'Bearer '+ADMIN_ACCESS},
                   data={})
    assert_status_code(response, 200)
    data = response.json()
    initial_ids = []
    for item in data['data']:
        initial_ids.append(item['id'])

    target = None
    for i in range(0, len(initial_ids) + 1):
        if target is None and i not in initial_ids:
            target = str(i)

    response = requests.post(SERVER+"/api/announcement",
                   headers={'Authorization': 'Bearer '+ADMIN_ACCESS},
                   data={'Scope':'2',
                         'Text': 'testing',
                         'Email': '0'})
    assert_status_code(response, 404)

    response = requests.get(SERVER+"/api/announcement/"+target,
                   headers={'Authorization': 'Bearer '+ADMIN_ACCESS},
                   data={})
    assert_status_code(response, 404)

    response = requests.put(SERVER+"/api/announcement/"+target,
                   headers={'Authorization': 'Bearer '+ADMIN_ACCESS},
                   data={'Text': 'New Message'})
    assert_status_code(response, 404)

    response = requests.delete(SERVER+"/api/announcement/"+target,
                   headers={'Authorization': 'Bearer '+ADMIN_ACCESS},
                   data={})
    assert_status_code(response, 404)

def test_announce_params():
    """ Getting the announcments with invalid params"""

    response = requests.post(SERVER+"/api/announcement/1",
                   headers={'Authorization': 'Bearer '+ADMIN_ACCESS},
                   data={'Text': 'testing',
                         'Email': '0'})
    assert_status_code(response, 400)

    response = requests.post(SERVER+"/api/announcement/1",
                   headers={'Authorization': 'Bearer '+ADMIN_ACCESS},
                   data={'Scope': '2',
                         'Email': '0'})
    assert_status_code(response, 400)

    response = requests.post(SERVER+"/api/announcement/1",
                   headers={'Authorization': 'Bearer '+ADMIN_ACCESS},
                   data={'Email': '0'})
    assert_status_code(response, 400)

    target = add_announcement()

    response = requests.put(SERVER+"/api/announcement/"+target,
                   headers={'Authorization': 'Bearer '+ADMIN_ACCESS},
                   data={})
    assert_status_code(response, 400)

    response = requests.put(SERVER+"/api/announcement/"+target,
                   headers={'Authorization': 'Bearer '+ADMIN_ACCESS},
                   data={'Department': '-1'})
    assert_status_code(response, 400)

    response = requests.delete(SERVER+"/api/announcement/"+target,
                   headers={'Authorization': 'Bearer '+ADMIN_ACCESS},
                   data={})
    assert_status_code(response, 204)
