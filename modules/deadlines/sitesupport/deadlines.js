/*
 * Javacript for the Deadlines page
 */

/* jshint browser: true */
/* jshint -W097 */
/* globals apiRequest, showSpinner, hideSpinner, showSidebar,
           confirmbox, alertbox, departmentDropdown */

var deadlinePage = (function(options) {
  'use strict';

  var permissions = null;

  var settings = Object.assign(
    {
    }, options);

  return {
    options: function(opts) {
      settings = Object.assign(settings, opts);
    },

    openEdit: function() {
      var dataset = this.dataset;
      var id = dataset.ciabDeadlineId;
      var date = dataset.ciabDeadlineDate;
      var note = document.getElementById('deadline-' + id + '-note').innerHTML;
      var deadlineDate = document.getElementById('deadline_date');

      deadlineDate.value = date;
      document.getElementById('deadline_note').value = note;
      document.getElementById('department_dropdown_select')
        .value = dataset.ciabDeadlineDepartment;

      var canEdit = Object.prototype.hasOwnProperty.call(permissions,
        dataset.ciabDeadlineDepartment + '_deadline_put');
      var canDelete = Object.prototype.hasOwnProperty.call(permissions,
        dataset.ciabDeadlineDepartment + '_deadline_delete');

      var button = document.getElementById('remove_event_button');
      if (canDelete) {
        button.classList.remove('UI-hide');
        button.dataset.ciabDeadlineId = id;
      } else {
        button.classList.add('UI-hide');
      }
      button = document.getElementById('save_event_button');
      if (canEdit) {
        button.classList.remove('UI-hide');
        button.dataset.ciabDeadlineId = id;
      } else {
        button.classList.add('UI-hide');
      }

      document.getElementById('modify_title').innerHTML = 'Modify';
      showSidebar('modify_deadline');
    },

    removeEvent: function(button) {
      var dataset = button.dataset;
      var id = dataset.ciabDeadlineId;
      confirmbox('Remove Deadline', 'Confirm removal of this deadline').then(
        function() {
          showSpinner();
          apiRequest('DELETE', 'deadline/' + id, '')
            .then(function() {
              location.reload();
            })
            .catch(function(response) {
              var data = JSON.parse(response.responseText);
              hideSpinner();
              alertbox('Delete Failed', data.status);
            });
        });
    },

    updateEvent: function(button) {
      var dataset = button.dataset;
      var id = dataset.ciabDeadlineId;
      var date = document.getElementById('deadline_date').value;
      var note = document.getElementById('deadline_note').value;
      var dept = document.getElementById('department_dropdown_select').value;
      confirmbox('Update Deadline', 'Confirm update of this deadline').then(
        function() {
          showSpinner();
          var method = 'POST';
          if (id == -1) {
            method = 'PUT';
            id = dept;
          }
          apiRequest(method, 'deadline/' + id,
            'Deadline=' + date + '&Note=' + encodeURI(note) + '&Department=' +
            dept)
            .then(function() {
              location.reload();
            })
            .catch(function(response) {
              var data = JSON.parse(response.responseText);
              hideSpinner();
              alertbox('Update Failed', data.status);
            });
        });
    },

    displayDeadlines: function(cache) {
      var target = document.getElementById('headline_section');
      cache.forEach(function(data) {
        if (data !== null) {
          target.appendChild(data[0]);
        }
      });
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
      } else if (now > warn) {
        line.classList.add('UI-yellow');
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
      f.id = 'deadline-' + data2.id + '-note';
      f.appendChild(document.createTextNode(data2.note));
      line.appendChild(f);
      f = document.createElement('DIV');
      f.setAttribute('name', 'deadline-table-modify-' + data2.departmentId.id);
      f.classList.add('UI-table-cell');
      f.classList.add('UI-center');
      f.classList.add('UI-hide');
      var button = document.createElement('button');
      button.innerHTML = '<i class="fas fa-calendar-check"></i>';
      button.dataset.ciabDeadlineId = data2.id;
      button.dataset.ciabDeadlineDepartment = data2.departmentId.id;
      button.dataset.ciabDeadlineDate = t[0] + '-' + t[1] + '-' + t[2];
      button.onclick = deadlinePage.openEdit;
      button.classList.add('UI-button');
      f.appendChild(button);
      line.appendChild(f);
      table.appendChild(line);
    },

    generateTableHeader: function(dept) {
      var line = document.createElement('DIV');
      line.id = 'deadline-table-header-' + dept.id;
      line.classList.add('UI-table-row');
      var f = document.createElement('DIV');
      f.classList.add('UI-table-cell');
      f.appendChild(document.createTextNode('Date'));
      line.appendChild(f);
      f = document.createElement('DIV');
      f.classList.add('UI-table-cell');
      f.appendChild(document.createTextNode('Deadline'));
      line.appendChild(f);
      f = document.createElement('DIV');
      f.setAttribute('name', 'deadline-table-modify-' + dept.id);
      f.classList.add('UI-table-cell');
      f.classList.add('UI-hide');
      f.classList.add('UI-center');
      f.appendChild(document.createTextNode(''));
      line.appendChild(f);
      return line;
    },

    newDeadline: function() {
      var dataset = this.dataset;
      document.getElementById('deadline_note').value = '';
      if (dataset.ciabDeadlineDepartment) {
        document.getElementById('department_dropdown_select')
          .value = dataset.ciabDeadlineDepartment;
      } else {
        document.getElementById('department_dropdown_select')
          .selectedIndex = 0;
      }
      var button = document.getElementById('remove_event_button');
      button.classList.add('UI-hide');
      button = document.getElementById('save_event_button');
      button.classList.remove('UI-hide');
      button.dataset.ciabDeadlineId = -1;
      var date = new Date();
      document.getElementById('deadline_date').value =
        date.getFullYear().toString() + '-' +
        (date.getMonth() + 1).toString().padStart(2, 0) + '-' +
        (date.getDate() + 1).toString().padStart(2, 0);

      document.getElementById('modify_title').innerHTML = 'Add';
      showSidebar('modify_deadline');
    },

    emptyDeadlineBlock: function(dept) {
      var block = document.createElement('DIV');
      block.id = 'deadline-block-' + dept.id;
      block.classList.add('UI-container');
      var title = document.createElement('H2');
      title.classList.add('UI-secondary-sectionbar');
      title.appendChild(document.createTextNode(dept.name));
      var button = document.createElement('button');
      button.innerHTML = '<i class="fas fa-calendar-plus"></i>';
      title.appendChild(button);
      button.classList.add('UI-button');
      button.classList.add('UI-right');
      button.classList.add('UI-hide');
      button.dataset.ciabDeadlineDepartment = dept.id;
      button.onclick = deadlinePage.newDeadline;
      block.appendChild(title);
      var contents = document.createElement('DIV');
      contents.classList.add('UI-container');
      block.appendChild(contents);
      var table = document.createElement('DIV');
      table.classList.add('UI-table-all');
      contents.appendChild(table);
      var line = deadlinePage.generateTableHeader(dept);
      table.appendChild(line);
      return [block, table];
    },

    buildDeadlineBlock: function(cache, result, data) {
      var table;
      var rc;
      var dept = cache[parseInt(data.departmentId.id)];
      if (!dept) {
        rc = deadlinePage.emptyDeadlineBlock(data.departmentId);
        table = rc[1];
        cache[parseInt(data.departmentId.id)] = rc;
      } else {
        rc = cache[parseInt(data.departmentId.id)];
        table = rc[1];
      }
      deadlinePage.addDeadline(table, data);
    },

    filter: function(dept) {
      var id = dept.id.id.toString() + '_deadline_put';
      return Object.prototype.hasOwnProperty.call(permissions, id);
    },

    loadDeadlines: function() {
      showSpinner();
      apiRequest('GET',
        'member/deadlines/',
        'maxResults=all&include=departmentId')
        .then(function(response) {
          var result = JSON.parse(response.responseText);
          if (result.data.length > 0) {
            var cache = Array();
            result.data.forEach(function(data) {
              deadlinePage.buildDeadlineBlock(cache, result, data);
            });
            deadlinePage.displayDeadlines(cache);
          }
          apiRequest('GET',
            'permissions/method/deadline',
            'maxResults=all')
            .then(function(response) {
              result = JSON.parse(response.responseText);
              var havePut = false;
              permissions = {};
              if (result.data.length > 0) {
                result.data.forEach(function(data) {
                  if (data.allowed) {
                    permissions[data.subdata.departmentId + '_' +
                                  data.subtype] = data;
                    if (data.subtype == 'deadline_put') {
                      var sect = 'deadline-block-' + data.subdata.departmentId;
                      var block = document.getElementById(sect);
                      if (block !== null) {
                        var button = block.getElementsByTagName('button');
                        if (button !== null) {
                          button[0].classList.remove('UI-hide');
                        }
                      }
                      if (!havePut) {
                        havePut = true;
                        var add = document.getElementById(
                          'deadline-sectionbar-add');
                        add.classList.remove('UI-hide');
                        add.onclick = deadlinePage.newDeadline;
                      }
                    }
                    if (data.subtype == 'deadline_delete' ||
                          data.subtype == 'deadline_post') {
                      var line = 'deadline-table-modify-' +
                            data.subdata.departmentId;
                      var cells = document.getElementsByName(line);
                      cells.forEach(function(cell) {
                        cell.classList.remove('UI-hide');
                      });
                    }
                  }
                });
              }
              departmentDropdown.build(
                {
                  div: 'dept_drop',
                  filter: deadlinePage.filter
                }
              );
              hideSpinner();
            })
            .catch(function() {
              hideSpinner();
            });
        })
        .catch(function(response) {
          var target = document.getElementById('headline_section');
          target.innerHTML = response.responseText;
          hideSpinner();
        });
    }
  };
})();

if (window.addEventListener) {
  window.addEventListener('load', deadlinePage.loadDeadlines);
} else {
  window.attachEvent('onload', deadlinePage.loadDeadlines);
}
