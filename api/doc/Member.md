##### [Return to Top](README.md)&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;[Return to Resource List](README.md#resources)
---
# Member Resource

The following methods are available to member resources:


|Resource Method|HTTP Request|Description|RBAC|
|---|---|---|---|
|[status](Member.md#status)|GET /member/{id}/status|Returns a code based on the status of the user account specified by `id`|--|
|[get](Member.md#get)|GET /member/{id}|Get details about a given member. To get information about the current authenticated user, leave the [id] blank.|--|

<a name="status"></a>
## status

Returns a code based on the status of a user account.

*Note*: Authentication is not required to use this entry point.

### status Request

```GET /member/{id}/status```

### status Parameters

The following find parameters are available:

|Parameter|Meaning|Notes|
|---|---|---|
|id|Member for which data is being retrieved.||

### status Request Body
Do not supply a request body.

### status Response
A status object is returned.

```
{
    "type": "member_status",
    "status": {integer},
}
```

Status object have a number of available properties. These include:

|Object Property|Value|Description|
|---|---|---|
|type|string|Always `member_status`|
|status|integer|status code of the member.|

* status 0x0 is account is valid
* status 0x1 is no such account
* status 0x2 is password expired
* status 0x3 is account locked
* status 0x10 is added if the account has multiple entries

### get Code Samples
Request Sample

```
curl -X GET -H 'Authorization: Bearer e0438d90599b1c4762d12fd03db6311c9ca46729' http://localhost/api/member/1002/status?pretty=true
```
Response Sample

```
{
    "type": "member_status",
    "status": 0
}
```


<a name="get"></a>
## get

Get information about a member in the system.

### get Request

```GET /member/[{id}]```

### get Parameters

The following find parameters are available:

|Parameter|Meaning|Notes|
|---|---|---|
|id|Member for which data is being retrieved. To get information about the current authenticated member, leave blank.||

### get Request Body
Do not supply a request body.

### get Response
A member object resource is returned.

```
{
    "id": {integer},
    "firstName": {string},
    "LastName": {string},
    "email": {string},
    "type": "member",
    "links": [{HATEOAS link}]
}
```

Member object resources have a number of available properties. These include:

|Object Property|Value|Description|
|---|---|---|
|id|integer|ID of the member|
|firstName|string|Members preferred first name|
|lastName|string|Members preferred last name|
|email|string|Members primary email|
|type|string|Always `member`|
|links[]|list| List of HATEOAS links for this member|

### get Code Samples
Request Sample

```
curl -X GET -H 'Authorization: Bearer e0438d90599b1c4762d12fd03db6311c9ca46729' http://localhost/api/member1002?pretty=true
```
Response Sample

```
{
    "id": "1002",
    "firstName": "koaWebgou",
    "lastName": "Zuimenmohnun",
    "email": "koaWebgou@email.net",
    "type": "member",
    "links": [
        {
            "method": "self",
            "href": "http:\/\/localhost:8080\/api\/member\/1002",
            "request": "GET"
        }
    ]
}
```

---
##### [Return to Top](README.md)&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;[Return to Resource List](README.md#resources)
