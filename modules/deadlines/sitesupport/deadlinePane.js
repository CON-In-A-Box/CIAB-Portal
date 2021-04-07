/*
 * Javacript for the Deadlines Main Panel
 */

/* jshint browser: true */
/* jshint -W097 */
/* globals apiRequest */

var deadlinePane = (function(options) {
  'use strict';

  var settings = Object.assign(
    {
    }, options);

  return {
    options: function(opts) {
      settings = Object.assign(settings, opts);
    },

    displayDeadlines: function(frame) {
      var target = document.getElementById('deadline_pane');
      if (frame !== null) {
        target.appendChild(frame[0]);
      }
    },

    addDeadline: function(table, data2) {
      var line = document.createElement('DIV');
      line.classList.add('UI-table-row');
      var t = data2.deadline.split(/[- :]/);
      var now = new Date(Date.now());
      var date = new Date(t[0], t[1] - 1, t[2]);
      var end = new Date(t[0], t[1] - 1, t[2]);
      end.setDate(end.getDate() + 1);
      var warn = new Date(t[0], t[1] - 1, t[2]);
      warn.setDate(warn.getDate() - 30);
      if (now > end) {
        line.classList.add('UI-red');
      } else if (now >= warn) {
        line.classList.add('UI-yellow');
      }
      else {
        return 1;
      }
      var f = document.createElement('DIV');
      f.classList.add('UI-table-cell');
      f.id = 'deadline-' + data2.id + '-date';
      f.appendChild(
        document.createTextNode(date.toDateString())
      );
      line.appendChild(f);
      f = document.createElement('DIV');
      f.classList.add('UI-table-cell');
      f.id = 'deadline-' + data2.id + '-department';
      f.appendChild(document.createTextNode(data2.department.name));
      line.appendChild(f);
      f = document.createElement('DIV');
      f.classList.add('UI-table-cell');
      f.id = 'deadline-' + data2.id + '-note';
      f.appendChild(document.createTextNode(data2.note));
      line.appendChild(f);
      table.appendChild(line);
      return 0;
    },

    generateTableHeader: function() {
      var line = document.createElement('DIV');
      line.id = 'deadline-table-header';
      line.classList.add('UI-table-row');
      line.classList.add('UI-white');
      var f = document.createElement('DIV');
      f.classList.add('UI-table-cell');
      f.appendChild(document.createTextNode('Date'));
      line.appendChild(f);
      f = document.createElement('DIV');
      f.classList.add('UI-table-cell');
      f.appendChild(document.createTextNode('Department'));
      line.appendChild(f);
      f = document.createElement('DIV');
      f.classList.add('UI-table-cell');
      f.appendChild(document.createTextNode('Deadline'));
      line.appendChild(f);
      return line;
    },

    emptyDeadlineBlock: function() {
      var block = document.createElement('DIV');
      block.id = 'deadline-block';
      block.classList.add('UI-container');
      block.classList.add('event-color-primary');
      var title = document.createElement('div');
      title.classList.add('event-color-primary');
      title.classList.add('UI-center');
      var txt = document.createElement('B');
      txt.classList.add('UI-bold');
      txt.style.fontSize = '125%';
      txt.appendChild(document.createTextNode('Deadlines (Next 30 days) '));
      title.appendChild(txt);
      var i = document.createElement('I');
      i.id = 'deadline_sync';
      i.classList.add('fas');
      i.classList.add('fa-sync');
      i.classList.add('UI-spin');
      title.appendChild(i);
      block.appendChild(title);
      var contents = document.createElement('DIV');
      contents.classList.add('UI-container');
      block.appendChild(contents);
      var table = document.createElement('DIV');
      table.classList.add('UI-table');
      table.classList.add('UI-table-padded');
      table.classList.add('UI-table-heading');
      contents.appendChild(table);
      var line = deadlinePane.generateTableHeader();
      table.appendChild(line);
      return [block, table];
    },

    buildDeadlineBlock: function(frame, result, data) {
      var rc = frame[0];
      var table = rc[1];
      return deadlinePane.addDeadline(table, data);
    },

    addMissing: function(frame, count) {
      if (count > 0) {
        var line = document.createElement('DIV');
        var text = document.createElement('SPAN');
        line.classList.add('UI-center');
        line.classList.add('UI-bold');
        line.appendChild(document.createTextNode('+' +
            count + ' more deadlines'));
        line.appendChild(text);
        frame[0].appendChild(line);
      }
    },

    load: function() {
      var frame = [ deadlinePane.emptyDeadlineBlock() ];
      apiRequest('GET',
        'member/current/deadlines',
        'max_results=all')
        .then(function(response) {
          var result = JSON.parse(response.responseText);
          if (result.data.length > 0) {
            var count = 0;
            result.data.forEach(function(data) {
              count += deadlinePane.buildDeadlineBlock(frame, result, data);
            });
            deadlinePane.addMissing(frame[0], count);
          } else {
            var target = document.getElementById('deadline_pane');
            target.classList.add('UI-hide');
          }
        })
        .finally(function() {
          document.getElementById('deadline_sync').classList.add('UI-hide');
        });
      deadlinePane.displayDeadlines(frame[0]);
    }
  };
})();

if (window.addEventListener) {
  window.addEventListener('load', deadlinePane.load);
} else {
  window.attachEvent('onload', deadlinePane.load);
}
