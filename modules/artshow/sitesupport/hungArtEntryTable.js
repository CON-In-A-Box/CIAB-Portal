/*
 * Javacript for the Hung Art Table
 */

/* jshint browser: true */
/* jshint -W097 */
/* globals apiRequest, buildTableTextCell, buildTableInputCell,
           buildTableSelectCell, buildTableCheckCell,
           buildTableNumericInputCell, alertbox */

var hungArtEntryTable = (function(options) {
  'use strict';

  var configuration;
  var artist;

  var settings = Object.assign(
    {
      debugElement: 'headline_section',
      element: 'new_hung_list',
    }, options);

  return {
    options: function(opts) {
      settings = Object.assign(settings, opts);
    },

    debugmsg: function(message) {
      if (settings.debug) {
        var target = document.getElementById(settings.debugElement);
        target.classList.add('UI-show');
        target.innerHTML = message;
      }
    },

    load: function(config, who, max) {
      configuration = config;
      artist = who;

      document.getElementById(settings.element).innerHTML = '';
      hungArtEntryTable.buildArtShowEntryTable();
      for (var i = 0; i < max; i++) {
        var entryBlock = hungArtEntryTable.buildHungArtTableLine(i);
        document.getElementById(settings.element).appendChild(entryBlock);
      }
    },

    buildHungPieceEntryTableHeader: function() {
      var row = document.createElement('DIV');
      row.classList.add('ARTSHOW-modal-header-row');
      row.appendChild(buildTableTextCell('Entry'));
      row.appendChild(buildTableTextCell('Name'));
      row.appendChild(buildTableTextCell('Medium'));
      row.appendChild(buildTableTextCell('Edition'));
      row.appendChild(buildTableTextCell('Type'));
      row.appendChild(buildTableTextCell(
        'Not<br>For<br>Sale'));
      configuration.PriceType.value.forEach(function(data) {
        if (data.SetPrice == '1') {
          row.appendChild(buildTableTextCell(
            data.PriceType.replace(' ','<br>')));
        }
      });
      return row;
    },

    buildArtShowEntryTable: function() {
      var table = document.getElementById(settings.element);
      var header = hungArtEntryTable.buildHungPieceEntryTableHeader();
      table.appendChild(header);
    },

    buildHungArtTableLine: function(index) {
      var row = document.createElement('DIV');
      row.classList.add('UI-table-row');
      row.id = 'enter_row_' + index;
      row.appendChild(buildTableTextCell(index + 1));
      row.appendChild(buildTableInputCell('enter_name_' + index));
      row.appendChild(buildTableInputCell('enter_medium_' + index));
      row.appendChild(buildTableInputCell('enter_edition_' + index));
      row.appendChild(buildTableSelectCell(
        'enter_type_' + index, hungArtEntryTable.populateArtTypeSelect));

      row.appendChild(buildTableCheckCell(index +
        '_enter_nfs', 'hungArtEntryTable.nfsClick("' + index + '_enter");'));

      configuration.PriceType.value.forEach(function(data) {
        if (data.SetPrice == '1') {
          var id = index + '_enter_price_' + data.PriceType.replace(' ','_');
          row.appendChild(buildTableNumericInputCell(id,
            'hungArtEntryTable.validatePrice(\'' + id + '\');'
          ));
        }
      });

      return row;
    },

    sendHungArt: function(index) {
      var element = document.getElementById('enter_name_' + index);
      var name = null;
      if (element) {
        name = element.value;
      }
      if (name) {
        var medium = document.getElementById('enter_medium_' + index).value;
        var edition = document.getElementById('enter_edition_' + index).value;
        var type = document.getElementById('enter_type_' + index).value;
        var nfs = (document.getElementById(index + '_enter_nfs').checked ?
          '1' : '0');

        var param = 'Name=' + name + '&Medium=' + medium + '&Edition=' +
                    edition + '&PieceType=' + type + '&NFS=' + nfs;

        configuration.PriceType.value.forEach(function(data) {
          if (data.SetPrice == '1') {
            var id = index + '_enter_price_' + data.PriceType.replace(' ','_');
            var name = data.PriceType.replace(' ','%20');
            var value = document.getElementById(id).value;
            if (value !== null && value !== '') {
              param += '&' + name + '=' + document.getElementById(id).value;
            }
          }
        });

        return apiRequest('POST', 'artshow/artist/' + artist + '/art',
          param);
      }
    },

    populateArtTypeSelect: function(element) {
      configuration.PieceType.value.forEach(function(x, i) {
        var option = document.createElement('option');
        option.text = x;
        element.add(option);
        if (x == configuration['Artshow_DefaultType'].value) {
          element.selectedIndex = i;
        }
      });
    },

    validateArt: function(index) {
      var blank = 0;
      var element = document.getElementById('enter_name_' + index);
      if (element) {
        var name = element.value;
        if (name == null || name == '') {blank |= 1 << 0;}
        var medium = document.getElementById('enter_medium_' + index).value;
        if (medium == null || medium == '') {blank |= 1 << 1;}
        var edition = document.getElementById('enter_edition_' + index).value;
        if (edition == null || edition == '') {blank |= 1 << 2;}

        var offset = 3;
        if (!document.getElementById(index + '_enter_nfs').checked) {
          configuration.PriceType.value.forEach(function(data) {
            if (data.SetPrice == '1') {
              var id = index + '_enter_price_' +
                       data.PriceType.replace(' ','_');
              var price = document.getElementById(id).value;
              if (price == null || price == '' || price < 1) {
                blank |= 1 << offset;
              }
              offset++;
            }
          });
        }
        if (blank == ((1 << offset) - 1)) {
          return null;
        }
      } else {
        return null;
      }
      return blank;
    },

    reportArtProblem: function(index, invalid) {
      var error = [];
      var nfs = document.getElementById(index + '_enter_nfs').checked;
      if (invalid & 1 << 0) {error.push('Name');}
      if (invalid & 1 << 1) {error.push('Medium');}
      if (invalid & 1 << 2) {error.push('Edition');}
      if (!nfs && invalid >= 1 << 3) {error.push('Valid Prices');}
      alertbox('Entry ' + (index + 1) + ' is missing: ' + error.join(', '));
    },

    submitArt: function() {
      return new Promise(function(resolve, reject) {
        var max = parseInt(configuration['Artshow_DisplayLimit'].value);
        for (var i = 0; i < max; i++) {
          var invalid = hungArtEntryTable.validateArt(i);
          if (invalid == null) {
            continue;
          }
          if (invalid)
          {
            hungArtEntryTable.reportArtProblem(i, invalid);
            reject();
            return;
          }
        }
        var promises = Array();
        for (i = 0; i < max; i++) {
          promises.push(hungArtEntryTable.sendHungArt(i));
        }

        Promise.all(promises).then(function(response) {
          hungArtEntryTable.debugmsg(response.responseText);
          resolve();
        })
          .catch(function(response) {
            if (response instanceof Error) { throw response; }
            hungArtEntryTable.debugmsg(response.responseText);
            reject();
          });
      });
    },

    validatePrice: function(element) {
      var e = document.getElementById(element);
      e.value = Math.floor(e.value);
      if (e.value <= 0) {
        e.value = 1;
      }
    }

  };

})();
