<meta http-equiv="Cache-control" content="No-Cache">
<script src="sitesupport/alertbox.js"></script>
<script src="sitesupport/validation.js"></script>
<script src="sitesupport/password.js"></script>
<script type="text/javascript">
function validate_form(thisform)
{
  with (thisform)
  {
    if (validateRequired(firstName,"Please enter a legal first name")==false)
    {firstName.focus();return false}
    if (validateRequired(lastName,"Please enter a legal last name")==false)
    {lastName.focus();return false}
    if (validateEmail(email1,"Must have at least one valid Email address")==false)
    {email1.focus();return false}
    if (validateRequired(phone1,"Must have at least one phone number")==false)
    {phone1.focus();return false}
    if (validateRequired(addressLine1,"Address cannot be blank")==false)
    {addressLine1.focus();return false}
    if (validateRequired(city,"City cannot be blank")==false)
    {city.focus();return false}
    if (validateRequired(zipCode,"Zip Code cannot be blank")==false)
    {zipCode.focus();return false}
    if (validateSelect(country,"Please select a country")==false)
    {country.focus();return false}
  }
}
</script>
