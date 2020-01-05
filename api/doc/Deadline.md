##### [Return to Top](README.md)&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;[Return to Resource List](README.md#resources)
---
# Deadline Resource
The following methods are available to deadline resources:

|Resource Method|HTTP Request|Description|module|RBAC|
|---|---|---|---|---|
|[add](Deadline.md#add)|PUT /deadline/{department}|Add a new deadline.|core|api.put.deadline.{department}|
|[get](Deadline.md#get)|GET /deadline/{id}|Get details about a given deadline.|core|api.get.deadline.{department}|
|[modify](Deadline.md#modify)|POST /deadline/{id}|Modify an existing deadline|core|api.post.deadline.{department}|
|[delete](Deadline.md#delete)|DELETE /deadline/{id}|Delete a deadline|core|api.delete.deadline.{department}|

<a name="common_objects"></a>
## Common Objects

#### deadline object resource
A deadline object resource is returned.

```
{
    "type": "deadline",
    "id": {integer},
    "departmentId": {integer},
    "deadline": {date},
    "note": {string},
    "links": [HATEOAS links]
}
```

Deadline object resources have a number of available properties. These include:

|Object Property|Value|Description|
|---|---|---|
|type|string|Always `deadline`|
|id|integer|Deadline ID|
|departmentId|integer|Department ID for the deadline|
|deadline|date|Due date for the deadline|
|note|string|Note about the deadline|
|links[]|list|List of HATEOAS links for this deadline|

The following HATEOAS methods are available as well:

|HATEOAS Method|Request|Description|
|---|---|---|
|modify|POST|Method to modify the deadline.|
|delete|DELETE|Method to delete the deadline.|
|department|GET|Method to get the department for the deadline.|


#### deadline_list object resource

A deadline_list object resource is used when listing deadlines.

```
{
	"type":"deadline_list",
	"data":[{deadline}]
}
```

deadline_list object resources have the following available properties:

|Object Property|Value|Description|
|---|---|---|
|type|string|Always `deadline_list`.|
|data[]|list|A list of `deadline` resources.|


<a name="add"></a>
## add
Add a new deadline.

### add Request

```PUT /deadline/{department}?Deadline={date}&Note={note}```

### add Parameters
The following parameters are available:

|Parameter|Meaning|Notes|
|---|---|---|
|department|Department for which data is being retrieved.|`integer` id or `string` name|
|date|Date of the deadline.|*required*|
|note|Note specifying the deadline.|*required*|


### add Request Body
Do not supply a request body.

### add Response

Does not return a response.

### add Code Samples
Request Sample

```
curl -X PUT -H 'Authorization: Bearer e0438d90599b1c4762d12fd03db6311c9ca46729' http://localhost/api/deadline/Art%20Show?Deadline=2019-12-27&Note=Testing
```
Response Sample

```
[]
```

<a name="get"></a>
## get
Get information about a given deadline.

### get Request

```GET /deadline/{id}```

### get Parameters
The following parameters are available:

|Parameter|Meaning|Notes|
|---|---|---|
|id|Deadline for which data is being retrieved.||

### get Request Body
Do not supply a request body.

### get Response

A [deadline](#common_objects) resource.

### get Code Samples
Request Sample

```
curl -X GET -H 'Authorization: Bearer e0438d90599b1c4762d12fd03db6311c9ca46729' http://localhost/api/deadline/1?pretty=true
```
Response Sample

```
{
    "type": "deadline",
    "id": "1",
    "departmentId": "114",
    "deadline": "2019-12-27",
    "note": "Testing"
    "links": [
        {
            "method": "self",
            "href": "http:\/\/localhost\/api\/deadline\/1",
            "request": "GET"
        },
        {
            "method": "modify",
            "href": "http:\/\/localhost\/api\/deadline\/1",
            "request": "POST"
        },
        {
            "method": "delete",
            "href": "http:\/\/localhost\/api\/deadline\/1",
            "request": "DELETE"
        },
        {
            "method": "department",
            "href": "http:\/\/localhost\/api\/department\/114",
            "request": "GET"
        }
    ]
}
```

<a name="modify"></a>
## modify
Modify an existing deadline.

### modify Request

```POST /deadline/{id}?Departemnt={dept}&Deadline={date}&Note={note}```

### modify Parameters
The following parameters are available:

|Parameter|Meaning|Notes|
|---|---|---|
|id|The ID of the deadline being modified|*required*|
|department|Department for which data is being retrieved.|`integer` id or `string` name|
|date|Date of the deadline.||
|note|Note specifying the deadline.||


### modify Request Body
Do not supply a request body.

### modify Response

Does not return a response.

### modify Code Samples
Request Sample

```
curl -X POST -H 'Authorization: Bearer e0438d90599b1c4762d12fd03db6311c9ca46729' http://localhost/api/deadline/1/Department=Art%20Show?Deadline=2019-12-27&Note=Testing
```
Response Sample

```
[]
```

<a name="delete"></a>
## delete
Delete an existing deadline.

### delete Request

```DELETE /deadline/{id}```

### delete Parameters
The following parameters are available:

|Parameter|Meaning|Notes|
|---|---|---|
|id|The ID of the deadline being modified|*required*|

### delete Request Body
Do not supply a request body.

### delete Response

Does not return a response.

### delete Code Samples
Request Sample

```
curl -X DELETE -H 'Authorization: Bearer e0438d90599b1c4762d12fd03db6311c9ca46729' http://localhost/api/deadline/1
```
Response Sample

```
[]
```

---
##### [Return to Top](README.md)&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;[Return to Resource List](README.md#resources)
