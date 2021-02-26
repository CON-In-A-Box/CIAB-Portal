##### [Return to Top](README.md)&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;[Return to Resource List](README.md#resources)
---
# Event Resource
The following methods are available to Event resources:

|Resource Method|HTTP Request|Description|module|RBAC|
|---|---|---|---|---|
|[get](Event.md#get)|GET /event/{id}|Get details about a given event.|core|-|
|[list](Event.md#list)|GET /event|Get a list of events.|core|-|
|[create](Event.md#create)|POST /event|Create a new event.|core|api.post.event|
|[delete](Event.md#delete)|DELETE /event/{id}|Delete an event.|core|api.delete.event|



<a name="common_objects"></a>
## Common Objects

#### Event object resource
A Event object resource is returned.

```
{
    "type": "event",
    "id": {integer},
    "cycle": {integer},
    "dateFrom": {date},
    "dateTo": {date},
    "Name": {string}
    "links": [HATEOAS links]

}
```

Event object resources have a number of available properties. These include:

|Object Property|Value|Description| Includable|
|---|---|---|---|
|type|string|Always `event`|-|
|id|integer|Event ID|-|
|cycle|integer|Cycle ID|yes `cycle`|
|dateFrom|date|Date the Event starts|-|
|dateTo|date|Date the Event ends.|-|
|name|string|Name of the Event.|-|
|links[]|list|List of HATEOAS links for this cycle|-|

The other than `self` no other HATEOAS methods are available.

#### Event_list object resource

A event_list object resource is used when listing events.

```
{
	"type":"event_list",
	"data":[{event}]
}
```

event_list object resources have the following available properties:

|Object Property|Value|Description|
|---|---|---|
|type|string|Always `event_list`.|
|data[]|list|A list of `event` resources.|


<a name="get"></a>
## get
Get information about a given event.

### get Request

```GET /Event/{id}```

### get Parameters
The following parameters are available:

|Parameter|Meaning|Notes|
|---|---|---|
|id|Event for which data is being retrieved.||

### get Request Body
Do not supply a request body.

### get Response

A [Event](#common_objects) resource.

### get Code Samples
Request Sample

```
curl -X GET -H 'Authorization: Bearer e0438d90599b1c4762d12fd03db6311c9ca46729' http://localhost/api/event/46?pretty=true
```
Response Sample

```
{
	"type":"event",
	"id": 46,
	"cycle": 4,
	"DateFrom":"2020-01-02",
	"DateTo":"2020-01-01",
	"name": "Awesome Con"
	"links":
	[
		{
		 "method":"self",
		 "href":"http:\/\/localhost:8080\/api\/event\46",
		 "request":"GET"
		 }
	]
}
```

<a name="list"></a>
## list
List Events.

### list Request

```GET /event```

### list Parameters
The following parameters are available:

|Parameter|Meaning|Notes|
|---|---|---|
|begin|Beginning date for Events that are being retrieved.|optional|
|end|Ending date for Events that are being retrieved.|optional|


### list Request Body
Do not supply a request body.

### list Response

A [Event_list](#common_objects) resource.

### list Code Samples
Request Sample

```
curl -X GET -H 'Authorization: Bearer e0438d90599b1c4762d12fd03db6311c9ca46729' http://localhost/api/event?from=01/01/2019&to=01/01/2031&pretty=true
```
Response Sample

```
{
	{
		"type":"event_list",
		"data"[
			{
				"type":"event",
				"id": 46,
				"cycle": 4,
				"DateFrom":"2020-01-02",
				"DateTo":"2020-01-01",
				"name": "Awesome Con"
			}
		]
	}
}
```

<a name="create"></a>
## create

Create a new event in the system.

### create Request

```POST /event```

### create Parameters

There are no parameters available.

### create Request Body

|Parameter|Meaning|Notes|
|---|---|---|
|From|Date of event start.|<b>Required</b>|
|To|Date of event end.|<b>Required</b>|
|Name|Name of the event.|<b>Required</b>|

### create Response
A [Event](#common_objects) resource.

###create Code Samples
Request Sample

```
curl -X POST -H 'Authorization: Bearer e0438d90599b1c4762d12fd03db6311c9ca46729' http://localhost/api/event?pretty=true \
-d { 'From' : '2020-01-01', \
	 'To' : '2020-01-02', \
	 'Name' : 'Awesome Con'}
```
Response Sample

```
{
   "type":"event",
	"id": 47,
	"cycle": 4,
	"DateFrom":"2020-01-01",
	"DateTo":"2020-01-02",
	"name": "Awesome Con"
	"links":
	[
		{
		 "method":"self",
		 "href":"http:\/\/localhost:8080\/api\/event\47",
		 "request":"GET"
		 }
	]
}
```

<a name="delete"></a>
## delete
Delete an existing event.

### delete Request

```DELETE /event/{id}```

### delete Parameters
The following parameters are available:

|Parameter|Meaning|Notes|
|---|---|---|
|id|The ID of the event being modified|*required*|

### delete Request Body
Do not supply a request body.

### delete Response

Does not return a response.

### delete Code Samples
Request Sample

```
curl -X DELETE -H 'Authorization: Bearer e0438d90599b1c4762d12fd03db6311c9ca46729' http://localhost/api/event/1
```
Response Sample

```
[]
```

---
##### [Return to Top](README.md)&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;[Return to Resource List](README.md#resources)
