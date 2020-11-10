##### [Return to Top](README.md)&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;[Return to Resource List](README.md#resources)
---
# Cycle Resource
The following methods are available to cycle resources:

|Resource Method|HTTP Request|Description|module|RBAC|
|---|---|---|---|---|
|[add](Cycle.md#add)|POST /cycle/|Add a new cycle.|core|api.post.cycle|
|[get](Cycle.md#get)|GET /cycle/{id}|Get details about a given cycle.|core|-|
|[modify](Cycle.md#modify)|PUT /cycle/{id}|Modify an existing cycle|core|api.put.cycle|
|[delete](Cycle.md#delete)|DELETE /cycle/{id}|Delete a cycle|core|api.delete.cycle|
|[list](Cycle.md#list)|GET /cycle|Get a list of Cycles.|core|-|


<a name="common_objects"></a>
## Common Objects

#### cycle object resource
A cycle object resource is returned.

```
{
    "type": "cycle",
    "id": {integer},
    "dateFrom": {date},
    "dateTo": {date},
    "links": [HATEOAS links]
}
```

cycle object resources have a number of available properties. These include:

|Object Property|Value|Description| Includable|
|---|---|---|---|
|type|string|Always `cycle`|-|
|id|integer|cycle ID|-|
|dateFrom|date|Date the cycle starts|-|
|dateTo|date|Date the cycle ends.|-|
|links[]|list|List of HATEOAS links for this cycle|-|

The other than `self` no other HATEOAS methods are available.


#### cycle_list object resource

A cycle_list object resource is used when listing cycles.

```
{
	"type":"cycle_list",
	"data":[{cycle}]
}
```

cycle_list object resources have the following available properties:

|Object Property|Value|Description|
|---|---|---|
|type|string|Always `cycle_list`.|
|data[]|list|A list of `cycle` resources.|


<a name="add"></a>
## add
Add a new cycle.

### add Request

```POST /cycle/{department}?Scope={integer}&Text={text}```

### add Parameters
The following parameters are available:

|Parameter|Meaning|Notes|
|---|---|---|
|From|Start date of the cycle.|*required*|
|To|End date of the cycle.|*required*|


### add Request Body
Do not supply a request body.

### add Response

A [cycle](#common_objects) resource.

### add Code Samples
Request Sample

```
curl -X POST -H 'Authorization: Bearer e0438d90599b1c4762d12fd03db6311c9ca46729' http://localhost/api/cycle/?From=1/1/2020&To=1/1/2021
```
Response Sample

```
{
	"DateFrom":"2020-01-02",
	"DateTo":"2020-01-01",
	"id": 46,
	"type":"cycle",
	"links":
		[
			{
			 "method":"self",
			 "href":"http:\/\/localhost:8080\/api\/cycle\46",
			 "request":"GET"
			 }
		]
}
```

<a name="get"></a>
## get
Get information about a given cycle.

### get Request

```GET /cycle/{id}```

### get Parameters
The following parameters are available:

|Parameter|Meaning|Notes|
|---|---|---|
|id|cycle for which data is being retrieved.||

### get Request Body
Do not supply a request body.

### get Response

A [cycle](#common_objects) resource.

### get Code Samples
Request Sample

```
curl -X GET -H 'Authorization: Bearer e0438d90599b1c4762d12fd03db6311c9ca46729' http://localhost/api/cycle/46?pretty=true
```
Response Sample

```
{
	"DateFrom":"2020-01-02",
	"DateTo":"2020-01-01",
	"id": 46,
	"type":"cycle",
	"links":
		[
			{
			 "method":"self",
			 "href":"http:\/\/localhost:8080\/api\/cycle\46",
			 "request":"GET"
			 }
		]
}
```

<a name="modify"></a>
## modify
Modify an existing cycle.

### modify Request

```PUT /cycle/{id}?From={date}&To={date}```

### modify Parameters
The following parameters are available:

|Parameter|Meaning|Notes|
|---|---|---|
|id|The ID of the cycle being modified|*required*|
|From|From date of the cycle.||
|To|To date of the cycle.||

### modify Request Body
Do not supply a request body.

### modify Response

A [cycle](#common_objects) resource.

### modify Code Samples
Request Sample

```
curl -X PUT -H 'Authorization: Bearer e0438d90599b1c4762d12fd03db6311c9ca46729' http://localhost/api/cycle/46/To=01/01/2031
```
Response Sample

```
{
	"DateFrom":"2020-01-02",
	"DateTo":"2030-01-01",
	"id": 46,
	"type":"cycle",
	"links":
		[
			{
			 "method":"self",
			 "href":"http:\/\/localhost:8080\/api\/cycle\46",
			 "request":"GET"
			 }
		]
}
```

<a name="delete"></a>
## delete
Delete an existing cycle.

### delete Request

```DELETE /cycle/{id}```

### delete Parameters
The following parameters are available:

|Parameter|Meaning|Notes|
|---|---|---|
|id|The ID of the cycle being deleted|*required*|

### delete Request Body
Do not supply a request body.

### delete Response

Does not return a response.

### delete Code Samples
Request Sample

```
curl -X DELETE -H 'Authorization: Bearer e0438d90599b1c4762d12fd03db6311c9ca46729' http://localhost/api/cycle/46
```
Response Sample

```
[]
```

<a name="list"></a>
## list
List cycles.

### list Request

```GET /cycle```

### list Parameters
The following parameters are available:

|Parameter|Meaning|Notes|
|---|---|---|
|begin|Beginning date for cycles that are being retrieved.|optional|
|end|Ending date for cycles that are being retrieved.|optional|


### list Request Body
Do not supply a request body.

### list Response

A [cycle_list](#common_objects) resource.

### list Code Samples
Request Sample

```
curl -X GET -H 'Authorization: Bearer e0438d90599b1c4762d12fd03db6311c9ca46729' http://localhost/api/cycle?from=01/01/2029&to=01/01/2031&pretty=true
```
Response Sample

```
{
	{
		"type":"cycle_list",
		"data"[
			{
				"DateFrom":"2030-07-31",
				"DateTo":"2031-07-30",
				"type":"cycle",
				"id":"23"
			}
		]
	}
}
```


---
##### [Return to Top](README.md)&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;[Return to Resource List](README.md#resources)
