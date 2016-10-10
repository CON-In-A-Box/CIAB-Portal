<script src="sitesupport/validation.js"></script>
<script type="text/javascript">
function validate_form(thisform)
{
  with (thisform)
  {
    if (validate_select(position,"Need to Select a position!")==false)
    {position.focus();return false}
    if (validate_required(user,"User must be filled out!")==false)
    {user.focus();return false}
    if (validate_required(password,"Password must be entered!")==false)
    {password.focus();return false}
  }
}
</script>
