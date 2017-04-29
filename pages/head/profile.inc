<script src="sitesupport/validation.js"></script>
<script type="text/javascript">
function validate_form(thisform)
{
  with (thisform)
  {
    if (validate_required(firstName,"Please enter a legal first name")==false)
    {firstName.focus();return false}
    if (validate_required(lastName,"Please enter a legal last name")==false)
    {lastName.focus();return false}
    if (validate_email(email1,"Must have at least one valid Email address")==false)
    {email1.focus();return false}
    if (validate_required(phone1,"Must have at least one phone number")==false)
    {phone1.focus();return false}
    if (validate_required(addressLine1,"Address cannot be blank")==false)
    {addressLine1.focus();return false}
    if (validate_required(city,"City cannot be blank")==false)
    {city.focus();return false}
    if (validate_required(zipCode,"Zip Code cannot be blank")==false)
    {zipCode.focus();return false}
    if (validate_select(country,"Please select a country")==false)
    {country.focus();return false}
  }
}
</script>
