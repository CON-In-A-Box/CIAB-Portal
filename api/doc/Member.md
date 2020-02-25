##### [Return to Top](README.md)&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;[Return to Resource List](README.md#resources)
---
# Member Resource

The following methods are available to member resources:


|Resource Method|HTTP Request|Description|Module|RBAC|
|---|---|---|---|---|
|[status](Member.md#status)|GET /member/{id}/status|Returns a code based on the status of the user account specified by `id`|core|--|
|[get](Member.md#get)|GET /member/{id}|Get details about a given member. To get information about the current authenticated user, leave the [id] blank.|core|--|
|[deadlines](Member.md#deadlines)|GET /member/{id}/deadlines|Get deadlines for a given member.|core|--|
|[announcements](Member.md#announcements)|GET /member/{id}/announcements|Get a list of announcements for a given member.|core|-|
|[concom](Concom.md#member)|GET /member/{id}/concom|Get the list of concom positions the member fills, leave the `id` out specify the logged in Member|[concom](Concom.md)|api.get.concom|

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

The following HATEOAS methods are available as well:

|HATEOAS Method|Request|Description|
|---|---|---|
|deadlines|GET|Get a list of deadlines for the member.|

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

<a name="deadlines"></a>
## deadlines
Get a list of deadlines for a member.

### deadlines Request

```GET /member/{identifier}/deadlines```

### deadlines Parameters

The following deadlines parameters are available:

|Parameter|Meaning|Notes|
|---|---|---|
|identifier|Name or Id of the member||
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
    http://localhost/api/member/1001/deadlines?pretty=true
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
            "departmentID": "7",
            "deadline": "2019-12-27",
            "get": "http:\/\/localhost:8080\/api\/deadline\/1"
        }
    ]
}
```

<a name="announcements"></a>
## announcements
Get a list of announcements for a member.

### announcements Request

```GET /member/{identifier}/announcements```

### announcements Parameters

The following announcements parameters are available:

|Parameter|Meaning|Notes|
|---|---|---|
|identifier|Name or Id of the member||
|page|Page token for the list.|Defaults to first page.|
|maxResults|Maximum results in the list|Defaults to 100. The token "all" specifies the full remaining list.|

### announcements Request Body

Do not supply a request body.

### announcements Response
A [announcement_list](Announcement.md#common_objects) object resource.

### announcements Code Samples
Request Sample

```
curl -X GET -H 'Authorization: Bearer e0438d90599b1c4762d12fd03db6311c9ca46729' \
    http://localhost/api/member/1001/announcements?pretty=true
```
Response Sample

```
{
    "type": "announcement_list",
    "data": {
        {
            "type": "announcement_entry",
            "id": "5",
            "departmentID": "7",
            "postedOn": "2020-11-11",
            "postedBy": 1000,
            "scope": 0,
            "text": "This is an important announcement",  
            "get": "http:\/\/localhost:8080\/api\/deadline\/5"
        },
        {
            "type": "deadline_entry",
            "id": "1",
            "departmentID": "7",
            "postedOn": "2020-11-11",
            "postedBy": 1000,
            "scope": 0,
            "text": "This is another very important announcement", 
            "get": "http:\/\/localhost:8080\/api\/deadline\/1"
        }
    ]
}
```

---
##### [Return to Top](README.md)&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;[Return to Resource List](README.md#resources)
