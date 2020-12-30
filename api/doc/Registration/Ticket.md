##### [Return to Top](../README.md)&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;[Return to Resource List](../README.md#resources)
---
# Registration/Ticket Module



# Registration/Ticket Resource

The following base methods are available to registration resources:


|Resource Method|HTTP Request|Description|Module|RBAC|
|---|---|---|---|---|
|[list](Ticket.md#list)|GET /registration/ticket/list/[/{member}[/{event}]]|List all the tickets for a member and an event.|registration|Owner or api.registration.ticket.list|
|[type](Ticket.md#type)|GET /registration/ticket/type/[/{id}[/{event}]]|Get Ticket types for an event.|registration|--|
|[queue](Ticket.md#queue)|GET /registration/ticket/printqueue[/{event}]|Get Ticket print queue for an event.|registration|--|
|[claim](Ticket.md#claim)|PUT /registration/ticket/printqueue/claim/{id}|Claim Ticket print queue item to print.|registration|api.registration.ticket.print|
|[get](Ticket.md#get)|GET /registration/ticket/{id}|Returns the given ticket.|registration|Owner or api.registration.ticket.get|
|[checkin](Ticket.md#checking)|PUT /registration/ticket/{id}/checkin|Mark a ticket as checked in.|registration|Owner or api.registration.ticket.checkin|
|[lost](Ticket.md#lost)|PUT /registration/ticket/{id}/lost|Mark a ticket as lost.|registration|Owner or api.registration.ticket.lost|
|[pickup](Ticket.md#pickup)|PUT /registration/ticket/{id}/pickup|Mark a ticket as picked up.|registration|Owner or api.registration.ticket.pickup|
|[email](Ticket.md#email)|PUT /registration/ticket/{id}/email|Email ticket info to the member.|registration|Owner or api.registration.ticket.email|
|[print](Ticket.md#print)|PUT /registration/ticket/{id}/print|Add the given ticket to the print queue an additional time.|registration|api.registration.ticket.print|
|[put](Ticket.md#put)|PUT /registration/ticket/{id}|Update Ticket information.|registration|api.registration.ticket.put|
|[void](Ticket.md#void)|PUT /registration/ticket/{id}/void|Void a Ticket.|registration|api.registration.ticket.void|
|[reinstate](Ticket.md#reinstate)|PUT /registration/ticket/{id}/reinstate|Reinstate a voided Ticket.|registration|api.registration.ticket.unvoid|
|[post](Ticket.md#post)|POST /registration/ticket|Add a new Ticket.|registration|api.registration.ticket.post|
|[delete](Ticket.md#delete)|DELETE /registration/ticket/{id}|Delete a Ticket.|registration|api.registration.ticket.delete|

## Common Types

<a name="ticket"></a>

#ticket

```
{
	"type": "ticket",
	"id": {integer},
	"member": {integer},
	"badgeDependentOn": {integer},
	"badgeName": {string},
	"badgesPickedUp": {integer},
	"boardingPassGenerated": {date},
	"emergencyContact": {string},
	"event": {integer},
	"lastPrintedDate": {date},
	"note": {string},
	"printRequestIp": {string},
	"printRequested": {date},
	"registeredBy": {integer},
	"registrationDate": {date},
	"ticketType": {integer},
	"voidBy": {integer},
	"voidDate": {date},
	"voidReason": {string}
}
```

Ticket object resources have a number of available properties. These include:

|Object Property|Value|Description|Includable|
|---|---|---|---|
|type|string|Always `ticket`|-|
|id|integer|ID of the ticket|-|
|member|integer|ID of member the ticket is for|**yes** `member`|
|badgeDependentOn|integer|ID of the member the badge is dependent on (for minors)|**yes** `member`|
|badgeName|string|Name on the badge|-|
|badgesPickedUp|integer|The number of times this ticket has been collected|-|
|boardingPassGenerated|date|When the boarding pass for this ticket was generated|-|
|emergencyContact|string|Emergency contact string|-|
|event|integer|The ID for the event this ticket is for|**yes** `event`|
|lastPrintedDate|date|When is the most recent time this badge was printed|-|
|note|string|Optional note|-|
|printRequestIp|string|IP address of the machine performing the print request|-|
|printRequested|date|At what time was a print request generated|-|
|registeredBy|integer|The id of the member who purchased the ticket|**yes** `member`|
|registrationDate|date|When the ticket was purchased|-|
|ticketType|integer|The type of the badge|**yes** `ticket_type`|
|voidBy|integer|The member id of who voided the ticket|**yes** `member`|
|voidDate|date|When was this ticket voided|-|
|voidReason|string|Why was this ticket voided|-|

<a name="ticket_type"></a>
#ticket_type


```
{
    "type": "ticket_type",
    "id": {integer},
    "AvailableFrom": {date},
    "AvailableTo": {date},
    "BackgroundImage": {string},
    "Cost": {float},
    "Name": {string},
    "event": {integer}
}
```
Ticket_type object resources have a number of available properties. These include:

|Object Property|Value|Description|Includable|
|---|---|---|---|
|type|string|Always `ticket_type`|-|
|id|integer|ID of the ticket type|-|
|AvailableFrom|date|When the ticket can first be purchased|-|
|AvailableTo|date|Until when can the ticket be purchased|-|
|BackgroundImage|string|Data for the badge printing|-|
|Cost|float|Cost of the ticket|-|
|Name|string|Name of the ticket type|-|
|event|integer|ID of the event for this ticket|**yes** `event`|


<a name="ticket_list"></a>

#ticket_list

```
{
    "data": [{ticket}]
    "type": "ticket_list"
}
```
List of ticket object resources have a number of available properties. These include:

|Object Property|Value|Description|Includable|
|---|---|---|---|
|type|string|Always `ticket_list`|-|
|data[]|list|A list of `ticket` resources|-|


<a name="list"></a>
## list

Returns a list of all the tickets for a given member and event.

Member defaults to currently authenticated member and event defaults to current event.

### list Request

```GET /registration/ticket/list/[/{member}[/{event}]][?showVoid={boolean}]```

### list Parameters

|Parameter|Meaning|Notes|
|---|---|---|
|member|`member` for whom tickets are being listed.|Default **current user**|
|event|`event` for the event tickets are being listed.|Default **current event**|
|showVoid| `boolean` true if voided tickets should be listed.|Default is `false`|

### list Request Body
Do not supply a request body.

### list Response
A [ticket_list](#ticket_list) object

### list Code Samples
Request Sample

```
curl -X GET -H 'Authorization: Bearer e0438d90599b1c4762d12fd03db6311c9ca46729' http://localhost/api/registration/ticket/list?pretty=true
```
Response Sample

```
{
    "data": [
        {
            "member": "1000",
            "badgeDependentOn": null,
            "badgeName": "ValtojNefKic",
            "badgesPickedUp": "1",
            "boardingPassGenerated": "2020-12-27 02:41:07",
            "emergencyContact": null,
            "event": "1",
            "id": "1",
            "lastPrintedDate": "2020-12-29 19:38:02",
            "note": null,
            "printRequestIp": null,
            "printRequested": null,
            "registeredBy": "1000",
            "registrationDate": "2020-10-11 00:15:58",
            "type": "ticket",
            "ticketType": "4",
            "voidBy": null,
            "voidDate": null,
            "voidReason": null
        },
        {
            "member": "1000",
            "badgeDependentOn": null,
            "badgeName": "kijCilMulmoh",
            "badgesPickedUp": "0",
            "boardingPassGenerated": "2020-12-27 03:02:32",
            "emergencyContact": null,
            "event": "1",
            "id": "5",
            "lastPrintedDate": null,
            "note": null,
            "printRequestIp": null,
            "printRequested": null,
            "registeredBy": "1000",
            "registrationDate": "2020-10-11 00:15:58",
            "type": "ticket",
            "ticketType": "2",
            "voidBy": null,
            "voidDate": null,
            "voidReason": null
        },
        {
            "member": "1000",
            "badgeDependentOn": null,
            "badgeName": "ValtojNefKic Again",
            "badgesPickedUp": "0",
            "boardingPassGenerated": "2020-12-29 20:06:52",
            "emergencyContact": null,
            "event": "1",
            "id": "102",
            "lastPrintedDate": "2020-12-29 20:06:54",
            "note": null,
            "printRequestIp": null,
            "printRequested": null,
            "registeredBy": "1000",
            "registrationDate": "2020-10-11 00:15:58",
            "type": "ticket",
            "ticketType": "4",
            "voidBy": null,
            "voidDate": null,
            "voidReason": null
        },
        {
            "member": "1000",
            "badgeDependentOn": null,
            "badgeName": "ValtojNefKic three",
            "badgesPickedUp": "0",
            "boardingPassGenerated": "2020-12-29 19:40:47",
            "emergencyContact": null,
            "event": "1",
            "id": "103",
            "lastPrintedDate": "2020-12-29 19:40:50",
            "note": null,
            "printRequestIp": null,
            "printRequested": null,
            "registeredBy": "1000",
            "registrationDate": "2020-10-11 00:15:58",
            "type": "ticket",
            "ticketType": "4",
            "voidBy": null,
            "voidDate": null,
            "voidReason": null
        }
    ],
    "type": "ticket_list"
}
```

<a name="type"></a>
## type

Returns a list of all the ticket type for a given event.

### type Request

```GET /registration/ticket/type/[/{id}[/{event}]]```

### type Parameters

|Parameter|Meaning|Notes|
|---|---|---|
|id|`ticket` Get a single ticket type.|Default to list all ticket types|
|event|`event` for the event tickets are being listed.|Default **current event**|

### type Request Body
Do not supply a request body.

### type Response

```
{
    "data": [{ticket}]
    "type": "ticket_type_list"
}
```
List of ticket object resources have a number of available properties. These include:

|Object Property|Value|Description|Includable|
|---|---|---|---|
|type|string|Always `ticket_type_list`|-|
|data[]|list|A list of `ticket_type` resources|-|

OR a single [ticket_type](#ticket_type) object


### type Code Samples
Request Sample

```
curl -X GET -H 'Authorization: Bearer e0438d90599b1c4762d12fd03db6311c9ca46729' http://localhost/api/registration/ticket/type?pretty=true
```
Response Sample

```
{
    "data": [
        {
            "AvailableFrom": "2020-10-10",
            "AvailableTo": "2021-04-08",
            "BackgroundImage": "",
            "Cost": "10.00",
            "Name": "Badge Type 0",
            "event": "1",
            "id": "1",
            "type": "ticket_type"
        },
        {
            "AvailableFrom": "2020-10-10",
            "AvailableTo": "2021-04-08",
            "BackgroundImage": "",
            "Cost": "20.00",
            "Name": "Badge Type 1",
            "event": "1",
            "id": "2",
            "type": "ticket_type"
        },
        {
            "AvailableFrom": "2020-10-10",
            "AvailableTo": "2021-04-08",
            "BackgroundImage": "",
            "Cost": "30.00",
            "Name": "Badge Type 2",
            "event": "1",
            "id": "3",
            "type": "ticket_type"
        },
        {
            "AvailableFrom": "2020-10-10",
            "AvailableTo": "2021-04-08",
            "BackgroundImage": "",
            "Cost": "40.00",
            "Name": "Badge Type 3",
            "event": "1",
            "id": "4",
            "type": "ticket_type"
        },
        {
            "AvailableFrom": "2020-10-10",
            "AvailableTo": "2021-04-08",
            "BackgroundImage": "",
            "Cost": "50.00",
            "Name": "Badge Type 4",
            "event": "1",
            "id": "5",
            "type": "ticket_type"
        }
    ],
    "type": "ticket_type_list"
}
```

<a name="queue"></a>
## Queue

Returns a list of all the ticket type for a given event.

### Queue Request

```GET /registration/ticket/printqueue/[/{event}]```

### Queue Parameters

|Parameter|Meaning|Notes|
|---|---|---|
|event|`event` for the event print queue are being listed.|Default **current event**|

### Queue Request Body
Do not supply a request body.

### Queue Response

```
{
    "type": "print_job",
    "id": {integer},
    "claim": {
        "href": {URL},
        "method": "claim",
        "request": "PUT"
    }
}
```

|Object Property|Value|Description|Includable|
|---|---|---|---|
|type|string|Always `print_job`|-|
|id|integer|Ticket ID to be printed|-|
|claim|URL|URL to the method to claim the print job|-|


```
{
    "type": "print_queue",
    "data": [{print_job}]
}
```
List of ticket object resources have a number of available properties. These include:

|Object Property|Value|Description|Includable|
|---|---|---|---|
|type|string|Always `print_queue`|-|
|data[]|list|A list of `print_job` resources|-|


### Queue Code Samples
Request Sample

```
curl -X GET -H 'Authorization: Bearer e0438d90599b1c4762d12fd03db6311c9ca46729' http://localhost/api/registration/ticket/printqueue?pretty=true
```
Response Sample

```
{
    "data": [
        {
            "claim": {
                "href": "http://localhost:8080/api/registration/ticket/printqueue/claim/1",
                "method": "claim",
                "request": "PUT"
            },
            "id": "1",
            "type": "print_job"
        }
    ],
    "type": "print_queue"
}
```

<a name="claim"></a>
## Claim

Returns a ticket object for a print queue item and removes it from the queue.

### Claim Request

```PUT /registration/ticket/printqueue/claim/{id}```

### Claim Parameters

|Parameter|Meaning|Notes|
|---|---|---|
|id|`ticket` The ticket in in the print queue.||

### Claim Request Body
Do not supply a request body.

### Claim Response

A [ticket](#ticket) object

### Claim Code Samples
Request Sample

```
curl -X GET -H 'Authorization: Bearer e0438d90599b1c4762d12fd03db6311c9ca46729' http://localhost/api/registration/ticket/printqueue/claim/1?pretty=true
```
Response Sample

```
{
    "member": "1000",
    "badgeDependentOn": null,
    "badgeName": "whee",
    "badgesPickedUp": "0",
    "boardingPassGenerated": "2020-12-29 19:40:47",
    "emergencyContact": null,
    "event": "1",
    "id": "1",
    "lastPrintedDate": null,
    "note": null,
    "printRequestIp": "192.168.100.2",
    "printRequested": "2020-12-29 19:40:50",
    "registeredBy": "1000",
    "registrationDate": "2020-10-11 00:15:58",
    "type": "ticket",
    "ticketType": "4",
    "voidBy": null,
    "voidDate": null,
    "voidReason": null
}
```

<a name="get"></a>
##Get

Returns the given ticket.

### Get Request

```GET /registration/ticket/{id}```

### Get Parameters

|Parameter|Meaning|Notes|
|---|---|---|
|id|`ticket` Ticket ID to get.||

### Get Request Body
Do not supply a request body.

### Get Response

A [ticket](#ticket) object

### Get Code Samples
Request Sample

```
curl -X GET -H 'Authorization: Bearer e0438d90599b1c4762d12fd03db6311c9ca46729' http://localhost/api/registration/ticket/1?pretty=true
```
Response Sample

```
{
    "member": "1000",
    "badgeDependentOn": null,
    "badgeName": "whee",
    "badgesPickedUp": "0",
    "boardingPassGenerated": "2020-12-29 19:40:47",
    "emergencyContact": null,
    "event": "1",
    "id": "1",
    "lastPrintedDate": "2020-12-29 19:40:50",
    "note": null,
    "printRequestIp": null,
    "printRequested": null,
    "registeredBy": "1000",
    "registrationDate": "2020-10-11 00:15:58",
    "type": "ticket",
    "ticketType": "4",
    "voidBy": null,
    "voidDate": null,
    "voidReason": null
}
```

<a name="checkin"></a>
## checkin

Mark a ticket as checked in.

### checkin Request

```PUT /registration/ticket/{id}/checkin```

### checkin Parameters

|Parameter|Meaning|Notes|
|---|---|---|
|id|`ticket` The ticket being checked in.||

### checkin Request Body
Do not supply a request body.

### checkin Response

Does not supply a response.

### checkin Code Samples
Request Sample

```
curl -X GET -H 'Authorization: Bearer e0438d90599b1c4762d12fd03db6311c9ca46729' http://localhost/api/registration/ticket/1/checkin?pretty=true
```

Response Sample

```
[]
```

<a name="lost"></a>
##

Mark a ticket as lost.

### lost Request

```PUT /registration/ticket/{id}/lost```

### lost Parameters

|Parameter|Meaning|Notes|
|---|---|---|
|id|`ticket` The ticket being marked as lost.||

### lost Request Body
Do not supply a request body.

### lost Response

Does not supply a response.

### lost Code Samples
Request Sample

```
curl -X GET -H 'Authorization: Bearer e0438d90599b1c4762d12fd03db6311c9ca46729' http://localhost/api/registration/ticket/1/lost?pretty=true
```
Response Sample

```
[]
```

<a name="pickup"></a>
## pickup

Mark a ticket as picked up.

### pickup Request

```PUT /registration/ticket/{id}/pickup```

### pickup Parameters

|Parameter|Meaning|Notes|
|---|---|---|
|id|`ticket` The ticket being marked as picked up.||

### pickup Request Body
Do not supply a request body.

### pickup Response

Does not supply a response.

### pickup Code Samples
Request Sample

```
curl -X GET -H 'Authorization: Bearer e0438d90599b1c4762d12fd03db6311c9ca46729' http://localhost/api/registration/ticket/1/pickup?pretty=true
```
Response Sample

```
[]
```

<a name="email"></a>
##email

Email ticket info to the member.

### email Request

```PUT /registration/ticket/{id}/email```

### email Parameters

|Parameter|Meaning|Notes|
|---|---|---|
|id|`ticket` The ticket to be emailed.||

### email Request Body
Do not supply a request body.

### email Response

Does not supply a response.

### email Code Samples
Request Sample

```
curl -X GET -H 'Authorization: Bearer e0438d90599b1c4762d12fd03db6311c9ca46729' http://localhost/api/registration/ticket/1/email?pretty=true
```
Response Sample

```
[]
```

<a name="print"></a>
##print

Add the given ticket to the print queue an additional time.

### print Request

```PUT /registration/ticket/{id}/print```

### print Parameters

|Parameter|Meaning|Notes|
|---|---|---|
|id|`ticket` The ticket being printed.||

### print Request Body
Do not supply a request body.

### print Response

Does not supply a response.

### print Code Samples
Request Sample

```
curl -X GET -H 'Authorization: Bearer e0438d90599b1c4762d12fd03db6311c9ca46729' http://localhost/api/registration/ticket/1/print?pretty=true
```
Response Sample

```
[]
```

<a name="put"></a>
##put

Update Ticket information.

### put Request

```PUT /registration/ticket/{id} ```

### put Parameters

|Parameter|Meaning|Notes|
|---|---|---|
|id|`ticket` The ticket being updated.||

### put Request Body

|Parameter|Meaning|Notes|
|---|---|---|
|badgeName|Name on ticket badge.|Optional|
|contact|Emergency Contact Info.|Optional|
|note|Note on ticket.|Optional|


### put Response

A [ticket](#ticket) object

### put Code Samples
Request Sample

```
curl -X PUT -H 'Authorization: Bearer e0438d90599b1c4762d12fd03db6311c9ca46729' http://localhost/api/registration/ticket/1?pretty=true&badgeName="foo"
```
Response Sample

```
{
    "member": "1000",
    "badgeDependentOn": null,
    "badgeName": "foo",
    "badgesPickedUp": "0",
    "boardingPassGenerated": "2020-12-29 19:40:47",
    "emergencyContact": null,
    "event": "1",
    "id": "1",
    "lastPrintedDate": "2020-12-29 19:40:50",
    "note": null,
    "printRequestIp": null,
    "printRequested": null,
    "registeredBy": "1000",
    "registrationDate": "2020-10-11 00:15:58",
    "type": "ticket",
    "ticketType": "4",
    "voidBy": null,
    "voidDate": null,
    "voidReason": null
}
```

<a name="void"></a>
##void

Void a ticket.

### void Request

```PUT /registration/ticket/{id}/void ```

### void Parameters

|Parameter|Meaning|Notes|
|---|---|---|
|id|`ticket` The ticket being marked as void.||

### void Request Body
|Parameter|Meaning|Notes|
|---|---|---|
|reason|Reason for voiding ticket.|**required**|

### void Response

Does not supply a response.

### void Code Samples
Request Sample

```
curl -X PUT -H 'Authorization: Bearer e0438d90599b1c4762d12fd03db6311c9ca46729' http://localhost/api/registration/ticket/1/void?pretty=true
```
Response Sample

```
[]
```

<a name="reinstate"></a>
##reinstate

Reinstate a voided Ticket.

### reinstate Request

```PUT /registration/ticket/{id}/reinstate ```

### reinstate Parameters

|Parameter|Meaning|Notes|
|---|---|---|
|id|`ticket` The ticket being reinstated.||

### reinstate Request Body
Do not supply a request body.

### reinstate Response

A [ticket](#ticket) object

### reinstate Code Samples
Request Sample

```
curl -X GET -H 'Authorization: Bearer e0438d90599b1c4762d12fd03db6311c9ca46729' http://localhost/api/registration/ticket/1/reinstate?pretty=true
```
Response Sample

```
{
    "member": "1000",
    "badgeDependentOn": null,
    "badgeName": "foo",
    "badgesPickedUp": "0",
    "boardingPassGenerated": "2020-12-29 19:40:47",
    "emergencyContact": null,
    "event": "1",
    "id": "1",
    "lastPrintedDate": "2020-12-29 19:40:50",
    "note": null,
    "printRequestIp": null,
    "printRequested": null,
    "registeredBy": "1000",
    "registrationDate": "2020-10-11 00:15:58",
    "type": "ticket",
    "ticketType": "4",
    "voidBy": null,
    "voidDate": null,
    "voidReason": null
}
```

<a name="post"></a>
##post

Add a new Ticket.

### post Request

```POST /registration/ticket```

### post Parameters

No additional parameters.

### post Request Body

|Parameter|Meaning|Notes|
|---|---|---|
|member|Member acquiring the ticket|**required**|
|ticketType|Type of the ticket|**required**|
|event|ID Event of the ticket|default is current event|
|dependOn|Member ID the dependent ticket is dependent on|Optional|
|badgeName|Badge name for ticket|Optional|
|contact|Emergency Contact|Optional|
|registeredBy|Member creating the ticket|default is member|

### post Response

A [ticket](#ticket) object

### post Code Samples
Request Sample

```
curl -X POST -H 'Authorization: Bearer e0438d90599b1c4762d12fd03db6311c9ca46729' http://localhost/api/registration/ticket?pretty=true -d {'member':'1000', 'ticketType':'4'}
```
Response Sample

```
{
    "member": "1000",
    "badgeDependentOn": null,
    "badgeName": null,
    "badgesPickedUp": null,
    "boardingPassGenerated": null,
    "emergencyContact": null,
    "event": "1",
    "id": "109",
    "lastPrintedDate": null,
    "note": null,
    "printRequestIp": null,
    "printRequested": null,
    "registeredBy": "1000",
    "registrationDate": 2021-1-2 19:40:47,
    "type": "ticket",
    "ticketType": "4",
    "voidBy": null,
    "voidDate": null,
    "voidReason": null
}
```

<a name="delete"></a>
##delete

Completely deletes a ticket. **This is not reversable.**

### delete Request

```DELETE /registration/ticket/{id}```

### delete Parameters

|Parameter|Meaning|Notes|
|---|---|---|
|id|`ticket` The ticket being deleted.||

### delete Request Body
Do not supply a request body.

### delete Response

Does not supply a response.

### delete Code Samples
Request Sample

```
curl -X DELETE -H 'Authorization: Bearer e0438d90599b1c4762d12fd03db6311c9ca46729' http://localhost/api/registration/ticket/109?pretty=true
```
Response Sample

```
[]
```

---
##### [Return to Top](README.md)&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;[Return to Resource List](README.md#resources)
