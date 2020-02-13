##### [Return to Top](README.md)&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;[Return to Resource List](README.md#resources)
---
# Admin Module

The Admin module includes the following methods.

|Resource Method|HTTP Request|Description|module|RBAC|
|---|---|---|---|---|
|[sudo](Admin.md#sudo)|POST /admin/SUDO/{name}|Convert login to a new Member.|core|admin.sudo|


<a name="sudo"></a>
## sudo
Convert login to a new Member.

### sudo Request

```POST /admin/SUDO/{name} ```

### sudo Parameters

The following sudo parameters are available:

|Parameter|Meaning|Notes|
|---|---|---|
|name|The member to convert to.||

### sudo Request Body

Do not supply a request body.

### sudo Response

Does not supply any response.

### sudo Code Samples
Request Sample

```
curl -X POST -H 'Authorization: Bearer e0438d90599b1c4762d12fd03db6311c9ca46729' \
    http://localhost/api/admin/SUDO/2034
```
Response Sample

```
{}
```
---
##### [Return to Top](README.md)&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;[Return to Resource List](README.md#resources)
