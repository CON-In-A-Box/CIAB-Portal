/*
 * Javacript for the Announcements page
 */

/* jshint browser: true */
/* jshint -W097 */
/* globals apiRequest, showSpinner, hideSpinner, confirmbox, settingsTable
           */

var artshowAdminPage = (function(options) {
  'use strict';

  var settings = Object.assign(
    {
      'debug': true
    }, options);

  var configuration;

  return {
    options: function(opts) {
      settings = Object.assign(settings, opts);
    },

    debugmsg: function(message) {
      if (settings.debug) {
        var target = document.getElementById('headline_section');
        target.classList.add('UI-show');
        target.innerHTML = message;
      }
    },

    load: function() {

      new settingsTable({api: 'artshow/configuration'}).createElement();

      showSpinner();
      apiRequest('GET',
        'artshow/',
        'maxResults=all')
        .then(function(response) {
          artshowAdminPage.debugmsg(response.responseText);
          var result = JSON.parse(response.responseText);
          configuration = result.data;

          var PaymentType = document.getElementById('PaymentType');
          configuration.PaymentType.value.forEach(function(type) {
            var option = document.createElement('option');
            option.text = type;
            PaymentType.add(option);
          });
          PaymentType.size = configuration.PaymentType.value.length + 1;

          var PieceType = document.getElementById('PieceType');
          configuration.PieceType.value.forEach(function(type) {
            var option = document.createElement('option');
            option.text = type;
            PieceType.add(option);
          });
          PieceType.size = configuration.PieceType.value.length + 1;

          var ReturnMethod = document.getElementById('ReturnMethod');
          configuration.ReturnMethod.value.forEach(function(type) {
            var option = document.createElement('option');
            option.text = type;
            ReturnMethod.add(option);
          });
          ReturnMethod.size = configuration.ReturnMethod.value.length + 1;

          var data = [];
          configuration.PriceType.value.forEach(function(type) {
            data[type.Position] = type;
          });

          var PriceType = document.getElementById('PriceType');
          data.forEach(function(type) {
            var option = document.createElement('option');
            option.text = type.PriceType;
            option.value = JSON.stringify(type);
            PriceType.add(option);
          });
          PriceType.size = data.length;

          var RegQuestion = document.getElementById('RegistrationQuestion');
          configuration.RegistrationQuestion.value.forEach(function(type) {
            var option = document.createElement('option');
            option.text = type.Text;
            option.value = JSON.stringify(type);
            RegQuestion.add(option);
          });
          RegQuestion.size = configuration.RegistrationQuestion.value.length +
             1;
        })
        .catch(function(response) {
          if (response instanceof Error) { throw response; }
          artshowAdminPage.debugmsg(response.responseText);
        })
        .finally(function() {
          hideSpinner();
        });
    },

    updateSetting: function(element) {
      console.log('here');
      var id = element.id.replace(/_/g,' ');
      var type = element.type;
      var value;
      if (type == 'checkbox') {
        value = (element.checked ? '1' : '0');
      } else {
        value = element.value;
      }
      apiRequest('PUT',
        'artshow/configuration',
        'Field=' + id + '&Value=' + value)
        .then(function(response) {
          artshowAdminPage.debugmsg(response.responseText);
        })
        .catch(function(response) {
          if (response instanceof Error) { throw response; }
          artshowAdminPage.debugmsg(response.responseText);
        });
    },

    updateList: function(uri, original, updated, msgFragment) {
      var method = 'PUT';
      var type = document.getElementById(original).value;
      var target = document.getElementById(updated).value;
      var title = 'Update ' + msgFragment;
      var msg = 'Are you sure you want to change the ' + msgFragment + ' "';
      if (type == null || type == '') {
        method = 'POST';
        title = 'Add ' + msgFragment;
        msg = 'Are you sure you want to add the ' + msgFragment + ' "';
      } else {
        msg += type + '" to "';
        uri += '/' + encodeURI(type);
      }
      var param = 'Type=' + encodeURI(target);
      msg += target + '"?';
      confirmbox(title, msg)
        .then(function() {
          apiRequest(method, uri, param)
            .then(function(response) {
              artshowAdminPage.debugmsg(response.responseText);
              var OriginalValue = document.getElementById(original);

              if (method == 'POST') {
                var option = document.createElement('option');
                option.text = target;
                OriginalValue.add(option);
                OriginalValue.size = OriginalValue.size + 1;
              } else {
                for (var i = 0; i < OriginalValue.options.length; i++)
                {
                  if (OriginalValue.options[i].innerHTML == type) {
                    OriginalValue.options[i].innerHTML = target;
                    break;
                  }
                }
                document.getElementById(updated).value = target;
              }
            })
            .catch(function(response) {
              if (response instanceof Error) { throw response; }
              artshowAdminPage.debugmsg(response.responseText);
            });
        });
    },

    removeList: function(uri, original, updated, msgFragment) {
      var sel = document.getElementById(original);
      var type = sel.options[sel.selectedIndex].text;
      if (type != null && type != '') {
        confirmbox(
          'Remove ' + msgFragment,
          'Are you sure you want to permenantly delete the ' +
             msgFragment + ' "' + type + '"?')
          .then(function() {
            apiRequest('DELETE',
              uri + '/' + encodeURI(type),
              null)
              .then(function(response) {
                artshowAdminPage.debugmsg(response.responseText);
                var element = document.getElementById(original);
                for (var i = 0; i < element.options.length; i++)
                {
                  if (element.options[i].innerHTML == type) {
                    element.remove(i);
                    break;
                  }
                }
                document.getElementById(updated).value = null;
              })
              .catch(function(response) {
                if (response instanceof Error) { throw response; }
                artshowAdminPage.debugmsg(response.responseText);
              });
          });
      }
    },

    updatePayment: function() {
      artshowAdminPage.updateList('artshow/configuration/paymenttype',
        'PaymentType', 'new_PaymentType', 'Payment Type');
    },

    removePayment: function() {
      artshowAdminPage.removeList('artshow/configuration/paymenttype',
        'PaymentType', 'new_PaymentType', 'Payment Type');
    },

    paymentChange: function() {
      document.getElementById('new_PaymentType').value =
        document.getElementById('PaymentType').value;
    },

    updatePiece: function() {
      artshowAdminPage.updateList('artshow/configuration/piecetype',
        'PieceType', 'new_PieceType', 'Piece Type');
    },

    removePiece: function() {
      artshowAdminPage.removeList('artshow/configuration/piecetype',
        'PieceType', 'new_PieceType', 'Piece Type');
    },

    pieceChange: function() {
      document.getElementById('new_PieceType').value =
        document.getElementById('PieceType').value;
    },

    updateReturn: function() {
      artshowAdminPage.updateList('artshow/configuration/returnmethod',
        'ReturnMethod', 'new_ReturnMethod', 'Return Method');
    },

    removeReturn: function() {
      artshowAdminPage.removeList('artshow/configuration/returnmethod',
        'ReturnMethod', 'new_ReturnMethod', 'Return Method');
    },

    returnChange: function() {
      document.getElementById('new_ReturnMethod').value =
        document.getElementById('ReturnMethod').value;
    },

    updatePrice: function() {
      var uri = 'artshow/configuration/pricetype';
      var original = 'PriceType';
      var updated = 'new_PriceType';
      var msgFragment = 'Price Type';

      var method = 'PUT';
      var sel = document.getElementById(original);
      var type = sel.options[sel.selectedIndex].text;
      var target = document.getElementById(updated).value;
      var visible = (document.getElementById('PriceUser').checked ? '1' : '0');
      var value = '{"PriceType":"' + target + '","SetPrice":"' + visible + '"}';
      var title = 'Update ' + msgFragment;
      var msg = 'Are you sure you want to change the ' + msgFragment + ' "';
      if (type == '<New>') {
        method = 'POST';
        title = 'Add ' + msgFragment;
        msg = 'Are you sure you want to add the ' + msgFragment + ' "';
      } else {
        msg += type + '" to "';
        uri += '/' + encodeURI(type);
      }
      var param = 'PriceType=' + encodeURI(target) + '&SetPrice=' + visible;
      msg += target + '"?';
      confirmbox(title, msg)
        .then(function() {
          apiRequest(method, uri, param)
            .then(function(response) {
              artshowAdminPage.debugmsg(response.responseText);
              var OriginalValue = document.getElementById(original);

              if (method == 'POST') {
                var option = document.createElement('option');
                option.value = value;
                option.text = target;
                OriginalValue.add(option);
                OriginalValue.size = OriginalValue.size + 1;
              } else {
                for (var i = 0; i < OriginalValue.options.length; i++)
                {
                  if (OriginalValue.options[i].text == type) {
                    OriginalValue.options[i].innerHTML = target;
                    OriginalValue.options[i].value = value;
                    break;
                  }
                }
                document.getElementById(updated).value = target;
              }
            })
            .catch(function(response) {
              if (response instanceof Error) { throw response; }
              artshowAdminPage.debugmsg(response.responseText);
            });
        });
    },

    removePrice: function() {
      artshowAdminPage.removeList('artshow/configuration/pricetype',
        'PriceType', 'new_PriceType', 'Price');
    },

    priceChange: function() {
      if (document.getElementById('PriceType').value) {
        var data = JSON.parse(document.getElementById('PriceType').value);
        document.getElementById('new_PriceType').value = data.PriceType;
        document.getElementById('PriceUser').checked = (data.SetPrice == '1');
      } else {
        document.getElementById('new_PriceType').value = '';
        document.getElementById('PriceUser').checked = true;
      }
    },

    updateRegistrationQuestion: function() {
      var uri = 'artshow/configuration/registrationquestion';
      var original = 'RegistrationQuestion';
      var updated = 'new_RegistrationQuestion';
      var msgFragment = 'Registration Question';

      var method = 'PUT';
      var sel = document.getElementById(original);
      var type = sel.options[sel.selectedIndex].text;
      var target = document.getElementById(updated).value;
      var isBool = (document.getElementById('QuestionIsBoolean').checked ? '1' :
        '0');
      var value = '{"Text":"' + target + '","BooleanQuestion":"' + isBool +
         '"}';
      var title = 'Update ' + msgFragment;
      var msg = 'Are you sure you want to change the ' + msgFragment + ' "';

      if (type == '<New>') {
        method = 'POST';
        title = 'Add ' + msgFragment;
        msg = 'Are you sure you want to add the ' + msgFragment + ' "';
      } else {
        var data = JSON.parse(sel.value);
        msg += type + '" to "';
        uri += '/' + data.QuestionID;
      }
      var param = 'Text=' + encodeURI(target) + '&BooleanQuestion=' + isBool;
      msg += target + '"?';
      confirmbox(title, msg)
        .then(function() {
          apiRequest(method, uri, param)
            .then(function(response) {
              artshowAdminPage.debugmsg(response.responseText);
              var OriginalValue = document.getElementById(original);

              if (method == 'POST') {
                var option = document.createElement('option');
                option.value = value;
                option.text = target;
                OriginalValue.add(option);
                OriginalValue.size = OriginalValue.size + 1;
              } else {
                for (var i = 0; i < OriginalValue.options.length; i++)
                {
                  if (OriginalValue.options[i].text == type) {
                    OriginalValue.options[i].innerHTML = target;
                    break;
                  }
                }
                document.getElementById(updated).value = target;
              }
            })
            .catch(function(response) {
              if (response instanceof Error) { throw response; }
              artshowAdminPage.debugmsg(response.responseText);
            });
        });
    },

    removeRegistrationQuestion: function() {
      var uri = 'artshow/configuration/registrationquestion';
      var original = 'RegistrationQuestion';
      var updated = 'new_RegistrationQuestion';
      var msgFragment = 'Registration Question';

      var sel = document.getElementById(original);
      var type = sel.options[sel.selectedIndex].text;
      var data = JSON.parse(sel.value);
      if (type != null && type != '') {
        confirmbox(
          'Remove ' + msgFragment,
          'Are you sure you want to permenantly delete the ' +
             msgFragment + ' "' + type + '"?')
          .then(function() {
            apiRequest('DELETE',
              uri + '/' + data.QuestionID,
              null)
              .then(function(response) {
                artshowAdminPage.debugmsg(response.responseText);
                var element = document.getElementById(original);
                for (var i = 0; i < element.options.length; i++)
                {
                  if (element.options[i].innerHTML == type) {
                    element.remove(i);
                    break;
                  }
                }
                document.getElementById(updated).value = null;
              })
              .catch(function(response) {
                if (response instanceof Error) { throw response; }
                artshowAdminPage.debugmsg(response.responseText);
              });
          });
      }
    },

    questionChange: function() {
      if (document.getElementById('RegistrationQuestion').value) {
        var data = JSON.parse(document.getElementById(
          'RegistrationQuestion').value);
        document.getElementById('new_RegistrationQuestion').value = data.Text;
        document.getElementById('QuestionIsBoolean').checked =
            (data.BooleanQuestion == '1');
      } else {
        document.getElementById('new_RegistrationQuestion').value = '';
        document.getElementById('QuestionIsBoolean').checked = true;
      }
    },

    demoBidTag: function() {
      apiRequest('GET',
        'artshow/art/tag/demo',
        '', true)
        .then(function(response) {
          var file = new Blob([ response.response ],
            { type: 'application/pdf' });
          var fileURL = URL.createObjectURL(file);
          window.open(fileURL);
        });
    }
  };
})();

if (window.addEventListener) {
  window.addEventListener('load', artshowAdminPage.load);
} else {
  window.attachEvent('onload', artshowAdminPage.load);
}
