function validate_required(field,alerttxt)
{
with (field)
{
if (value==null||value=="")
  {alert(alerttxt);return false}
else {return true}
}
}
// example of required
// function validate_form(thisform)
// {
// with (thisform)
// {
// if (validate_required(email,"Name must be filled out!")==false)
//   {name.focus();return false}
// }
// }

function validate_select(field,alerttxt)
{
with (field)
{
if (selectedIndex==null||selectedIndex=="0")
  {alert(alerttxt);return false}
else {return true}
}
}
// example of required
// Note:  Will only validate that index isn't 0 (the first option)
// function validate_form(thisform)
// {
// with (thisform)
// {
// if (validate_select(Item,"Must make a selection!")==false)
//   {Item.focus();return false}
// }
// }


function validate_email(field,alerttxt)
{
with (field)
{
apos=value.indexOf("@")
dotpos=value.lastIndexOf(".")
if (apos<1||dotpos-apos<2) 
  {alert(alerttxt);return false}
else {return true}
}
}

// example of email
// function validate_form(thisform)
// {
// with (thisform)
// {
// if (validate_email(email,"Not a valid e-mail address!")==false)
//   {email.focus();return false}
// }
// }
