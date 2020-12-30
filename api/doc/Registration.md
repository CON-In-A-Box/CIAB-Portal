##### [Return to Top](README.md)&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;[Return to Resource List](README.md#resources)
---
# Registration Module

The Registration module includes quite a few new resources and methods.

<a name="resources"></a>
## Resources

* [Configuration](Registration/Configuration.md)
* [Ticket](Registration/Ticket.md)


# Registration Resource

The following base methods are available to registration resources:


|Resource Method|HTTP Request|Description|Module|RBAC|
|---|---|---|---|---|
|[admin](Registration.md#admin)|GET /registration/admin|Returns a response based on if the authenticated user has admin privileges for Registations|registration|--|
|[open](Registration.md#open)|GET /registration/open[/{event}]|Returns if registration is open for the current, or given event.|registration|--|

<a name="admin"></a>
## admin

Returns a response based on if the authenticated user has admin privileges for Registation.

### admin Request

```GET /registration/admin```

### admin Parameters

Takes no parameters.

### status Request Body
Do not supply a request body.

### status Response
A status object is returned.

```
{
     "admin": {boolean},
}
```
Status object resources is as follows:

|Object Property|Value|Description|Includable|
|---|---|---|---|
|admin|boolean|`true` if current user is a Registration admin|-|



### admin Code Samples
Request Sample

```
curl -X GET -H 'Authorization: Bearer e0438d90599b1c4762d12fd03db6311c9ca46729' http://localhost/api/registration/admin?pretty=true
```
Response Sample

```
{
    "admin": true
}
```

<a name="open"></a>
## open

Returns if registration is open for the current, or given event.

*Note*: Authentication is not required to use this entry point.

### open Request

```GET /registration/open[/{event}]```

### open Parameters

The following find parameters are available:

|Parameter|Meaning|Notes|
|---|---|---|
|event|`eventId` being checked.||

### open Request Body
Do not supply a request body.

### open Response
A open object is returned.

```
{
    "type": "registration",
    "event: {integer},
    "open": {boolean}
}
```

Open object resources is as follows:

|Object Property|Value|Description|Includable|
|---|---|---|---|
|type|string|Always `registration`|-|
|event|integer|Event ID for the event.|**yes** `event`|
|open|boolean|`true` if on-site registration is open.|-|



### open Code Samples
Request Sample

```
curl -X GET -H 'Authorization: Bearer e0438d90599b1c4762d12fd03db6311c9ca46729' http://localhost/api/registration/open?pretty=true
```
Response Sample

```
{
    "type": "registration",
    "event": "22",
    "open": true
}
```


---
##### [Return to Top](README.md)&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;[Return to Resource List](README.md#resources)
