/*
 * Javacript for the Dropdown sections
 */

/* jshint browser: true */
/* jshint -W097 */

function expandSection(id) {
  var x = [document.getElementById(id)];
  if (x[0] === null) {
    x = document.getElementsByClassName(id);
  }
  if (x === null || x.length === 0) {
    x = document.getElementsByName(id);
  }
  if (x === null || x.length === 0) {
    return;
  }
  var y = document.getElementById(id + '_arrow');
  for (var i = 0; i < x.length; i++) {
    if (x[i].className.indexOf('w3-show') == -1) {
      x[i].className += ' w3-show';
      y.className = 'fa fa-caret-up';
    } else {
      x[i].className = x[i].className.replace(' w3-show', '');
      y.className = 'fa fa-caret-down';
    }
  }

}
