/*
 * Validation functions
 */

/* jshint browser: true */
/* jshint -W097 */
/* global alertbox */
/* exported validateRequired */

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
