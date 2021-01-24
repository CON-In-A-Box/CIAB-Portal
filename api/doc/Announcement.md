##### [Return to Top](README.md)&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;[Return to Resource List](README.md#resources)
---
# Announcement Resource
The following methods are available to announcement resources:

|Resource Method|HTTP Request|Description|module|RBAC|
|---|---|---|---|---|
|[add](Announcement.md#add)|POST /announcement/{department}|Add a new announcement.|core|api.post.announcement.{department}|
|[get](Announcement.md#get)|GET /announcement/{id}|Get details about a given announcement.|core|-|
|[modify](Announcement.md#modify)|PUT /announcement/{id}|Modify an existing announcement|core|api.put.announcement.{department}|
|[delete](Announcement.md#delete)|DELETE /announcement/{id}|Delete a announcement|core|api.delete.announcement.{department}|

## Permissions
|Resource Method|HTTP Request|Description|module|RBAC|
|---|---|---|---|---|
|[resource](Permissions.md#resource_announcement)|GET /permissions/resource/announcement/{department}[/{method}]|Check permissions by announcement resource identifier and optionally method.|core|-|
|[method](Permissions.md#method_announcement)|GET /permissions/method/announcement/[/{method}[/{department}]]|Check permissions by announcement resource and optionally method and parameter.|core|-|

<a name="common_objects"></a>
## Common Objects

#### Announcement object resource
A announcement object resource is returned.

```
{
    "type": "announcement",
    "id": {integer},
    "departmentId": {integer},
    "postedOn": {date},
    "postedBy": {memberId},
    "scope": {integer},
    "text": {string},
    "links": [HATEOAS links]
}
```

Announcement object resources have a number of available properties. These include:

|Object Property|Value|Description| Includable|
|---|---|---|---|
|type|string|Always `announcement`|-|
|id|integer|announcement ID|-|
|departmentId|integer|Department ID for the announcement|**yes** `department`|
|postedOn|date|Date the announcement was first posted|-|
|postedBy|integer|Member Id of the member who created the announcement.|**yes** `member`|
|scope|integer|The scope of the announcement|-|
|text|string|Text of the announcement|-|
|links[]|list|List of HATEOAS links for this announcement|-|

The following HATEOAS methods are available as well:

|HATEOAS Method|Request|Description|
|---|---|---|
|modify|POST|Method to modify the announcement.|
|delete|DELETE|Method to delete the announcement.|
|department|GET|Method to get the department for the announcement.|


#### announcement_list object resource

A announcement_list object resource is used when listing announcements.

```
{
	"type":"announcement_list",
	"data":[{announcement}]
}
```

announcement_list object resources have the following available properties:

|Object Property|Value|Description|
|---|---|---|
|type|string|Always `announcement_list`.|
|data[]|list|A list of `announcement` resources.|


<a name="add"></a>
## add
Add a new announcement.

### add Request

```POST /announcement/{department}?Scope={integer}&Text={text}[&Email={bool}]```

### add Parameters
The following parameters are available:

|Parameter|Meaning|Notes|
|---|---|---|
|department|Department adding the announcement.|`integer` id or `string` name|
|Scope|Scope of the announcement.|*required*|
|Text|Text of the announcement.|*required*|
|Email|Send announcment via email to all relevent members.|default `true`|


### add Request Body
Do not supply a request body.

### add Response

Does not return a response.

### add Code Samples
Request Sample

```
curl -X POST -H 'Authorization: Bearer e0438d90599b1c4762d12fd03db6311c9ca46729' http://localhost/api/announcement/Art%20Show?Scope=0&Text=Testing
```
Response Sample

```
[]
```

<a name="get"></a>
## get
Get information about a given announcement.

### get Request

```GET /announcement/{id}```

### get Parameters
The following parameters are available:

|Parameter|Meaning|Notes|
|---|---|---|
|id|Announcement for which data is being retrieved.||

### get Request Body
Do not supply a request body.

### get Response

A [announcement](#common_objects) resource.

### get Code Samples
Request Sample

```
curl -X GET -H 'Authorization: Bearer e0438d90599b1c4762d12fd03db6311c9ca46729' http://localhost/api/announcement/1?pretty=true
```
Response Sample

```
{
    "type": "announcement",
    "id": "1",
    "departmentId": "114",
    "postedOn": "2020-02-02",
    "postedBy": 1000,
    "scope": 0,
    "text": "This is an important announcement",    
    "links": [
        {
            "method": "self",
            "href": "http:\/\/localhost\/api\/announcement\/1",
            "request": "GET"
        },
        {
            "method": "modify",
            "href": "http:\/\/localhost\/api\/announcement\/1",
            "request": "POST"
        },
        {
            "method": "delete",
            "href": "http:\/\/localhost\/api\/announcement\/1",
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
Modify an existing announcement.

### modify Request

```PUT /announcement/{id}?Scope={integer}&Department={department}&Text={text}```

### modify Parameters
The following parameters are available:

|Parameter|Meaning|Notes|
|---|---|---|
|id|The ID of the announcement being modified|*required*|
|Scope|Scope of the announcement.||
|Department|Department for which data is being retrieved.|`integer` id or `string` name|
|Text|Text of the announcement.||


### modify Request Body
Do not supply a request body.

### modify Response

Does not return a response.

### modify Code Samples
Request Sample

```
curl -X PUT -H 'Authorization: Bearer e0438d90599b1c4762d12fd03db6311c9ca46729' http://localhost/api/announcement/1/Department=Art%20Show?Scope=1&Text=Testing
```
Response Sample

```
[]
```

<a name="delete"></a>
## delete
Delete an existing announcement.

### delete Request

```DELETE /announcement/{id}```

### delete Parameters
The following parameters are available:

|Parameter|Meaning|Notes|
|---|---|---|
|id|The ID of the announcement being modified|*required*|

### delete Request Body
Do not supply a request body.

### delete Response

Does not return a response.

### delete Code Samples
Request Sample

```
curl -X DELETE -H 'Authorization: Bearer e0438d90599b1c4762d12fd03db6311c9ca46729' http://localhost/api/announcement/1
```
Response Sample

```
[]
```

---
##### [Return to Top](README.md)&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;[Return to Resource List](README.md#resources)
