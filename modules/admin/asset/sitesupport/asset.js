/* jshint browser: true */
/* jshint -W097 */
/* globals  basicBackendRequest, apiRequest, showSpinner, hideSpinner */
/* exported  uploadAsset, startAssetUpload, populateAssetConfiguration,
             setAssetField */

'use strict';

function uploadAsset(id, file) {
  var data = file[0];
  var formData = new FormData();
  formData.append('AssetID', id);
  formData.append('AssetData', data);
  basicBackendRequest('POST', 'admin/asset', formData,
    function() {
      location.reload();
    });
}

function startAssetUpload(asset) {
  var upload = document.getElementById('assetToUpload.' + asset);
  upload.click();
}

function setAssetField(field) {
  var value = document.getElementById('config_' + field).value;
  showSpinner();
  apiRequest('PUT','admin/configuration',
    'Field=' + field + '&Value=' + value)
    .then(function() {
      hideSpinner();
    })
    .catch(function(response) {
      hideSpinner();
      if (response instanceof Error) { throw response; }
    });
}

function createTableCell() {
  var cell = document.createElement('DIV');
  cell.classList.add('UI-table-cell');
  cell.classList.add('UI-padding');
  return cell;
}

function populateAssetConfiguration() {
  basicBackendRequest('GET', 'admin/asset', 'configuration=1',
    function(request) {
      var base = document.getElementById('config_table');
      base.innerHTML = '';
      var output = JSON.parse(request.responseText);
      for (var property in output) {
        var line = document.createElement('DIV');
        line.classList.add('UI-table-row');
        line.classList.add('UI-padding');
        var value = createTableCell();
        value.innerHTML = property;
        line.appendChild(value);
        value = createTableCell();
        value.innerHTML = output[property].description;
        line.appendChild(value);

        if (output[property].source == 'ENV' || output[property].readonly) {
          value = createTableCell();
          value.innerHTML = output[property].value;
          line.appendChild(value);

          if (!output[property].readonly) {
            value = createTableCell();
            value.innerHTML = '&lt;ENVIRONMENT&gt;';
            line.appendChild(value);
          } else {
            value = createTableCell();
            value.innerHTML = '';
            line.appendChild(value);
          }
        } else {
          value = createTableCell();
          var edit = document.createElement('INPUT');
          edit.setAttribute('type', 'text');
          edit.id = 'config_' + property;
          edit.value = output[property].value;
          edit.classList.add('UI-input');
          value.appendChild(edit);
          line.appendChild(value);

          value = createTableCell();
          var button = document.createElement('BUTTON');
          button.classList.add('UI-eventbutton');
          button.setAttribute('onclick', 'setAssetField("' + property + '");');
          button.innerHTML = 'Set';
          value.appendChild(button);
          line.appendChild(value);
        }

        base.appendChild(line);

        if (property == 'ASSET_TYPES') {
          var upload = document.getElementById('assetToUpload.org-icon');
          upload.setAttribute('accept', output[property].value);
        }
      }
    });

}
