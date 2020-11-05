/*
 * Base function to lookup artshow piece
 */

/* jshint browser: true */
/* globals escapeHtml, apiRequest */

var lookupPiece = (function(options) {
  'use strict';

  var settings = Object.assign(
    {
      urlTag : 'pieceId',
      memberName: 'lookupPiece_id',
      message: 'Art Show Piece ID',
      success: _lookupSuccess,
      fail: _lookupFailed,
      handler: null,
      needForm: true
    }, options);

  function _lookupSuccess(target, response) {
    document.getElementById('lookupPiece_message').innerHTML =
      escapeHtml('Found ' + response['Name']);
    if (target) {
      lookupPiece.gotoTarget(target.href, response);
    }
  }

  function _lookupFailed(target, response, user, code) {
    lookupPiece.markFailure();
    if (code == 400) {
      document.getElementById('lookupPiece_message').innerHTML =
          user + ' invalid lookup.';
    }
    else if (code == 404) {
      document.getElementById('lookupPiece_message').innerHTML =
          user + ' not found.';
    }
  }

  return {
    options: function(opts) {
      settings = Object.assign(settings, opts);
    },

    gotoTarget: function(origin, item) {
      if (settings.handler !== null) {
        settings.handler(origin, item);
        return;
      }
      var newTarget = '';
      var i = origin.indexOf(settings.urlTag + '=');
      var id = null;
      if (item) {
        id = item.Id;
      }
      if (id) {
        if (i != -1) {
          var regexp = new RegExp('(' + settings.urlTag + '=).*?($)');
          newTarget = origin.replace(regexp, '$1' + id);
        } else {
          newTarget = origin + '&' + settings.urlTag + '=' + id;
        }
      } else {
        if (i != -1) {
          i--;
          newTarget = origin.substring(0, i);
        }
      }
      window.location = newTarget;
    },

    markFailure: function() {
      document.getElementById('lookupPiece_id').classList.add(
        'UI-red');
      document.getElementById('lookupPiece_spinner').innerHTML = '';
    },

    clearFailure: function() {
      document.getElementById('lookupPiece_id').classList.remove(
        'UI-red');
      document.getElementById('lookupPiece_spinner').innerHTML = '';
    },

    clear: function() {
      document.getElementById('lookupPiece_id').classList.remove(
        'UI-red');
      document.getElementById('lookupPiece_spinner').innerHTML = '';
      document.getElementById('lookupPiece_id').value = '';
      document.getElementById('lookupPiece_message').innerHTML = '';
    },

    changed: function(obj, target) {
      lookupPiece.lookupId(obj, target);
    },

    lookupId: function(obj, target) {
      var id = obj.value;
      if (id) {
        var res = id.split(':');
        document.getElementById('lookupPiece_spinner').innerHTML =
              '<i class=\'fas fa-spinner UI-spin\'></i>';
        document.getElementById('lookupPiece_message').innerHTML = '';
        var url = 'artshow/art/piece/' + res[0];
        if (res.length > 1) {
          url += '/' + res[1];
        }

        apiRequest('GET', url, 'include=ArtistID')
          .then(function(response) {
            var data = JSON.parse(response.responseText);
            lookupPiece.clearFailure();
            settings.success(target, data);
          })
          .catch(function(response) {
            if (response instanceof Error) { throw response; }
            var data = JSON.parse(response.responseText);
            settings.fail(target, data, id, response.status);
          });
      } else {
        if (target) {
          lookupPiece.gotoTarget(target.href, null);
        }
      }
    },

    keydown: function(keyCode, obj, target) {
      if (keyCode == 13) {
        lookupPiece.lookupId(obj, target);
        return false;
      }
      return true;
    },

    set: function(value) {
      document.getElementById('lookupPiece_id').value = value;
    },

    build: function() {
      var dom = document.getElementById('lookupPiece_div');
      var div = document.createElement('DIV');
      dom.appendChild(div);
      if (settings.needForm) {
        var form = document.createElement('FORM');
        div.appendChild(form);
        form.method = 'post';
        div = document.createElement('DIV');
        div.classList.add('UI-bar');
        form.appendChild(div);
      } else {
        var divI = document.createElement('DIV');
        divI.classList.add('UI-bar');
        div.appendChild(divI);
      }
      var div2 = document.createElement('DIV');
      div2.classList.add('UI-bar-item');
      div.appendChild(div2);
      var label = document.createElement('LABEL');
      label.classList.add('UI-padding');
      label.classList.add('UI-bar-item');
      label.innerHTML = settings.message;
      div2.appendChild(label);
      var div3 = document.createElement('DIV');
      div3.classList.add('UI-bar');
      label.classList.add('UI-bar-item');
      div2.appendChild(div3);
      var input = document.createElement('INPUT');
      div3.appendChild(input);
      input.classList.add('UI-input');
      input.classList.add('UI-bar-item');
      input.classList.add('UI-padding');
      input.name = 'MemberID';
      input.id = 'lookupPiece_id';
      input.name = settings.memberName;
      input.setAttribute('onchange',
        'lookupPiece.changed(this, location)');
      input.setAttribute('onkeydown',
        'return lookupPiece.keydown(event.keyCode, this, location)');
      input.placeholder = '';
      input.required = true;
      if (dom.getAttribute('data-user')) {
        input.value = dom.getAttribute('data-user');
      }
      var button = document.createElement('BUTTON');
      div3.appendChild(button);
      button.type = 'button';
      button.classList.add('icon-barcode');
      button.classList.add('button-scan');
      button.classList.add('UI-lookup-user-button');
      button.setAttribute('onclick',
        'QuaggaApp.init("lookupPiece_id", lookupPiece.lookupId, false)');
      var span = document.createElement('SPAN');
      div3.appendChild(span);
      span.classList.add('UI-bar-item');
      span.id = 'lookupPiece_spinner';
      span = document.createElement('SPAN');
      div3.appendChild(span);
      span.classList.add('UI-bar-item');
      span.id = 'lookupPiece_message';
    }

  };
}) ();

if (window.addEventListener) {
  window.addEventListener('load', lookupPiece.build);
} else {
  window.attachEvent('onload', lookupPiece.build);
}
