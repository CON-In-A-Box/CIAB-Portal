##### [Return to Top](README.md)&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;[Return to Resource List](README.md#resources)
---
# Concom Module

The Concom module includes the following methods.

## Common Resources

<a name="concom_list"></a>
#### concom\_entry object resource

A concom\_entry object resource is used when listing concom members. Use a member get method to retrieve the full member data.

```
{
	"type":"concom_entry",
	"memberId":{integer},
	"note":{string},
	"position":{string},
	"departmentId": {integer}
	"links":[{HATEOAS links}]
}
```

concom\_entry object resources have the following available properties:

|Object Property|Value|Description|
|---|---|---|
|note|string|Note for the entry|
|memberId|integer|Member ID for this entry|
|position|string|Concom position|
|type|string|Always `concom_entry`|
|departmentId|integer|`id` of the department for this position|
|links[]|[list]|HATEOAS links for the resource|

The following HATEOAS methods are available as well:

|HATEOAS Method|Request|Description|
|---|---|---|
|member|GET|Get the member resource for the member.|
|department|GET|Get the department resource for the department.|


#### concom\_list object resource

A concom\_list object resource is used when listing concom members.

```
{
	"type": "concom_list",
	"event": {integer},
	"data": {list}
}
```

concom\_list object resources have the following available properties:

|Object Property|Value|Description|
|---|---|---|
|type|string|Always "concom\_list"|
|event|integer|The `id` of the event being listed|
|data[]|[concom\_entry]|A matching list of members in the concom.|


<a name="department"></a>
## department
Get a list of concom for a given department.

### department Request

```GET /department/{identifier}/concom ```

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
A [concom\_list](#concom_list) object resource.

### department Code Samples
Request Sample

```
curl -X GET -H 'Authorization: Bearer e0438d90599b1c4762d12fd03db6311c9ca46729' \
    http://localhost/api/department/Art%20Show/concom?pretty=true
```
Response Sample

```
{
    "type": "concom_list",
    "event": "1",
    "data": [
        {
            "type": "concom_entry",
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
            "type": "concom_entry",
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
            "type": "concom_entry",
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
## concom list
Get a list of concom for an event.

### concom list Request

```GET /department/concom/```

### concom list Parameters

The following concom list parameters are available:

|Parameter|Meaning|Notes|
|---|---|---|
|page|Page token for the list.|Defaults to first page.|
|maxResults|Maximum results in the list|Defaults to 100. The token "all" specifies the full remaining list.|


### concom list Request Body

Do not supply a request body.

### concom list Response
A [concom\_list](#concom_list) object resource.

### concom list Code Samples
Request Sample

```
curl -X GET -H 'Authorization: Bearer e0438d90599b1c4762d12fd03db6311c9ca46729' \
    http://localhost/api/department/concom/?pretty=true
```
Response Sample

```
{
    "type": "concom_list",
    "event": "1",
    "data": [
        {
            "type": "concom_entry",
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
            "type": "concom_entry",
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
Get a list of concom for an member.

### member list Request

```GET /member/{id}/concom```

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
A [concom\_list](#concom_list) object resource.

### member list Code Samples
Request Sample

```
curl -X GET -H 'Authorization: Bearer e0438d90599b1c4762d12fd03db6311c9ca46729' \
    http://localhost/api/member/1002/concom?pretty=true
```
Response Sample

```
{
    "type": "concom_list",
    "event": "1",
    "data": [
        {
            "type": "concom_entry",
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
