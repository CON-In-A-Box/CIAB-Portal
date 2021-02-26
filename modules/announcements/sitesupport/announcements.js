/*
 * Javacript for the Announcements page
 */

/* jshint browser: true */
/* jshint -W097 */
/* globals apiRequest, showSpinner, hideSpinner, showSidebar,
           confirmbox, alertbox, departmentDropdown */

var announcementPage = (function(options) {
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
      var id = dataset.ciabAnnouncementId;
      var text = document.getElementById('announcement-' + id + '-text')
        .innerHTML;
      document.getElementById('announcement_text').value = text;
      document.getElementById('department_dropdown_select')
        .value = dataset.ciabAnnouncementDepartment;

      var canEdit = Object.prototype.hasOwnProperty.call(permissions,
        dataset.ciabAnnouncementDepartment + '_announcement_post');
      var canDelete = Object.prototype.hasOwnProperty.call(permissions,
        dataset.ciabAnnouncementDepartment + '_announcement_delete');

      var button = document.getElementById('remove_event_button');
      if (canDelete) {
        button.classList.remove('UI-hide');
        button.dataset.ciabAnnouncementId = id;
      } else {
        button.classList.add('UI-hide');
      }
      button = document.getElementById('save_event_button');
      if (canEdit) {
        button.classList.remove('UI-hide');
        button.dataset.ciabAnnouncementId = id;
      } else {
        button.classList.add('UI-hide');
      }

      document.getElementById('modify_title').innerHTML = 'Modify';
      showSidebar('modify_announcement');
    },

    removeEvent: function(button) {
      var dataset = button.dataset;
      var id = dataset.ciabAnnouncementId;
      confirmbox('Remove Announcement', 'Confirm removal of this announcement')
        .then(
          function() {
            showSpinner();
            apiRequest('DELETE', 'announcement/' + id, '')
              .then(function() {
                location.reload();
              })
              .catch(function(response) {
                if (response instanceof Error) { throw response; }
                var data = JSON.parse(response.responseText);
                hideSpinner();
                alertbox('Delete Failed', data.status);
              });
          });
    },

    updateEvent: function(button) {
      var dataset = button.dataset;
      var id = dataset.ciabAnnouncementId;
      var text = document.getElementById('announcement_text').value;
      var dept = document.getElementById('department_dropdown_select').value;
      var scope = document.getElementById('scope_drop').value;
      var email = (document.getElementById('announcement_email').checked ?
        1 : 0);
      var message = 'Confirm update of this announcement';
      if (email && id == -1) {
        if (scope == '0') {
          message += '<div class="UI-border UI-margin UI-red"> ' +
          '<span class="UI-bold">' +
          '<em>WARNING</em>: This will also email EVERY member that has EVER ' +
          'registered for the event!</span></div>';
        }
        else if (scope == '1') {
          message += '<div class="UI-border UI-margin UI-yellow"> ' +
          '<span class="UI-bold">This will also email ' +
          'EVERY member of the event staff</span>';
        }
        else if (scope == '2') {
          var d = document.getElementById('department_dropdown_select');
          message += '<div class="UI-border UI-margin"> ' +
          '<span>This will also email every member of the \'' +
          d.options[d.selectedIndex].text + '\' Department</span>';
        }
      }
      confirmbox('Update Announcement', message)
        .then(
          function() {
            showSpinner();
            var method = 'PUT';
            var path = 'announcement/' + id;
            if (id == -1) {
              method = 'POST';
              path = 'department/' + dept + '/announcement';
            }
            apiRequest(method, path,
              'Scope=' + scope + '&Text=' + encodeURI(text) + '&Department=' +
              dept + '&Email=' + email)
              .then(function() {
                location.reload();
              })
              .catch(function(response) {
                if (response instanceof Error) { throw response; }
                var data = JSON.parse(response.responseText);
                hideSpinner();
                alertbox('Update Failed', data.status);
              });
          })
        .catch(function(response) {
          if (response instanceof Error) { throw response; }
        });
    },

    displayAnnouncements: function(cache) {
      var target = document.getElementById('headline_section');
      cache.forEach(function(data) {
        if (data !== null) {
          target.appendChild(data[0]);
        }
      });
    },

    addAnnouncement: function(table, data2) {
      var line = document.createElement('DIV');
      line.classList.add('UI-table-row');

      var f = document.createElement('DIV');
      f.classList.add('UI-table-cell');
      f.id = 'announcement-' + data2.id + '-text';
      f.appendChild(document.createTextNode(data2.text));
      line.appendChild(f);

      f = document.createElement('DIV');
      f.classList.add('UI-table-cell');
      f.id = 'announcement-' + data2.id + '-posted';
      f.appendChild(document.createTextNode(data2.postedOn));
      line.appendChild(f);

      f = document.createElement('DIV');
      f.classList.add('UI-table-cell');
      f.id = 'announcement-' + data2.id + '-poster';
      var name = data2.postedBy.firstName + ' ' + data2.postedBy.lastName +
        ' (' + data2.postedBy.email + ')';
      f.appendChild(document.createTextNode(name));
      line.appendChild(f);

      f = document.createElement('DIV');
      f.classList.add('UI-table-cell');
      f.id = 'announcement-' + data2.id + '-scope';
      if (data2.scope >= 2)
      {
        f.appendChild(document.createTextNode('Department Only'));
      }
      else if (data2.scope >= 1)
      {
        f.appendChild(document.createTextNode('All ConCom'));
      }
      else
      {
        f.appendChild(document.createTextNode('Convention Wide'));
      }
      line.appendChild(f);

      f = document.createElement('DIV');
      f.setAttribute('name', 'announcement-table-modify-' +
        data2.departmentId.id);
      f.classList.add('UI-table-cell');
      f.classList.add('UI-center');
      f.classList.add('UI-hide');
      var button = document.createElement('button');
      button.innerHTML = '<i class="fas fa-calendar-check"></i>';
      button.dataset.ciabAnnouncementId = data2.id;
      button.dataset.ciabAnnouncementDepartment = data2.departmentId.id;
      button.onclick = announcementPage.openEdit;
      button.classList.add('UI-button');
      f.appendChild(button);
      line.appendChild(f);
      table.appendChild(line);
    },

    generateTableHeader: function(dept) {
      var line = document.createElement('DIV');
      line.id = 'announcement-table-header-' + dept.id;
      line.classList.add('UI-table-row');

      var f = document.createElement('DIV');
      f.classList.add('UI-table-cell');
      f.appendChild(document.createTextNode('Announcement'));
      line.appendChild(f);

      f = document.createElement('DIV');
      f.classList.add('UI-table-cell');
      f.appendChild(document.createTextNode('Posted On'));
      line.appendChild(f);

      f = document.createElement('DIV');
      f.classList.add('UI-table-cell');
      f.appendChild(document.createTextNode('Posted By'));
      line.appendChild(f);

      f = document.createElement('DIV');
      f.classList.add('UI-table-cell');
      f.appendChild(document.createTextNode('Scope'));
      line.appendChild(f);

      f = document.createElement('DIV');
      f.setAttribute('name', 'announcement-table-modify-' + dept.id);
      f.classList.add('UI-table-cell');
      f.classList.add('UI-hide');
      f.classList.add('UI-center');
      f.appendChild(document.createTextNode(''));
      line.appendChild(f);
      return line;
    },

    newAnnouncement: function() {
      var dataset = this.dataset;
      document.getElementById('announcement_text').value = '';
      document.getElementById('announcement_email').checked = false;
      if (dataset.ciabAnnouncementDepartment) {
        document.getElementById('department_dropdown_select')
          .value = dataset.ciabAnnouncementDepartment;
      } else {
        document.getElementById('department_dropdown_select')
          .selectedIndex = 0;
      }
      var button = document.getElementById('remove_event_button');
      button.classList.add('UI-hide');
      button = document.getElementById('save_event_button');
      button.classList.remove('UI-hide');
      button.dataset.ciabAnnouncementId = -1;

      document.getElementById('modify_title').innerHTML = 'Add';
      showSidebar('modify_announcement');
    },

    emptyAnnouncementBlock: function(dept) {
      var block = document.createElement('DIV');
      block.id = 'announcement-block-' + dept.id;
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
      button.dataset.ciabAnnouncementDepartment = dept.id;
      button.onclick = announcementPage.newAnnouncement;
      block.appendChild(title);
      var contents = document.createElement('DIV');
      contents.classList.add('UI-container');
      block.appendChild(contents);
      var table = document.createElement('DIV');
      table.classList.add('UI-table-all');
      contents.appendChild(table);
      var line = announcementPage.generateTableHeader(dept);
      table.appendChild(line);
      return [block, table];
    },

    buildAnnouncementBlock: function(cache, result, data) {
      var table;
      var rc;
      var dept = cache[parseInt(data.departmentId.id)];
      if (!dept) {
        rc = announcementPage.emptyAnnouncementBlock(data.departmentId);
        table = rc[1];
        cache[parseInt(data.departmentId.id)] = rc;
      } else {
        rc = cache[parseInt(data.departmentId.id)];
        table = rc[1];
      }
      announcementPage.addAnnouncement(table, data);
    },

    filter: function(dept) {
      var id = dept.id.id.toString() + '_announcement_post';
      return Object.prototype.hasOwnProperty.call(permissions, id);
    },

    loadAnnouncements: function() {
      showSpinner();
      apiRequest('GET',
        'member/current/announcements',
        'maxResults=all&include=departmentId,postedBy')
        .then(function(response) {
          var result = JSON.parse(response.responseText);
          if (result.data.length > 0) {
            var cache = Array();
            result.data.forEach(function(data) {
              announcementPage.buildAnnouncementBlock(cache, result, data);
            });
            announcementPage.displayAnnouncements(cache);
          }
          apiRequest('GET',
            'permissions/method/announcement',
            'maxResults=all')
            .then(function(response) {
              result = JSON.parse(response.responseText);
              var havePost = false;
              permissions = {};
              if (result.data.length > 0) {
                result.data.forEach(function(data) {
                  if (data.allowed) {
                    permissions[data.subdata.departmentId + '_' +
                                  data.subtype] = data;
                    if (data.subtype == 'announcement_post') {
                      var sect = 'announcement-block-' +
                                 data.subdata.departmentId;
                      var block = document.getElementById(sect);
                      if (block !== null) {
                        var button = block.getElementsByTagName('button');
                        if (button !== null) {
                          button[0].classList.remove('UI-hide');
                        }
                      }
                      if (!havePost) {
                        havePost = true;
                        var add = document.getElementById(
                          'announcement-sectionbar-add');
                        add.classList.remove('UI-hide');
                        add.onclick = announcementPage.newAnnouncement;
                      }
                    }
                    if (data.subtype == 'announcement_delete' ||
                          data.subtype == 'announcement_put') {
                      var line = 'announcement-table-modify-' +
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
                  filter: announcementPage.filter
                }
              );
              hideSpinner();
            })
            .catch(function(response) {
              if (response instanceof Error) { throw response; }
              hideSpinner();
            });
        })
        .catch(function(response) {
          if (response instanceof Error) { throw response; }
          var target = document.getElementById('headline_section');
          target.innerHTML = response.responseText;
          hideSpinner();
        });
    }
  };
})();

if (window.addEventListener) {
  window.addEventListener('load', announcementPage.loadAnnouncements);
} else {
  window.attachEvent('onload', announcementPage.loadAnnouncements);
}
