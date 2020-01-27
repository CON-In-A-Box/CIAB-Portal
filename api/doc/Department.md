##### [Return to Top](README.md)&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;[Return to Resource List](README.md#resources)
---
# Department Resource

The following methods are available to Department resources:


|Resource Method|HTTP Request|Description|Module|RBAC|
|---|---|---|---|---|
|[list](Department.md#list)|GET /department/|Get a list of departments at the convention.|core|--|
|[get](Department.md#get)|GET /department/{identifier}|Get details about a given Department.|core|--|
|[deadlines](Department.md#deadlines)|GET /department/{identifier}/deadlines|Get a list of deadlines for a given Department.|core|api.get.deadline.{department}|
|[concom](Concom.md#department)|GET /department/{identifier}/concom|Get a list of the ConCom for the department.|[concom](Concom.md)|api.get.concom|
|[concom list](Concom.md#list)|GET /department/concom/|Get a list of the whole ConCom.|[concom](Concom.md)|api.get.concom|

<a name="list"></a>
## list
Get a list of departments on the system.

### list Request

```GET /department/```

### list Parameters

The following list parameters are available:

|Parameter|Meaning|Notes|
|---|---|---|
|page|Page token for the list.|Defaults to first page.|
|maxResults|Maximum results in the list|Defaults to 100. The token "all" specifies the full remaining list.|

### list Request Body

Do not supply a request body.

### list Response
A department_entry object resource.

#### department_entry object resource

A department_entry object resource is used when listing departments. Use a department get method to retrieve the full department data.

```
{
	"type":"department_entry",
	"id":{integer},
	"get":{HATEOAS link}
}
```

department_entry object resources have the following available properties:

|Object Property|Value|Description|Includable|
|---|---|---|---|
|type|string|Always `department_entry`|-|
|id|integer|Department ID for this department.|**yes** `department`|
|get|HATEOAS link|A HATEOAS link for the get method on the department|-|


### list Code Samples
Request Sample

```
curl -X GET -H 'Authorization: Bearer e0438d90599b1c4762d12fd03db6311c9ca46729' \
    http://localhost/api/department/?pretty=true
```
Response Sample

```
{
    "type": "department_list",
    "data": {
        {
            "type": "department_entry",
            "id": 100,
            "get": "http:\/\/localhost\/api\/department\/100"
        },
        {
            "type": "department_entry",
            "id": 1,
            "get": "http:\/\/localhost\/api\/department\/1"
        },
        {
            "type": "department_entry",
            "id": 2,
            "get": "http:\/\/localhost\/api\/department\/2"
        },
        {
            "type": "department_entry",
            "id": 101,
            "get": "http:\/\/localhost\/api\/department\/101"
        },
        {
            "type": "department_entry",
            "id": 102,
            "get": "http:\/\/localhost\/api\/department\/102"
        }
    }
]
```

<a name="get"></a>
## get
Get details about a department

### get Request
```GET /department/{identifier}```

### get Parameters
The following get parameters are available:

|Parameter|Meaning|Notes|
|---|---|---|
|identifier|Name or Id of the department||

### get Request Body
Do not supply a request body.

### get Response

An email_entry resource.

```
{
	"email": {string},
	"isAlias": {boolean}
}
```

|Object Property|Value|Description|
|---|---|---|
|email|string|The email address|
|isAlias|boolean|if `true` then an alias|

A department resource.

```
{
    "type": "department",
    "name": {string},
    "division": {string},
    "id": {integer},
    "childCount": {integer},
    "email": [email_entry]
    "fallback": {integer},
    "links": [{HATEOAS links}]
}
```
|Object Property|Value|Description|Includable|
|---|---|---|---|
|type|string|Always "department"|-|
|name|string|Name of the department|-|
|division|string|Name of the division|**yes** `department`|
|id|integer|Id of the department|-|
|childCount|integer|Number of child departments|-|
|email[]|list|List of email_entry resources|-|
|fallback|integer|If set, the `id` of the fallback department. `null` if undefined.|**yes** `department`|
|links[]|list|HATEOAS links|-|

The following HATEOAS methods are available as well:

|HATEOAS Method|Request|Description|
|---|---|---|
|division|GET|Get the department resource for the departments parent division. Only present if the department has a parent.|
|deadlines|GET|Get a list of deadlines for the department.|


### get Code Samples
Request Sample

```
curl -X GET -H 'Authorization: Bearer e0438d90599b1c4762d12fd03db6311c9ca46729' \
   http://localhost:8080/api/department/127?pretty=true
```

Response Sample

```
{
    "name": "IT",
    "division": "Systems",
    "id": 127,
    "childCount": 0,
    "email": [
        {
            "email": "it@convergence-con.org"
            "isAlias": false,
        }
    ],
    "fallback": null,
    "type": "department",
    "links": [
        {
            "method": "self",
            "href": "http:\/\/localhost\/api\/department\/127",
            "request": "GET"
        },
        {
            "method": "division",
            "href": "http:\/\/localhost\/api\/department\/7",
            "request": "GET"
        }
    ]
}
```

<a name="deadlines"></a>
## deadlines
Get a list of deadlines for the department.

### deadlines Request

```GET /department/{identifier}/deadlines```

### deadlines Parameters

The following deadlines parameters are available:

|Parameter|Meaning|Notes|
|---|---|---|
|identifier|Name or Id of the department||
|page|Page token for the list.|Defaults to first page.|
|maxResults|Maximum results in the list|Defaults to 100. The token "all" specifies the full remaining list.|

### deadlines Request Body

Do not supply a request body.

### deadlines Response
A [deadline_list](Deadline.md#common_objects) object resource.

### deadlines Code Samples
Request Sample

```
curl -X GET -H 'Authorization: Bearer e0438d90599b1c4762d12fd03db6311c9ca46729' \
    http://localhost/api/department/7/deadlines?pretty=true
```
Response Sample

```
{
    "type": "deadline_list",
    "data": {
        {
            "type": "deadline_entry",
            "id": "5",
            "departmentID": "7",
            "deadline": "2020-11-11",
            "get": "http:\/\/localhost:8080\/api\/deadline\/5"
        },
        {
            "type": "deadline_entry",
            "id": "1",
            "deadline": "2019-12-27",
            "departmentID": "7",
            "get": "http:\/\/localhost:8080\/api\/deadline\/1"
        }
    ]
}
```


---
##### [Return to Top](README.md)&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;[Return to Resource List](README.md#resources)
