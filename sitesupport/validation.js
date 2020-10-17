/*
 * Validation functions
 */

/* jshint browser: true */
/* jshint -W097 */
/* global alertbox */
/* exported validateRequired, validateSelect, validateEmail, validateDate,
            validateForm  */

function validateRequired(field,alerttxt) {
  if (field.value === null || field.value === '') {
    alertbox(alerttxt);
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
    alertbox(alerttxt);return false;
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
  var apos = field.value.indexOf('@');
  var dotpos = field.value.lastIndexOf('.');
  if (apos < 1 || dotpos - apos < 2) {
    alertbox(alerttxt);
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
  var re = /^(\d{4})-(\d{2})-(\d{2})$/;
  if (field.value === null || !field.value.match(re)) {
    alertbox(alerttxt);
    return false;
  } else {
    return true;
  }
}

function validateForm(thisform) {
  if (validateRequired(thisform.firstName,
    'Please enter a legal first name') === false) {
    thisform.firstName.focus();
    return false;
  }
  if (validateRequired(thisform.lastName,
    'Please enter a legal last name') === false) {
    thisform.lastName.focus();
    return false;
  }
  if (validateEmail(thisform.email1,
    'Must have at least one valid Email address') === false) {
    thisform.email1.focus();
    return false;
  }
  return true;
}
