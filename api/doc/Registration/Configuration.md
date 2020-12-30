##### [Return to Top](../README.md)&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;[Return to Resource List](../README.md#resources)
---
# Registration/Configuration Module



# Registration/Configuration Resource

The following base methods are available to registration resources:


|Resource Method|HTTP Request|Description|Module|RBAC|
|---|---|---|---|---|
|[get](Configuration.md#get)|GET /registration/configuration[/{key}]|Returns the registration configuration option(s).|registration|--|
|[set](Configuration.md#set)|PUT /registration/configuration|Change a registration configuration option.|registration|api.set.registration.configuration|

<a name="get"></a>
## get

Returns a list of all, or a given registration configuration option.

### get Request

```GET /registration/configuration[/{key}]```

### get Parameters

|Parameter|Meaning|Notes|
|---|---|---|
|key|Configuration Key being requested.||

### get Request Body
Do not supply a request body.

### get Response
A configuration object or list of configuration objects are returned.


```
{
    "type": "configuration",
    "description": {string},
    "field": {string},
    "fieldType": {type},
    "options": [{option}],
    "value": {string}
}
```
```
{
    "type": "configuration_list",
    "data": [{configuration}]
}
```

### get Code Samples
Request Sample

```
curl -X GET -H 'Authorization: Bearer e0438d90599b1c4762d12fd03db6311c9ca46729' http://localhost/api/registration/configuration?pretty=true
```
Response Sample

```
{
    "type": "configuration_list",
    "data": [
        {
            "description": "Notice text presented at the top of the Checkin/Badge Pickup screen.",
            "field": "badgeNotice",
            "fieldType": "text",
            "options": null,
            "type": "configuration_entry",
            "value": "This is the badge notice."
        },
        {
            "description": "Online Check-In Registration is open. (Only valid during event days)",
            "field": "ForceOpen",
            "fieldType": "boolean",
            "options": null,
            "type": "configuration_entry",
            "value": "1"
        },
        {
            "description": "Instructions for use of the boarding pass.",
            "field": "passInstructions",
            "fieldType": "text",
            "options": null,
            "type": "configuration_entry",
            "value": "Please use this boarding pass to pick up your badge at registration."
        },
        {
            "description": "Hour that online check-in closes (24-hour clock)",
            "field": "RegistrationClose",
            "fieldType": "integer",
            "options": null,
            "type": "configuration_entry",
            "value": "0"
        },
        {
            "description": "Hour that online check-in opens (24-hour clock)",
            "field": "RegistrationOpen",
            "fieldType": "integer",
            "options": null,
            "type": "configuration_entry",
            "value": "0"
        }
    ]
}
```

<a name="set"></a>
## set

Change a registration configuration option.

### set Request

```PUT /registration/configuration```

### set Parameters

There are no request parameters.

### set Request Body

|Argument|Meaning|Notes|
|---|---|---|
|Field|Field being set.||
|Value|Value being set.||

### set Response
An updated configuration object is returned.

```
{
    "type": "configuration",
    "description": {string},
    "field": {string},
    "fieldType": {type},
    "options": [{option}],
    "value": {string}
}
```

### set Code Samples
Request Sample

```
curl -X PUT -H 'Authorization: Bearer e0438d90599b1c4762d12fd03db6311c9ca46729' http://localhost/api/registration/configuration?pretty=true\
-d {'Field' : 'RegistrationOpen', 'Value' : '11'}
```
Response Sample

```
{
	"type": "configuration",
	"description": "Hour that online check-in opens (24-hour clock)",
	"field": "RegistrationOpen",
	"fieldType": "integer",
	"options": null,
	"value": "11"
}
```


---
##### [Return to Top](README.md)&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;[Return to Resource List](README.md#resources)
