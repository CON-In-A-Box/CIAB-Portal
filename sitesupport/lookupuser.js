/*
 * Base function to lookup users
 */

/* jshint browser: true */

var userLookup = (function(options) {
    'use strict';

    var settings = Object.assign(
        {
          urlTag : 'memberId',
          memberName: 'userLookup_member',
          message: 'Member Badge Number, E-Mail or Full Name',
          success: _lookupSuccess,
          needForm: true,
          lookupTarget: 'index.php?Function=functions&lookupId=',
          badgeName: true,
        }, options);

    function _gotoTarget(origin, uid) {
      var newTarget = '';
      var i = origin.indexOf(settings.urlTag + '=');
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
    }

    function _lookupSuccess(target, response) {
      var uid = response[0].Id;
      document.getElementById('userLookup_message').innerHTML =
        escapeHtml('Found ' +
        response[0]['First Name'] + ' ' +
        response[0]['Last Name']);
      if (target) {
        _gotoTarget(target.href, uid);
      }
    }

    return {
        options: function(opts) {
          settings = Object.assign(settings, opts);
        },

        markFailure: function() {
          document.getElementById('userLookup_member').classList.add(
            'w3-red');
          document.getElementById('userLookup_spinner').innerHTML = '';
        },

        clearFailure: function() {
          document.getElementById('userLookup_member').classList.remove(
            'w3-red');
          document.getElementById('userLookup_spinner').innerHTML = '';
        },

        clear: function() {
          document.getElementById('userLookup_member').classList.remove(
            'w3-red');
          document.getElementById('userLookup_spinner').innerHTML = '';
          document.getElementById('userLookup_member').value = '';
        },

        lookupId: function(obj, target) {
          var id = obj.value;
          if (id) {
            var xhttp = new XMLHttpRequest();
            document.getElementById('userLookup_spinner').innerHTML =
              '<i class=\'fa fa-spinner w3-spin\'></i>';
            document.getElementById('userLookup_message').innerHTML = '';
            xhttp.onreadystatechange = function() {
              if (this.readyState == 4 && this.status == 200) {
                userLookup.clearFailure();
                var response = JSON.parse(this.responseText);
                settings.success(target, response);
              } else if (this.readyState == 4) {
                userLookup.markFailure();
                if (this.status == 400) {
                  document.getElementById('userLookup_message').innerHTML =
                    id + ' invalid lookup.';
                }
                else if (this.status == 404) {
                  document.getElementById('userLookup_message').innerHTML =
                    id + ' not found.';
                }
                else if (this.status == 409) {
                  document.getElementById('userLookup_message').innerHTML =
                    id + ' has too many matches.';
                }
              }
            };
            var url = settings.lookupTarget + id;
            if (settings.badgeName) {
              url += '&useBadgeName=1';
            }
            xhttp.open('GET', url, true);
            xhttp.send();
          } else {
            if (target) {
              _gotoTarget(target.href, null);
            }
          }
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
            div.classList.add('w3-bar');
            form.appendChild(div);
          } else {
            var divI = document.createElement('DIV');
            divI.classList.add('w3-bar');
            div.appendChild(divI);
          }
          var div2 = document.createElement('DIV');
          div2.classList.add('w3-bar-item');
          div.appendChild(div2);
          var label = document.createElement('LABEL');
          label.classList.add('w3-padding');
          label.classList.add('w3-bar-item');
          label.innerHTML = settings.message;
          div2.appendChild(label);
          var div3 = document.createElement('DIV');
          div3.classList.add('w3-bar');
          label.classList.add('w3-bar-item');
          div2.appendChild(div3);
          var input = document.createElement('INPUT');
          div3.appendChild(input);
          input.classList.add('w3-input');
          input.classList.add('w3-border');
          input.classList.add('w3-bar-item');
          input.classList.add('w3-padding');
          input.name = 'MemberID';
          input.id = 'userLookup_member';
          input.name = settings.memberName;
          input.setAttribute('onchange',
            'userLookup.lookupId(this, location)');
          input.placeholder = '(badge #, email, Name)';
          input.required = true;
          if (dom.getAttribute('data-user')) {
            input.value = dom.getAttribute('data-user');
          }
          var button = document.createElement('BUTTON');
          div3.appendChild(button);
          button.type = 'button';
          button.classList.add('w3-button');
          button.classList.add('w3-bar-item');
          button.classList.add('w3-border');
          button.classList.add('icon-barcode');
          button.classList.add('button-scan');
          button.setAttribute('onclick',
            'QuaggaApp.init("userLookup_member", userLookup.lookupId)');
          var span = document.createElement('SPAN');
          div3.appendChild(span);
          span.classList.add('w3-bar-item');
          span.id = 'userLookup_spinner';
          span = document.createElement('SPAN');
          div3.appendChild(span);
          span.classList.add('w3-bar-item');
          span.id = 'userLookup_message';
        }

      };
  }) ();

if (window.addEventListener) {
  window.addEventListener('load', userLookup.build);
} else {
  window.attachEvent('onload', userLookup.build);
}
