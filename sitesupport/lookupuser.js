/*
 * Base function to lookup users
 */

/* jshint browser: true */
/* globals escapeHtml */

var userLookup = (function(options) {
  'use strict';

  var settings = Object.assign(
    {
      urlTag : 'memberId',
      memberName: 'userLookup_member',
      message: 'Member Badge Number, E-Mail or Full Name',
      success: _lookupSuccess,
      fail: _lookupFailed,
      handler: null,
      needForm: true,
      lookupTarget: 'index.php?Function=functions&lookupId=',
      badgeName: true,
      partialMatch: false,
    }, options);

  function _lookupSuccess(target, response) {
    if (response.length == 1) {
      document.getElementById('userLookup_message').innerHTML =
          escapeHtml('Found ' +
          response[0]['First Name'] + ' ' +
          response[0]['Last Name']);
    } else if (response.length > 1) {
      var e = document.getElementById('userLookup_dropdown');
      while (e.hasChildNodes()) {
        e.removeChild(e.firstChild);
      }
      response.forEach(function(item) {
        var div = document.createElement('DIV');
        div.classList.add('UI-bar-item');
        div.classList.add('UI-button');
        if (target) {
          div.setAttribute('onclick',
            'userLookup.gotoTarget("' + target.href + '", ' +
              JSON.stringify(item) + ');');
        }
        div.innerHTML = '<span>' + item.Id + ' : ' +
            item['First Name'] + ' ' + item['Last Name'] +
            ' (' + item.Email + ')</span>';
        e.appendChild(div);
      });
      e.classList.remove('UI-hide');
      return;
    }
    if (target) {
      userLookup.gotoTarget(target.href, response[0]);
    }
  }

  function _lookupFailed(target, response, user, code) {
    userLookup.markFailure();
    if (code == 400) {
      document.getElementById('userLookup_message').innerHTML =
          user + ' invalid lookup.';
    }
    else if (code == 404) {
      document.getElementById('userLookup_message').innerHTML =
          user + ' not found.';
    }
    else if (code == 409) {
      document.getElementById('userLookup_message').innerHTML =
          user + ' has too many matches.';
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
      var uid = null;
      if (item) {
        uid = item.Id;
      }
      if (uid) {
        if (i != -1) {
          var regexp = new RegExp('(' + settings.urlTag + '=).*?($)');
          newTarget = origin.replace(regexp, '$1' + uid);
        } else {
          newTarget = origin + '&' + settings.urlTag + '=' + uid;
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
      document.getElementById('userLookup_member').classList.add(
        'UI-red');
      document.getElementById('userLookup_spinner').innerHTML = '';
    },

    clearFailure: function() {
      document.getElementById('userLookup_member').classList.remove(
        'UI-red');
      document.getElementById('userLookup_spinner').innerHTML = '';
    },

    clear: function() {
      document.getElementById('userLookup_member').classList.remove(
        'UI-red');
      document.getElementById('userLookup_spinner').innerHTML = '';
      document.getElementById('userLookup_member').value = '';
    },

    changed: function(obj, target) {
      var e = document.getElementById('userLookup_dropdown');
      if (!(e.offsetWidth > 0 && e.offsetHeight > 0)) {
        userLookup.lookupId(obj, target);
      }
    },

    lookupId: function(obj, target) {
      var id = obj.value;
      if (id) {
        var xhttp = new XMLHttpRequest();
        document.getElementById('userLookup_spinner').innerHTML =
              '<i class=\'fas fa-spinner UI-spin\'></i>';
        document.getElementById('userLookup_message').innerHTML = '';
        xhttp.onreadystatechange = function() {
          var response = JSON.parse(this.responseText);
          if (this.readyState == 4 && this.status == 200) {
            userLookup.clearFailure();
            settings.success(target, response);
          } else if (this.readyState == 4) {
            settings.fail(target, response, id, this.status);
          }
        };
        var url = settings.lookupTarget + id;
        if (settings.badgeName) {
          url += '&useBadgeName=1';
        }
        if (settings.partialMatch) {
          url += '&partialMatch=1';
        }
        xhttp.open('GET', url, true);
        xhttp.send();
      } else {
        if (target) {
          userLookup.gotoTarget(target.href, null);
        }
      }
    },

    keydown: function(keyCode, obj, target) {
      var e = document.getElementById('userLookup_dropdown');
      if (!e.classList.contains('UI-hide')) {
        e.classList.add('UI-hide');
      }
      if (keyCode == 13) {
        userLookup.lookupId(obj, target);
        return false;
      }
      return true;
    },

    set: function(value) {
      document.getElementById('userLookup_member').value = value;
    },

    build: function() {
      var dom = document.getElementById('userLookup_div');
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
      input.id = 'userLookup_member';
      input.name = settings.memberName;
      input.setAttribute('onchange',
        'userLookup.changed(this, location)');
      input.setAttribute('onkeydown',
        'return userLookup.keydown(event.keyCode, this, location)');
      input.placeholder = '(badge #, email, Name)';
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
        'QuaggaApp.init("userLookup_member", userLookup.lookupId)');
      var span = document.createElement('SPAN');
      div3.appendChild(span);
      span.classList.add('UI-bar-item');
      span.id = 'userLookup_spinner';
      span = document.createElement('SPAN');
      div3.appendChild(span);
      span.classList.add('UI-bar-item');
      span.id = 'userLookup_message';
      var drop = document.createElement('DIV');
      div3.appendChild(drop);
      drop.classList.add('UI-hide');
      drop.classList.add('UI-lookup-user-dropdown');
      drop.id = 'userLookup_dropdown';
    }

  };
}) ();

if (window.addEventListener) {
  window.addEventListener('load', userLookup.build);
} else {
  window.attachEvent('onload', userLookup.build);
}
