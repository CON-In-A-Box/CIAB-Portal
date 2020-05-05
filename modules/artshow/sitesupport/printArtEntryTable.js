/*
 * Javacript for the Print Art Table
 */

/* jshint browser: true */
/* jshint -W097 */
/* globals apiRequest, buildTableTextCell, buildTableInputCell,
           buildTableSelectCell, buildTableNumericInputCell,
           alertbox */

var printArtEntryTable = (function(options) {
  'use strict';

  var configuration;
  var artist;

  var settings = Object.assign(
    {
      debugElement: 'headline_section',
      element: 'new_printshop_list',
      printArtEntryCount: 10
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

    load: function(config, who) {
      configuration = config;
      artist = who;
      document.getElementById(settings.element).innerHTML = '';
      printArtEntryTable.buildPrintPieceTableHeader();
      for (var i = 1; i <= settings.printArtEntryCount; i++) {
        var entryBlock = printArtEntryTable.buildAddPrintArtTableLine(i);
        document.getElementById(settings.element).appendChild(entryBlock);
      }
    },

    buildPrintPieceTableHeader: function() {
      var table = document.getElementById('new_printshop_list');
      var row = document.createElement('DIV');
      row.classList.add('ARTSHOW-modal-header-row');
      row.appendChild(buildTableTextCell('Lot<br>Entry'));
      row.appendChild(buildTableTextCell('Name'));
      row.appendChild(buildTableTextCell('Type'));
      row.appendChild(buildTableTextCell('Quantity'));
      row.appendChild(buildTableTextCell('Price'));
      table.appendChild(row);
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

    validatePrice: function(element) {
      var e = document.getElementById(element);
      e.value = Math.floor(e.value);
      if (e.value <= 0) {
        e.value = 1;
      }
    },

    buildAddPrintArtTableLine: function(index) {
      var row = document.createElement('DIV');
      row.classList.add('UI-table-row');
      row.id = 'print_enter_row_' + index;
      row.appendChild(buildTableTextCell(index));
      row.appendChild(buildTableInputCell('print_enter_name_' + index));
      row.appendChild(buildTableSelectCell(
        'print_enter_type_' + index, printArtEntryTable.populateArtTypeSelect));

      row.appendChild(buildTableNumericInputCell(
        'print_enter_quantity_' + index,
        'printArtEntryTable.validatePrice(\'print_enter_quantity_' + index +
        '\');'
      ));
      row.appendChild(buildTableNumericInputCell(
        'print_enter_price_' + index,
        'printArtEntryTable.validatePrice(\'print_enter_price_' + index + '\');'
      ));

      return row;
    },

    sendPrintArt: function(index) {
      var element = document.getElementById('print_enter_name_' + index);
      var name = null;
      if (element) {
        name = element.value;
      }
      if (name) {
        var type = document.getElementById('print_enter_type_' + index).value;
        var quantity = document.getElementById('print_enter_quantity_' +
            index).value;
        var price = document.getElementById('print_enter_price_' + index).value;

        var param = 'Name=' + name + '&PieceType=' + type + '&Quantity=' +
                    quantity + '&Price=' + price;

        return apiRequest('POST', 'artshow/artist/' + artist + '/print', param);
      }
    },

    validateArt: function(index) {
      var blank = 0;
      var element = document.getElementById('print_enter_name_' + index);
      if (element) {
        var name = element.value;
        if (name == null || name == '') {blank |= 1 << 0;}
        var q = document.getElementById('print_enter_quantity_' + index).value;
        if (q == null || q == '') {blank |= 1 << 1;}
        var p = document.getElementById('print_enter_price_' + index).value;
        if (p == null || p == '') {blank |= 1 << 2;}
        var offset = 3;
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
      if (invalid & 1 << 0) {error.push('Name');}
      if (invalid & 1 << 1) {error.push('Quantity');}
      if (invalid & 1 << 2) {error.push('Price');}
      alertbox('Entry ' + index + ' is missing: ' + error.join(', '));
    },

    submitArt: function() {
      return new Promise(function(resolve, reject) {
        for (var i = 0; i < settings.printArtEntryCount; i++) {
          var invalid = printArtEntryTable.validateArt(i);
          if (invalid == null) {
            continue;
          }
          if (invalid)
          {
            printArtEntryTable.reportArtProblem(i, invalid);
            reject();
            return;
          }
        }
        var promises = Array();
        for (i = 0; i < settings.printArtEntryCount; i++) {
          promises.push(printArtEntryTable.sendPrintArt(i));
        }

        Promise.all(promises).then(function(response) {
          printArtEntryTable.debugmsg(response.responseText);
          resolve();
        })
          .catch(function(response) {
            if (response instanceof Error) { throw response; }
            printArtEntryTable.debugmsg(response.responseText);
            reject();
          });
      });
    }

  };

})();
