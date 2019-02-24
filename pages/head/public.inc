<meta http-equiv="Cache-control" content="No-Cache">
<script src="sitesupport/validation.js"></script>
<script type="text/javascript">
function validate_form(thisform)
{
  with (thisform)
  {
    if (validateRequired(user,"User must be filled out!")==false)
    {user.focus();return false}
    if (validateRequired(password,"Password must be entered!")==false)
    {password.focus();return false}
  }
}
</script>
