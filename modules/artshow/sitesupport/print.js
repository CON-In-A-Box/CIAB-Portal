/*
 * Javacript for the Artshow Piece Details
 */

/* jshint browser: true */
/* jshint -W097 */
/* globals apiRequest */

var artshowPrint = (function(options) {
  'use strict';

  var configuration;
  var data;

  var settings = Object.assign(
    {
      debugElement: 'headline_section',
      readonly: false,
    }, options);

  return {
    options: function(opts) {
      settings = Object.assign(settings, opts);
    },

    debugmsg: function(message) {
      if (settings.debugElement) {
        var target = document.getElementById(settings.debugElement);
        target.classList.add('UI-show');
        target.innerHTML = message;
      }
    },

    load: function(config, printShop, id, eventId) {
      configuration = config;
      return new Promise(function(resolve, reject) {
        var uri = 'artshow/print/' + id;

        if (eventId) {
          uri += '/' + eventId;
        }
        uri += '?include=ArtistID,AccountID';

        apiRequest('GET', uri, '')
          .then(function(response) {
            artshowPrint.debugmsg(response.responseText);
            var json = JSON.parse(response.responseText);
            artshowPrint.displayArt(json);
            resolve();
          })
          .catch(function(response) {
            if (response instanceof Error) { throw response; }
            artshowPrint.debugmsg(response.responseText);
            reject();
          });
      });
    },

    displayArt: function(input) {
      data = input;
      document.getElementById('print_id').value = data.PieceID;
      document.getElementById('print_name').value = data.Name;
      if (data.ArtistID.CompanyNameOnSheet != '0') {
        document.getElementById('print_artist').value =
              data.ArtistID.CompanyName;
      } else {
        document.getElementById('print_artist').value =
              data.ArtistID.AccountID.firstName +
              ' ' + data.ArtistID.AccountID.lastName;
      }
      document.getElementById('print_type').value = data.PieceType;
      document.getElementById('print_quantity').value = data.Quantity;
      document.getElementById('print_price').value = data.Price;
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

    buildForm: function(config, element) {
      configuration = config;
      var form = document.getElementById(element);
      form.innerHTML = '';

      var readonly = '';
      if (settings.readonly) {
        readonly = 'disabled';
      }

      var template = `
    <input class="UI-hide" id="print_id" value='' readonly>
    <label class="UI-label" for="print_name">Piece Name:</label>
    <input class="UI-input" id="print_name" value='' ${readonly}>
    <label class="UI-label" for="print_artist">Artist:</label>
    <input class="UI-input" id="print_artist" value='' disabled>
    <label class="UI-label" for="print_type">Type:</label>
    <select class="UI-select" id="print_type" ${readonly}>
    </select>
    <label class="UI-label" for="print_quantity">Quantity:</label>
    <input class="UI-input" id="print_quantity" value='1' type='number' min='1'
        ${readonly} onchange='artshowPrint.validate(this);'>
    <label class="UI-label" for="print_price">Price:</label>
    <input class="UI-input" id="print_price" value='1' type='number' min='1'
        ${readonly} onchange='artshowPrint.validate(this);'>
      `;

      var div = document.createElement('DIV');
      div.classList.add('UI-container');
      div.classList.add('UI-margin');
      div.insertAdjacentHTML('beforeend', template);
      form.appendChild(div);
      artshowPrint.populateArtTypeSelect(document.getElementById('print_type'));
    },

    validate: function(e) {
      e.value = Math.floor(e.value);
      if (e.value < 1) {
        e.value = 1;
      }
    },

    getPiece: function() {
      var output = {};
      output['PieceID'] = document.getElementById('print_id').value;
      output['EventID'] = data.EventID;
      output['ArtistID'] = data.ArtistID.ArtistID;
      output['Name'] =  document.getElementById('print_name').value;
      output['PieceType'] = document.getElementById('print_type').value;
      output['Quantity'] = document.getElementById('print_quantity').value;
      output['Price'] = document.getElementById('print_price').value;

      return output;
    },

    savePiece: function() {
      return new Promise(function(resolve, reject) {
        var object = artshowPrint.getPiece();
        var param = '';
        for (var key in object) {
          if (param != '') {
            param += '&';
          }
          param += key + '=' + encodeURIComponent(object[key]);
        }
        apiRequest('PUT', 'artshow/print/' + object.PieceID, param)
          .then(function(response) {
            artshowPrint.debugmsg(response.responseText);
            resolve(response);
          })
          .catch(function(response) {
            if (response instanceof Error) { throw response; }
            artshowPrint.debugmsg(response.responseText);
            reject(response);
          })
      });
    },

  };
})();
