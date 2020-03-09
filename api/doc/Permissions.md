##### [Return to Top](README.md)&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;[Return to Resource List](README.md#resources)
---
# Permissions Base Resource
The following methods are available to various resources permission resources:

|Resource Method|HTTP Request|Description|module|RBAC|
|---|---|---|---|---|
|[resource](Permissions.md#resource)|GET /permissions/resource/{resource}/{identifier}[/{method}]|Check permissions by resource identifier and optionally method.|core|-|
|[method](Permissions.md#method)|GET /permissions/method/{resource}/[/{method}[/{parameters}]]|Check permissions by resource and optionally method and parameter.|core|-|

<a name="common_objects"></a>
## Common Objects

#### base permission object resource
A base permissions object resource. Resource specific details will be found in the `subtype`.

```
{
    "type": "permissions_entry",
    "subtype": {string},
    "allowed": {boolean},
    "action": {HATEOAS method},
    "subdata": {custom data based on target resource}
}
```

Permission object resources have a number of available properties. These include:

|Object Property|Value|Description|Includable|
|---|---|---|---|
|type|string|Always `permissions_entry `|-|
|subtype|string|subtype identifier|-|
|allowed|boolean|Is the described resource permitted|-|
|action|hateoas link|A HATEOAS link describing the action|-|
|subdata|{}|A resource with data based on subtype|-|


#### permission_list object resource

A permission_list object resource is used when listing permissions.

```
{
	"type":"permission_list",
	"data":[{permission}]
}
```

permission_list object resources have the following available properties:

|Object Property|Value|Description|
|---|---|---|
|type|string|Always `permission_list`.|
|data[]|list|A list of `permission` resources.|


<a name="deadline_common_objects"></a>
## Deadline Common Objects

Deadline Permission `deadline.subdata`

```
{
	"departmentId": {integer}
}
```

|Object Property|Value|Description|Includable|
|---|---|---|---|
|departmentId|integer|Id for the department described|**yes**`department` resource|


Deadline `permission` resource

```
{
    "type": "permissions_entry",
    "subtype": "deadline_"{method},
    "allowed": {boolean},
    "action": {HATEOAS method},
    "subdata": {deadline.subdata}
}
```

|Object Property|Value|Description|Includable|
|---|---|---|---|
|type|string|Always `permissions_entry `|-|
|subtype|string|Always `deadline_` with method|-|
|allowed|boolean|Is the described resource permitted|-|
|action|hateoas link|A HATEOAS link describing the action|-|
|subdata|{`deadline.subdata`}|A `deadline.subdata` resource|-|


<a name="resource_deadline"></a>
## Deadline Resource
Check permissions on a deadline resource.

### deadline resource Request

```GET /permissions/resource/deadline/{department}[/{method}]```

### deadline resource Parameters
The following parameters are available:

|Parameter|Meaning|Notes|
|---|---|---|
|department|Department for which permissions is being retrieved.|`integer` id or `string` name|
|method|Method on the department resource being checked.|Optional|


### deadline resource Request Body
Do not supply a request body.

### deadline resource Response

`permission_list` of [Deadline permissions](#deadline_common_objects) permissions or `permission_entry` if only 1 entry.

### deadline resource Code Samples
Request Sample

```
curl -X GET -H 'Authorization: Bearer e0438d90599b1c4762d12fd03db6311c9ca46729' http://localhost/api/permissions/resource/deadline/Art%20Show
```
Response Sample

```
{
    "type": "permission_list",
    "data": [
        {
            "type": "permission_entry",
            "subtype": "deadline_get",
            "allowed": true,
            "action": {
                "method": "get",
                "href": "http:\/\/localhost\/api\/deadline\/104",
                "request": "GET"
            },
            "subdata": {
                "departmentId": 104
            }
        },
        {
            "type": "permission_entry",
            "subtype": "deadline_put",
            "allowed": true,
            "action": {
                "method": "put",
                "href": "http:\/\/localhost\/api\/deadline\/104",
                "request": "PUT"
            },
            "subdata": {
                "departmentId": 104
            }
        },
        {
            "type": "permission_entry",
            "subtype": "deadline_post",
            "allowed": true,
            "action": {
                "method": "post",
                "href": "http:\/\/localhost\/api\/deadline\/104",
                "request": "POST"
            },
            "subdata": {
                "departmentId": 104
            }
        },
        {
            "type": "permission_entry",
            "subtype": "deadline_delete",
            "allowed": true,
            "action": {
                "method": "delete",
                "href": "http:\/\/localhost\/api\/deadline\/104",
                "request": "DELETE"
            },
            "subdata": {
                "departmentId": 104
            }
        }
    ]
}
```

<a name="method_deadline"></a>
## Deadline Method
Check permissions on a deadline methods.

### deadline method Request

```GET /permissions/method/deadline/[/{method}[/{department}]]```

### deadline method Parameters
The following parameters are available:

|Parameter|Meaning|Notes|
|---|---|---|
|method|Method on the department resource being checked.|Optional|
|department|Department for which permissions is being retrieved.|`integer` id or `string` name|


### deadline method Request Body
Do not supply a request body.

### deadline method Response

`permission_list` of [Deadline permissions](#deadline_common_objects) permissions or `permission_entry` if only 1 entry.

### deadline method Code Samples
Request Sample

```
curl -X GET -H 'Authorization: Bearer e0438d90599b1c4762d12fd03db6311c9ca46729' http://localhost/api/permissions/method/deadline/get/Art%20Show
```
Response Sample

```
{
    "type": "permission_entry",
    "subtype": "deadline_get",
    "allowed": true,
    "action": {
        "method": "get",
        "href": "http:\/\/localhost\/api\/deadline\/104",
        "request": "GET"
    },
    "subdata": {
        "departmentId": 104
    }
}
```


<a name="announcement_common_objects"></a>
## Announcement Common Objects

Announcement Permission `announcement.subdata`

```
{
	"departmentId": {integer}
}
```

|Object Property|Value|Description|Includable|
|---|---|---|---|
|departmentId|integer|Id for the department described|**yes**`department` resource|


Announcement `permission` resource

```
{
    "type": "permissions_entry",
    "subtype": "announcement_"{method},
    "allowed": {boolean},
    "action": {HATEOAS method},
    "subdata": {announcement.subdata}
}
```

|Object Property|Value|Description|Includable|
|---|---|---|---|
|type|string|Always `permissions_entry `|-|
|subtype|string|Always `announcement_` with method|-|
|allowed|boolean|Is the described resource permitted|-|
|action|hateoas link|A HATEOAS link describing the action|-|
|subdata|{`announcement.subdata`}|A `announcement.subdata` resource|-|


<a name="resource_announcement"></a>
## Announcement Resource
Check permissions on a announcement resource.

### announcement resource Request

```GET /permissions/resource/announcement/{department}[/{method}]```

### announcement resource Parameters
The following parameters are available:

|Parameter|Meaning|Notes|
|---|---|---|
|department|Department for which permissions is being retrieved.|`integer` id or `string` name|
|method|Method on the department resource being checked.|Optional|


### announcement resource Request Body
Do not supply a request body.

### announcement resource Response

`permission_list` of [Announcement permissions](#announcement_common_objects) permissions or `permission_entry` if only 1 entry.

### announcement resource Code Samples
Request Sample

```
curl -X GET -H 'Authorization: Bearer e0438d90599b1c4762d12fd03db6311c9ca46729' http://localhost/api/permissions/resource/announcement/Art%20Show
```
Response Sample

```
{
    "type": "permission_list",
    "data": [
        {
            "type": "permission_entry",
            "subtype": "announcement_put",
            "allowed": true,
            "action": {
                "method": "put",
                "href": "http:\/\/localhost\/api\/announcement\/104",
                "request": "PUT"
            },
            "subdata": {
                "departmentId": 104
            }
        },
        {
            "type": "permission_entry",
            "subtype": "announcement_post",
            "allowed": true,
            "action": {
                "method": "post",
                "href": "http:\/\/localhost\/api\/announcement\/104",
                "request": "POST"
            },
            "subdata": {
                "departmentId": 104
            }
        },
        {
            "type": "permission_entry",
            "subtype": "announcement_delete",
            "allowed": true,
            "action": {
                "method": "delete",
                "href": "http:\/\/localhost\/api\/announcement\/104",
                "request": "DELETE"
            },
            "subdata": {
                "departmentId": 104
            }
        }
    ]
}
```

<a name="method_announcement"></a>
## Announcement Method
Check permissions on a announcement methods.

### announcement method Request

```GET /permissions/method/announcement/[/{method}[/{department}]]```

### announcement method Parameters
The following parameters are available:

|Parameter|Meaning|Notes|
|---|---|---|
|method|Method on the department resource being checked.|Optional|
|department|Department for which permissions is being retrieved.|`integer` id or `string` name|


### announcement method Request Body
Do not supply a request body.

### announcement method Response

`permission_list` of [Announcement permissions](#announcement_common_objects) permissions or `permission_entry` if only 1 entry.

### announcement method Code Samples
Request Sample

```
curl -X GET -H 'Authorization: Bearer e0438d90599b1c4762d12fd03db6311c9ca46729' http://localhost/api/permissions/method/announcement/get/Art%20Show
```
Response Sample

```
{
    "type": "permission_entry",
    "subtype": "announcement_put",
    "allowed": true,
    "action": {
        "method": "put",
        "href": "http:\/\/localhost\/api\/announcement\/104",
        "request": "PUT"
    },
    "subdata": {
        "departmentId": 104
    }
}
```

---
##### [Return to Top](README.md)&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;[Return to Resource List](README.md#resources)
