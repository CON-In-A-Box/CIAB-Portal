##### [Return to Top](README.md)&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;[Return to Resource List](README.md#resources)
---
# Member Resource

The following methods are available to member resources:


|Resource Method|HTTP Request|Description|Module|RBAC|
|---|---|---|---|---|
|[status](Member.md#status)|GET /member/{id}/status|Returns a code based on the status of the user account specified by `id`|core|--|
|[create](Member.md#create)|POST /member|Create a new Member|core|--|
|[createPassword](Member.md#createPassword)|POST /member/{id}/password|Create a new temporary password for a member and email the account.|core|--|
|[get](Member.md#get)|GET /member/{id}|Get details about a given member. To get information about the current authenticated user, leave the [id] blank.|core|--|
|[update](Member.md#update)|PUT /member/{id}|Update details about a given member.|core|api.put.member|
|[updatePassword](Member.md#updatePassword)|PUT /member/{id}/password|Update the password for a member.|core|api.put.member.password|
|[deadlines](Member.md#deadlines)|GET /member/{id}/deadlines|Get deadlines for a given member.|core|--|
|[announcements](Member.md#announcements)|GET /member/{id}/announcements|Get a list of announcements for a given member.|core|-|
|[staff](Staff.md#member)|GET /member/{id}/staff_membership|Get the list of staff positions the member fills, leave the `id` out specify the logged in Member|[staff](Staff.md)|api.get.staff|

<a name="common_objects"></a>
## Common Objects

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
|type|string|Always `member`|
|id|integer|ID of the member|
|firstName|string|Members preferred first name|
|lastName|string|Members preferred last name|
|email|string|Members primary email|
|legalFirstName|string|Members legal first name.|
|legalLastName|string|Members legal last name.|
|middleName|string|Member's middle name.|
|suffix|string|Suffix for members name.|
|email2|string|Member's second email.|
|email3|string|Member's third email|
|phone1|string|Member's primary phone|
|phone2|string|Member's secondary phone|
|addressLine1|string|Member's address line 1|
|addressLine2|string|Member's address line 2|
|city|string|Member's address city.|
|state|string|Member's address state|
|zipCode|string|Member's Address Zip code.|
|zipPlus4|string|Member's Address Zip code suffix|
|countryName|string|Member's Address country.|
|province|string|Member's Address province.|
|preferredFirstName|string|Member's Preferred First Name.|
|preferredLastName|string|Member's Preferred Last Name.|
|Deceased|Boolean|Is member deceased.|
|DoNotContact|Boolean|Do not contact member.|
|EmailOptOut|Boolean|Do not mass email member.|
|Birthdate|Date|Member's birth date.|
|Gender|string|Member's preferred gender string.|
|conComDisplayPhone|Boolean|If Staff display phone on list.|
|links[]|list| List of HATEOAS links for this member|

If a property is unset on the object then it will not be returned. 

The following HATEOAS methods are available as well:

|HATEOAS Method|Request|Description|
|---|---|---|
|deadlines|GET|Get a list of deadlines for the member.|
|update|PUT|Update the member data.|
|updatePassword|PUT|Update the member's temporary password.|

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

### status Code Samples
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

<a name="create"></a>
## create

Create a member in the system and generates a temporary password for the member and e-mails the password to the email for the newly created member.

*Note*: Authentication is not required to use this entry point.

### create Request

```POST /member```

### create Parameters

There are no parameters available.

### create Request Body

|Parameter|Meaning|Notes|
|---|---|---|
|email1|Primary E-Mail address and login username|<b>Required</b>|
|firstName|Members legal first name.|<b>Required</b>|
|lastName|Members legal last name.|<b>Required</b>|
|middleName|Member's middle name.|Optional|
|suffix|Suffix for members name.|Optional|
|email2|Member's second email.|Optional|
|email3|Member's third email|Optional|
|phone1|Member's primary phone|Optional|
|phone2|Member's secondary phone|Optional|
|addressLine1|Member's address line 1|Optional|
|addressLine2|Member's address line 2|Optional|
|city|Member's address city.|Optional|
|state|Member's address state|Optional|
|zipCode|Member's Address Zip code.|Optional|
|zipPlus4|Member's Address Zip code suffix|Optional|
|countryName|Member's Address country.|Optional|
|province|Member's Address province.|Optional|
|preferredFirstName|Member's Preferred First Name.|Optional|
|preferredLastName|Member's Preferred Last Name.|Optional|
|Deceased|Boolean: Is member deceased.|Optional|
|DoNotContact|Boolean: Do not contact member.|Optional|
|EmailOptOut|Boolean: Do not mass email member.|Optional|
|Birthdate|Date: Member's birth date.|Optional|
|Gender|Member's preferred gender string.|Optional|
|conComDisplayPhone|Boolean: If Staff display phone on list.|Optional|


### create Response
A member object resource is returned.

###create Code Samples
Request Sample

```
curl -X POST -H 'Authorization: Bearer e0438d90599b1c4762d12fd03db6311c9ca46729' http://localhost/api/member?pretty=true \
-d { 'email1' : 'koaWebgou@email.net', \
	 'firstName' : 'koaWebgou', \
	 'lastName' : 'Zuimenmohnun'}
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

<a name="createPassword"></a>
## createPassword

Generate a new temporary password for a member.

*Note*: Authentication is not required to use this entry point.

### createPassword Request

```POST /member/{id}/password```

### createPassword Parameters

The following parameters are available:

|Parameter|Meaning|Notes|
|---|---|---|
|id|Member for which data is being retrieved.||


### createPassword Request Body

Do not supply a request body.

### createPassword Response

Empty if successful

### createPassword Code Samples
Request Sample

```
curl -X POST -H 'Authorization: Bearer e0438d90599b1c4762d12fd03db6311c9ca46729' http://localhost/api/member/1002/password?pretty=true \
```
Response Sample

```
{ }
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

<a name="update"></a>
## update

Update a member information on the system.

### update Request

```PUT /member/{id}```

### update Parameters

There are no parameters available.

### update Request Body

|Parameter|Meaning|Notes|
|---|---|---|
|firstName|Members first name.|Optional|
|lastName|Members last name.|Optional|
|middleName|Member's middle name.|Optional|
|suffix|Suffix for members name.|Optional|
|email2|Member's second email.|Optional|
|email3|Member's third email|Optional|
|phone1|Member's primary phone|Optional|
|phone2|Member's secondary phone|Optional|
|addressLine1|Member's address line 1|Optional|
|addressLine2|Member's address line 2|Optional|
|city|Member's address city.|Optional|
|state|Member's address state|Optional|
|zipCode|Member's Address Zip code.|Optional|
|zipPlus4|Member's Address Zip code suffix|Optional|
|countryName|Member's Address country.|Optional|
|province|Member's Address province.|Optional|
|preferredFirstName|Member's Preferred First Name.|Optional|
|preferredLastName|Member's Preferred Last Name.|Optional|
|Deceased|Boolean: Is member deceased.|Optional|
|DoNotContact|Boolean: Do not contact member.|Optional|
|EmailOptOut|Boolean: Do not mass email member.|Optional|
|Birthdate|Date: Member's birth date.|Optional|
|Gender|Member's preffered gender string.|Optional|
|conComDisplayPhone|Boolean: If Staff display phone on list.|Optional|
...

### update Response
A member object resource is returned.

###update Code Samples
Request Sample

```
curl -X POST -H 'Authorization: Bearer e0438d90599b1c4762d12fd03db6311c9ca46729' http://localhost/api/member?pretty=true \
-d { 'email1' : 'koaWebgou@email.net', \
	 'firstName' : 'koaWebgou', \
	 'lastName' : 'Zuimenmohnun'}
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

<a name="updatePassword"></a>
## updatePassword

Save a new temporary or primary password for a member. Unless the `api.put.member.password` RBAC is set a member can only update their own password.

### updatePassword Request

```PUT /member/{id}/password```

### updatePassword Parameters

The following parameters are available:

|Parameter|Meaning|Notes|
|---|---|---|
|id|Member for which the password is being updated.||


### updatePassword Request Body

|Parameter|Meaning|Notes|
|---|---|---|
|NewPassword|The new password being set|*Required*|
|OldPassword|The existing password|Required unless the `api.put.member.password` RBAC is set|
|Temporary|Set the temporary password|Boolean, default `False`|

### updatePassword Response

Empty if successful

### updatePassword Code Samples
Request Sample

```
curl -X PUT -H 'Authorization: Bearer e0438d90599b1c4762d12fd03db6311c9ca46729' http://localhost/api/member/1002/password?pretty=true \
-d {'OldPassword' : 'fishy', 'NewPassword' : 'sticks'}
```
Response Sample

```
{ }
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
