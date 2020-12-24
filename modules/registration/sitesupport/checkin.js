/*
 * Javacript for the Registration badge handling page
 */

/* jshint browser: true */
/* jshint -W097 */
/* globals apiRequest, showSpinner, hideSpinner, confirmbox, alertbox */

var checkinPage = (function(options) {
  'use strict';

  var settings = Object.assign(
    {
      'debug': true
    }, options);

  var isOpen = false;
  var pendingCheckin = null;
  var readyCheckin = null;
  var configuration;

  return {
    options: function(opts) {
      settings = Object.assign(settings, opts);
    },

    emailMe: function(ticket) {
      showSpinner();
      apiRequest('PUT', 'registration/ticket/' + ticket.id + '/email','')
        .then(function() {
          hideSpinner();
          alertbox('Email sent', 'We have emailed you your boarding pass.<br>' +
                   'Kindly check your email for further instructions.');
        })
        .catch(function(response) {
          hideSpinner();
          if (response instanceof Error) { throw response; }
        })
    },

    pickupBadge: function(ticket) {
      showSpinner();
      apiRequest('PUT', 'registration/ticket/' + ticket.id + '/pickup','')
        .then(function(response) {
          location.reload();
          hideSpinner();
          console.log(response);
        })
        .catch(function(response) {
          hideSpinner();
          if (response instanceof Error) { throw response; }
        })
    },

    lostBadge: function(ticket) {
      showSpinner();
      apiRequest('PUT', 'registration/ticket/' + ticket.id + '/lost','')
        .then(function() {
          hideSpinner();
          alertbox('Badge has been re-printed!',
            'Please visit registration to claim your new badge.');
        })
        .catch(function(response) {
          hideSpinner();
          if (response instanceof Error) { throw response; }
        })
    },

    doCheckin: function() {
      confirmbox('Would you like to proceed with On-Line check In?')
        .then(function() {
          showSpinner();
          readyCheckin.forEach(function(ticket) {
            apiRequest('PUT', 'registration/ticket/' + ticket.id +
                              '/checkin','')
              .then(function(response) {
                hideSpinner();
                location.reload();
                console.log(response);
              })
              .catch(function(response) {
                hideSpinner();
                if (response instanceof Error) { throw response; }
              })
          })
        })
        .catch(function(response) {
          if (response instanceof Error) { throw response; }
        });
    },

    checkinOnOff: function() {
      var a = document.getElementsByName('checking-checkbox');
      var active = false;
      readyCheckin = [];
      a.forEach(function(e, i) {
        if (e.checked) {
          readyCheckin.push(pendingCheckin[i]);
          active = true;
        }
      });
      if (active) {
        document.getElementById('checkinbutton').classList.remove('UI-disabled')
      } else {
        document.getElementById('checkinbutton').classList.add('UI-disabled');
      }
    },

    createCard: function() {
      var e = document.createElement('DIV');
      e.classList.add('UI-border');
      e.classList.add('UI-center');
      e.classList.add('UI-container');
      e.classList.add('UI-padding');
      e.classList.add('UI-margin');
      return e;
    },

    displayCheckIn: function(ticket) {
      var e = checkinPage.createCard();
      var n = document.createElement('DIV');
      e.append(n);
      n.classList.add('UI-container');
      n.classList.add('UI-padding');
      var s = document.createElement('SPAN');
      s.classList.add('UI-padding');
      n.append(s);
      s.innerHTML = 'Badge&nbsp;#' + ticket.member.id + '&nbsp;-&nbsp;' +
        ticket.ticketType.Name;
      var br = document.createElement('BR');
      n.append(br);
      s = document.createElement('SPAN');
      s.classList.add('UI-padding');
      n.append(s);
      s.innerHTML = 'Badge Name ' + ticket.badgeName;
      br = document.createElement('BR');
      n.append(br);
      var l = document.createElement('LABEL');
      n.append(l);
      l.innerHTML = 'Check In&nbsp;';
      var cb = document.createElement('INPUT');
      cb.classList.add('UI-checkbox');
      cb.type = 'checkbox';
      cb.id = 'checkbox-' + ticket.id;
      cb.name = 'checking-checkbox';
      cb.onclick = checkinPage.checkinOnOff;
      n.append(cb);
      return e;
    },

    displayBoardingPass: function(ticket) {
      var e = checkinPage.createCard();
      var n = document.createElement('DIV');
      e.append(n);
      n.classList.add('UI-container');
      n.classList.add('UI-padding');
      var s = document.createElement('SPAN');
      s.classList.add('UI-padding');
      s.classList.add('UI-table-heading');
      n.append(s);
      s.innerHTML = '<h3>Boarding Pass</h3>';
      s = document.createElement('SPAN');
      s.classList.add('UI-padding');
      n.append(s);
      s.innerHTML = 'Badge&nbsp;#' + ticket.member.id + '&nbsp;-&nbsp;' +
        ticket.ticketType.Name;
      var br = document.createElement('BR');
      n.append(br);
      s = document.createElement('SPAN');
      s.classList.add('UI-padding');
      n.append(s);
      s.innerHTML = ticket.member.legalFirstName + '&nbsp;' +
                    ticket.member.legalLastName
      br = document.createElement('BR');
      n.append(br);
      var b = document.createElement('BUTTON');
      n.append(b);
      b.classList.add('UI-eventbutton');
      b.classList.add('UI-margin');
      b.innerHTML = 'Email Me';
      b.onclick = function() {
        confirmbox(
          'Are you sure you want to email this boarding pass to yourself?')
          .then(function() {
            checkinPage.emailMe(ticket);
          })
          .catch(function(response) {
            if (response instanceof Error) { throw response; }
          });
      };
      br = document.createElement('BR');
      n.append(br);
      b = document.createElement('BUTTON');
      n.append(b);
      b.classList.add('UI-eventbutton');
      b.classList.add('UI-margin');
      b.innerHTML = 'Badge Picked Up';
      b.onclick = function() {
        confirmbox(
          'Do you physically have your badge in your hand?')
          .then(function() {
            checkinPage.pickupBadge(ticket);
          })
          .catch(function(response) {
            if (response instanceof Error) { throw response; }
          });
      };
      return e;
    },

    displayLostBadge: function(ticket) {
      var e = checkinPage.createCard();
      var n = document.createElement('DIV');
      e.append(n);
      n.classList.add('UI-container');
      n.classList.add('UI-padding');
      var s = document.createElement('SPAN');
      s.classList.add('UI-padding');
      s.classList.add('UI-table-heading');
      n.append(s);
      s.innerHTML = '<h3>Report Lost Badge</h3>';
      s = document.createElement('SPAN');
      s.classList.add('UI-padding');
      n.append(s);
      s.innerHTML = 'Badge&nbsp;#' + ticket.member.id + '&nbsp;-&nbsp;' +
        ticket.ticketType.Name;
      var br = document.createElement('BR');
      n.append(br);
      s = document.createElement('SPAN');
      s.classList.add('UI-padding');
      n.append(s);
      s.innerHTML = ticket.member.legalFirstName + '&nbsp;' +
                    ticket.member.legalLastName
      br = document.createElement('BR');
      n.append(br);
      var b = document.createElement('BUTTON');
      n.append(b);
      b.classList.add('UI-eventbutton');
      b.classList.add('UI-margin');
      b.innerHTML = 'Report Lost';
      b.onclick = function() {
        confirmbox('Are you sure you want to report this badge as lost?<br>' +
                 '<b>There may be a fee involved in reprinting your badge.</b>')
          .then(function() {
            checkinPage.lostBadge(ticket);
          })
          .catch(function(response) {
            if (response instanceof Error) { throw response; }
          })
      };
      br = document.createElement('BR');
      return e;
    },

    handleTicket: function(ticket) {
      if (ticket.boardingPassGenerated == null) {
        var p = document.getElementById('checkin');
        pendingCheckin.push(ticket);
        p.append(checkinPage.displayCheckIn(ticket));
        p.classList.remove('UI-hide');
        document.getElementById('checkin_button').classList.remove('UI-hide');
      } else if (!(ticket.badgesPickedUp == 1)) {
        p = document.getElementById('pickup');
        p.append(checkinPage.displayBoardingPass(ticket));
        p.classList.remove('UI-hide');
      } else {
        p = document.getElementById('lost');
        p.append(checkinPage.displayLostBadge(ticket));
        p.classList.remove('UI-hide');
      }
    },

    processBadges: function() {
      if ('badgeNotice' in configuration &&
          configuration['badgeNotice'].value.length > 0) {
        var e = document.getElementById('instructions');
        e.classList.remove('UI-hide');
        e.innerHTML = configuration['badgeNotice'].value;
      }
      apiRequest('GET', 'registration/ticket/list',
        'maxResults=all&include=ticketType,member,registeredBy,event')
        .then(function(response) {
          var d = JSON.parse(response.responseText);
          if (Array.isArray(d.data)) {
            d.data.forEach(checkinPage.handleTicket);
          } else {
            checkinPage.handleTicket(d);
          }
          hideSpinner();
        })
        .catch(function(response) {
          hideSpinner();
          if (response instanceof Error) { throw response; }
        })
    },

    loadConfig: function() {
      if (isOpen) {
        apiRequest('GET', 'registration/configuration', 'maxResults=all')
          .then(function(response) {
            var v = JSON.parse(response.responseText).data;
            configuration = Array();
            v.forEach(function(e) {
              configuration[e.field] = e;
            });
            checkinPage.processBadges();
          })
          .catch(function(response) {
            hideSpinner();
            if (response instanceof Error) { throw response; }
          });
      } else {
        hideSpinner();
      }
    },

    load: function() {
      pendingCheckin = [];
      showSpinner();
      apiRequest('GET', 'registration/open', null)
        .then(function(response) {
          var result = JSON.parse(response.responseText);
          var e = document.getElementById('regopen');
          isOpen = result.open;
          if (result.open) {
            e.innerHTML = 'Open';
            e.classList.remove('UI-red');
            e.classList.add('UI-green');
          } else {
            e.innerHTML = 'Closed';
            e.classList.add('UI-red');
            e.classList.remove('UI-green');
          }
          checkinPage.loadConfig();
        })
        .catch(function(response) {
          hideSpinner();
          if (response instanceof Error) { throw response; }
          var e = document.getElementById('regopen');
          e.innerHTML = 'Closed';
          e.classList.add('UI-red');
          e.classList.remove('UI-green');
        });
    },

  };
})();

if (window.addEventListener) {
  window.addEventListener('load', checkinPage.load);
} else {
  window.attachEvent('onload', checkinPage.load);
}
