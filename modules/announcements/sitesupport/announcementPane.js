/*
 * Javacript for the Announcements Main Panel
 */

/* jshint browser: true */
/* jshint -W097 */
/* globals apiRequest */

var announcementPane = (function(options) {
  'use strict';

  var settings = Object.assign(
    {
    }, options);

  return {
    options: function(opts) {
      settings = Object.assign(settings, opts);
    },

    displayAnnouncements: function(frame) {
      var target = document.getElementById('announcement_pane');
      if (frame !== null) {
        target.appendChild(frame[0]);
      }
    },

    addAnnouncement: function(table, data2) {
      var line = document.createElement('DIV');
      line.classList.add('UI-table-row');
      line.classList.add('UI-white');
      var f = document.createElement('DIV');
      f.classList.add('UI-table-cell');
      f.classList.add('UI-announcement');
      f.id = 'announcement-' + data2.id + '-note';

      var a = document.createElement('DIV');
      a.appendChild(document.createTextNode(data2.text));
      var s = document.createElement('SPAN');
      s.classList.add('UI-announcement-source');
      s.appendChild(document.createTextNode('  -  ' + data2.department.name));
      a.appendChild(s);
      s.appendChild(document.createTextNode(' (' + data2.postedOn + ')'));
      a.appendChild(s);
      f.appendChild(a);
      line.appendChild(f);
      table.appendChild(line);
      return 0;
    },

    emptyAnnouncementBlock: function() {
      var block = document.createElement('DIV');
      block.id = 'announcement-block';
      block.classList.add('UI-container');
      block.classList.add('event-color-primary');
      var title = document.createElement('div');
      title.classList.add('event-color-primary');
      title.classList.add('UI-center');
      var txt = document.createElement('B');
      txt.classList.add('UI-bold');
      txt.style.fontSize = '125%';
      txt.appendChild(document.createTextNode('Announcements'));
      title.appendChild(txt);
      block.appendChild(title);
      var contents = document.createElement('DIV');
      contents.classList.add('UI-container');
      block.appendChild(contents);
      var table = document.createElement('DIV');
      table.classList.add('UI-table');
      table.classList.add('UI-table-padded');
      contents.appendChild(table);
      return [block, table];
    },

    buildAnnouncementBlock: function(frame, result, data) {
      var rc = frame[0];
      var table = rc[1];
      return announcementPane.addAnnouncement(table, data);
    },

    load: function() {
      var frame = [ announcementPane.emptyAnnouncementBlock() ];
      apiRequest('GET',
        'member/current/announcements',
        'maxResults=all')
        .then(function(response) {
          var target = document.getElementById('announcement_pane');
          var result = JSON.parse(response.responseText);
          if (result.data.length > 0) {
            result.data.forEach(function(data) {
              announcementPane.buildAnnouncementBlock(frame, result, data);
            });
            var line = document.createElement('DIV');
            line.classList.add('event-color-primary');
            line.appendChild(document.createTextNode('\u00A0'));
            frame[0][0].appendChild(line);
            target.classList.remove('UI-hide');
          } else {
            target.classList.add('UI-hide');
          }
        });
      announcementPane.displayAnnouncements(frame[0]);
    }
  };
})();

if (window.addEventListener) {
  window.addEventListener('load', announcementPane.load);
} else {
  window.attachEvent('onload', announcementPane.load);
}
