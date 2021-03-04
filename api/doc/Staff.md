##### [Return to Top](README.md)&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;[Return to Resource List](README.md#resources)
---
# Staff Module

The Staff module includes the following methods.

## Common Resources

<a name="staff_list"></a>
#### staff\_entry object resource

A staff\_entry object resource is used when listing staff members. Use a member get method to retrieve the full member data.

```
{
	"type":"staff_entry",
	"id": {integer},
	"memberId":{integer},
	"note":{string},
	"position":{string},
	"departmentId": {integer}
	"links":[{HATEOAS links}]
}
```

staff\_entry object resources have the following available properties:

|Object Property|Value|Description|Includable|
|---|---|---|---|
|note|string|Note for the entry|-|
|id|integer|Resource ID for this record|-|
|memberId|integer|Member ID for this entry|**yes** `member`|
|position|string|Staff position|-|
|type|string|Always `staff_entry`|-|
|departmentId|integer|`id` of the department for this position|**yes** `department`|
|links[]|[list]|HATEOAS links for the resource|-|

The following HATEOAS methods are available as well:

|HATEOAS Method|Request|Description|
|---|---|---|
|member|GET|Get the member resource for the member.|
|department|GET|Get the department resource for the department.|


#### staff\_list object resource

A staff\_list object resource is used when listing staff members.

```
{
	"type": "staff_list",
	"event": {integer},
	"data": {list}
}
```

staff\_list object resources have the following available properties:

|Object Property|Value|Description|
|---|---|---|
|type|string|Always "staff\_list"|
|event|integer|The `id` of the event being listed|
|data[]|[staff\_entry]|A matching list of members in the staff.|


<a name="department"></a>
## department
Get a list of staff for a given department.

### department Request

```GET /department/{identifier}/staff ```

### department Parameters

The following department parameters are available:

|Parameter|Meaning|Notes|
|---|---|---|
|page|Page token for the list.|Defaults to first page.|
|maxResults|Maximum results in the list|Defaults to 100. The token "all" specifies the full remaining list.|
|event|The event being queries|Defaults to the current event|

### department Request Body

Do not supply a request body.

### department Response
A [staff\_list](#staff_list) object resource.

### department Code Samples
Request Sample

```
curl -X GET -H 'Authorization: Bearer e0438d90599b1c4762d12fd03db6311c9ca46729' \
    http://localhost/api/department/Art%20Show/staff?pretty=true
```
Response Sample

```
{
    "type": "staff_list",
    "event": "1",
    "data": [
        {
            "type": "staff_entry",
            "id": "45",
            "memberId": "1405",
            "note": "",
            "position": "Head",
            "departmentId": "104",
            "links": [
                {
                    "method": "member",
                    "href": "http:\/\/localhost/api\/member\/1405",
                    "request": "GET"
                },
                {
                    "method": "department",
                    "href": "http:\/\/localhost\/api\/department\/104",
                    "request": "GET"
                }
            ]
        },
        {
            "type": "staff_entry",
            "id": "46",
            "memberId": "1458",
            "note": "",
            "position": "Sub-Head",
            "departmentId": "104",
            "links": [
                {
                    "method": "member",
                    "href": "http:\/\/localhost/api\/member\/1458",
                    "request": "GET"
                },
                {
                    "method": "department",
                    "href": "http:\/\/localhost\/api\/department\/104",
                    "request": "GET"
                }
            ]
        },
        {
            "type": "staff_entry",
            "id": "47",
            "memberId": "1228",
            "note": "",
            "position": "Specialist",
            "departmentId": "104",
            "links": [
                {
                    "method": "member",
                    "href": "http:\/\/localhost/api\/member\/1228",
                    "request": "GET"
                },
                {
                    "method": "department",
                    "href": "http:\/\/localhost\/api\/department\/104",
                    "request": "GET"
                }
            ]
        }
    ]
}
```

<a name="list"></a>
## staff list
Get a list of staff for an event.

### staff list Request

```GET /department/staff/```

### staff list Parameters

The following staff list parameters are available:

|Parameter|Meaning|Notes|
|---|---|---|
|page|Page token for the list.|Defaults to first page.|
|maxResults|Maximum results in the list|Defaults to 100. The token "all" specifies the full remaining list.|


### staff list Request Body

Do not supply a request body.

### staff list Response
A [staff\_list](#staff_list) object resource.

### staff list Code Samples
Request Sample

```
curl -X GET -H 'Authorization: Bearer e0438d90599b1c4762d12fd03db6311c9ca46729' \
    http://localhost/api/department/staff/?pretty=true
```
Response Sample

```
{
    "type": "staff_list",
    "event": "1",
    "data": [
        {
            "type": "staff_entry",
            "id": "45",
            "memberId": "1000",
            "note": "",
            "position": "Head",
            "departmentId": "3",
            "links": [
                {
                    "method": "member",
                    "href": "http:\/\/localhost/api\/member\/1000",
                    "request": "GET"
                },
                {
                    "method": "department",
                    "href": "http:\/\/localhost\/api\/department\/3",
                    "request": "GET"
                }
            ]
        },
        {
            "type": "staff_entry",
            "id": "46",
            "memberId": "1000",
            "note": "",
            "position": "Head",
            "departmentId": "2",
            "links": [
                {
                    "method": "member",
                    "href": "http:\/\/localhost/api\/member\/1000",
                    "request": "GET"
                },
                {
                    "method": "department",
                    "href": "http:\/\/localhost\/api\/department\/2",
                    "request": "GET"
                }
            ]
        }
    ],
    "nextPageToken": 2
}
```

<a name="member"></a>
## member
Get a list of staff for an member.

### member list Request

```GET /member/{id}/staff```

### member list Parameters

The following member parameters are available:

|Parameter|Meaning|Notes|
|---|---|---|
|id|`id` of the Member|Defaults to current logged in member.|
|page|Page token for the list.|Defaults to first page.|
|maxResults|Maximum results in the list|Defaults to 100. The token "all" specifies the full remaining list.|


### member list Request Body

Do not supply a request body.

### member list Response
A [staff\_list](#staff_list) object resource.

### member list Code Samples
Request Sample

```
curl -X GET -H 'Authorization: Bearer e0438d90599b1c4762d12fd03db6311c9ca46729' \
    http://localhost/api/member/1002/staff?pretty=true
```
Response Sample

```
{
    "type": "staff_list",
    "event": "1",
    "data": [
        {
            "type": "staff_entry",
            "id": "45",
            "note": "",
            "memberId": "1002",
            "position": "Sub-Head",
            "departmentId": "156",
            "links": [
                {
                    "method": "member",
                    "href": "http:\/\/localhost/api\/member\/1002",
                    "request": "GET"
                },
                {
                    "method": "department",
                    "href": "http:\/\/localhost\/api\/department\/156",
                    "request": "GET"
                }
            ]
        }
    ]
}
```

---
##### [Return to Top](README.md)&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;[Return to Resource List](README.md#resources)
