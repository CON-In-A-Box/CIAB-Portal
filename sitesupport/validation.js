function validateRequired(field,alerttxt) {
  if (field.value === null || field.value === '') {
    alert(alerttxt);
    return false;
  } else {
    return true;
  }
}
// example of required
// function validateForm(thisform)
// {
// with (thisform)
// {
// if (validateRequired(email,"Name must be filled out!")==false)
//   {name.focus();return false}
// }
// }

function validateSelect(field,alerttxt) {
  if (field.selectedIndex === null || field.selectedIndex == '0') {
    alert(alerttxt);return false;
  } else {
    return true;
  }
}
// example of required
// Note:  Will only validate that index isn't 0 (the first option)
// function validateForm(thisform)
// {
// with (thisform)
// {
// if (validateSelect(Item,"Must make a selection!")==false)
//   {Item.focus();return false}
// }
// }

function validateEmail(field,alerttxt) {
  apos = field.value.indexOf('@');
  dotpos = field.value.lastIndexOf('.');
  if (apos < 1 || dotpos - apos < 2) {
    alert(alerttxt);
    return false;
  } else {
    return true;
  }
}

// example of email
// function validateForm(thisform)
// {
// with (thisform)
// {
// if (validateEmail(email,"Not a valid e-mail address!")==false)
//   {email.focus();return false}
// }
// }

function validateDate(field,alerttxt) {
  re = /^(\d{4})-(\d{2})-(\d{2})$/;
  if (field.value === null || !field.value.match(re)) {
    alert(alerttxt);
    return false;
  } else {
    return true;
  }
}

function validateForm(thisform) {
  with (thisform) {
    if (validateRequired(firstName,
                        'Please enter a legal first name') === false) {
      firstName.focus();
      return false;
    }
    if (validateRequired(lastName,'Please enter a legal last name') === false) {
      lastName.focus();
      return false;
    }
    if (validateEmail(email1,
                      'Must have at least one valid Email address') === false) {
      email1.focus();
      return false;
    }
    if (validateRequired(phone1,
                         'Must have at least one phone number') === false) {
      phone1.focus();
      return false;
    }
    if (validateRequired(addressLine1,'Address cannot be blank') === false) {
      addressLine1.focus();
      return false;
    }
    if (validateRequired(city,'City cannot be blank') === false) {
      city.focus();
      return false;
    }
    if (validateRequired(zipCode,'Zip Code cannot be blank') === false) {
      zipCode.focus();
      return false;
    }
    if (validateSelect(country,'Please select a country') === false) {
      country.focus();
      return false;
    }
  }
  return true;
}
