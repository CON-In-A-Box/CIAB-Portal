/* jshint esversion: 6 */
/* globals Vue, apiRequest, confirmbox, systemDebug, settingsTable, CIABEventSource */

var app = Vue.createApp({
  created() {
    apiRequest('GET', 'artshow/', 'max_results=all')
      .then((response) => {
        this.configuration = JSON.parse(response.responseText);
        this.configLoaded();
      });
  },
  data() {
    return {
      configuration: {
        paymenttype: {},
        piecetype: {},
        returnmethod: {},
        pricetype: {},
        registrationquestion: {}
      },
      debug: systemDebug,
      debugMessage: null,
    }
  },
  methods: {
    configLoaded() {
      new settingsTable({api: 'artshow/configuration'}).createElement();
      delete this.configuration.paymenttype['type'];
      delete this.configuration.piecetype['type'];
      delete this.configuration.returnmethod['type'];
      delete this.configuration.pricetype['type'];
      delete this.configuration.registrationquestion['type'];
    },

    updateSetting(element) {
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
        .then(response => {
          this.debugmessage = response.responseText;
        })
        .catch((response) => {
          if (response instanceof Error) { throw response; }
          this.debugmessage = response.responseText;
        });
    },

    updateList(uri, original, updated, msgFragment, targetParam) {
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
      var param = targetParam + '=' + encodeURI(target);
      msg += target + '"?';
      confirmbox(title, msg)
        .then(() => {
          apiRequest(method, uri, param)
            .then((response) => {
              this.debugMessage = response.responseText;
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
            .catch((response) => {
              if (response instanceof Error) { throw response; }
              this.debugMessage = response.responseText;
            });
        });
    },

    removeList(uri, original, updated, msgFragment) {
      var sel = document.getElementById(original);
      var type = sel.options[sel.selectedIndex].text;
      if (type != null && type != '') {
        confirmbox(
          'Remove ' + msgFragment,
          'Are you sure you want to permenantly delete the ' +
             msgFragment + ' "' + type + '"?')
          .then(() => {
            apiRequest('DELETE',
              uri + '/' + encodeURI(type),
              null)
              .then((response) => {
                this.debugMessage = response.responseText;
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
              .catch((response) => {
                if (response instanceof Error) { throw response; }
                this.debugMessage = response.responseText;
              });
          });
      }
    },

    updatePayment() {
      this.updateList('artshow/configuration/paymenttype',
        'PaymentType', 'new_PaymentType', 'Payment Type', 'payment');
    },

    removePayment() {
      this.removeList('artshow/configuration/paymenttype',
        'PaymentType', 'new_PaymentType', 'Payment Type');
    },

    paymentChange() {
      document.getElementById('new_PaymentType').value =
        document.getElementById('PaymentType').value;
    },

    updatePiece() {
      this.updateList('artshow/configuration/piecetype',
        'PieceType', 'new_PieceType', 'Piece Type', 'piece');
    },

    removePiece() {
      this.removeList('artshow/configuration/piecetype',
        'PieceType', 'new_PieceType', 'Piece Type');
    },

    pieceChange() {
      document.getElementById('new_PieceType').value =
        document.getElementById('PieceType').value;
    },

    updateReturn() {
      this.updateList('artshow/configuration/returnmethod',
        'ReturnMethod', 'new_ReturnMethod', 'Return Method', 'method');
    },

    removeReturn() {
      this.removeList('artshow/configuration/returnmethod',
        'ReturnMethod', 'new_ReturnMethod', 'Return Method');
    },

    returnChange() {
      document.getElementById('new_ReturnMethod').value =
        document.getElementById('ReturnMethod').value;
    },

    updatePrice() {
      var uri = 'artshow/configuration/pricetype';
      var original = 'PriceType';
      var updated = 'new_PriceType';
      var msgFragment = 'Price Type';

      var method = 'PUT';
      var sel = document.getElementById(original);
      var type = sel.options[sel.selectedIndex].text;
      var target = document.getElementById(updated).value;
      var visible = (document.getElementById('PriceUser').checked ? '1' : '0');
      var fixed = (document.getElementById('PriceFixed').checked ? '1' : '0');
      var value = '{"price":"' + target + '","artist_set":"' + visible + '","fixed":"' + fixed + '"}';
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
      var param = 'price=' + encodeURI(target) + '&artist_set=' + visible;
      msg += target + '"?';
      confirmbox(title, msg)
        .then(() => {
          apiRequest(method, uri, param)
            .then((response) => {
              this.debugMessage = response.responseText;
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
            .catch((response) => {
              if (response instanceof Error) { throw response; }
              this.debugMessage = response.responseText;
            });
        });
    },

    removePrice() {
      this.removeList('artshow/configuration/pricetype',
        'PriceType', 'new_PriceType', 'Price');
    },

    priceChange() {
      if (document.getElementById('PriceType').value) {
        var data = JSON.parse(document.getElementById('PriceType').value);
        document.getElementById('new_PriceType').value = data.price;
        document.getElementById('PriceUser').checked = (data.artist_set == '1');
        document.getElementById('PriceFixed').checked = (data.fixed == '1');
      } else {
        document.getElementById('new_PriceType').value = '';
        document.getElementById('PriceUser').checked = true;
        document.getElementById('PriceFixed').checked = false;
      }
    },

    updateRegistrationQuestion() {
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
      var value = '{"text":"' + target + '","boolean":"' + isBool +
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
        uri += '/' + data.id;
      }
      var param = 'text=' + encodeURI(target) + '&boolean=' + isBool;
      msg += target + '"?';
      confirmbox(title, msg)
        .then(() => {
          apiRequest(method, uri, param)
            .then((response) => {
              this.debugMessage = response.responseText;
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
            .catch((response) => {
              if (response instanceof Error) { throw response; }
              this.debugMessage = response.responseText;
            });
        });
    },

    removeRegistrationQuestion() {
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
          .then(() => {
            apiRequest('DELETE',
              uri + '/' + data.id,
              null)
              .then((response) => {
                this.debugMessage = response.responseText;
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
              .catch((response) => {
                if (response instanceof Error) { throw response; }
                this.debugMessage = response.responseText;
              });
          });
      }
    },

    questionChange() {
      if (document.getElementById('RegistrationQuestion').value) {
        var data = JSON.parse(document.getElementById(
          'RegistrationQuestion').value);
        document.getElementById('new_RegistrationQuestion').value = data.text;
        document.getElementById('QuestionIsBoolean').checked =
            (data.boolean == '1');
      } else {
        document.getElementById('new_RegistrationQuestion').value = '';
        document.getElementById('QuestionIsBoolean').checked = true;
      }
    },

    demoBidTag() {
      const evtSource = new CIABEventSource(
        'api/artshow/art/tag/demo');
      evtSource.onmessage = (event) => {
        if (event.lastEventId == 'END') {
          evtSource.close();
          var file = evtSource.b64toBlob(event.data, 'application/pdf');
          var fileURL = URL.createObjectURL(file);
          window.open(fileURL);
        }
      }
    }
  }

});

app.mount('#page');

export default app;
