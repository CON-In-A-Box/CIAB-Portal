/* jshint browser: true */
/* jshint -W097 */
/* globals */
/* exported buildTableTextCell, buildTableInputCell, buildTableSelectCell,
            buildTableCheckCell, buildTableNumericInputCell */

function buildTableTextCell(text) {
  var cell = document.createElement('DIV');
  cell.classList.add('UI-table-cell');
  cell.innerHTML = text;
  return cell;
}

function buildTableInputCell(id) {
  var cell = document.createElement('DIV');
  var input = document.createElement('INPUT');
  cell.classList.add('UI-table-cell');
  input.classList.add('UI-input');
  input.id = id;
  cell.appendChild(input);
  return cell;
}

function buildTableSelectCell(id, populate) {
  var cell = document.createElement('DIV');
  var input = document.createElement('SELECT');
  cell.classList.add('UI-table-cell');
  input.classList.add('UI-select');
  input.id = id;
  if (populate) {
    populate(input);
  }
  cell.appendChild(input);
  return cell;
}

function buildTableCheckCell(id, func) {
  var cell = document.createElement('DIV');
  var input = document.createElement('INPUT');
  input.setAttribute('type', 'checkbox');
  cell.classList.add('UI-table-cell');
  input.classList.add('UI-checkbox');
  input.id = id;
  input.setAttribute('onclick', func);
  cell.appendChild(input);
  return cell;
}

function buildTableNumericInputCell(id, validate) {
  var cell = document.createElement('DIV');
  var input = document.createElement('INPUT');
  cell.classList.add('UI-table-cell');
  input.classList.add('UI-input');
  input.setAttribute('type', 'number');
  input.setAttribute('min', '1');
  input.setAttribute('onchange', validate);
  input.id = id;
  cell.appendChild(input);
  return cell;
}
