/*
 * Elements for report generation
 */

/* jshint browser: true */
/* globals showSidebar, hideSidebar, basicBackendRequest, CSVReport */
/* exported reportGenerationSidebar */

var reportGenerationSidebar = (function(options) {
  'use strict';

  var settings = Object.assign(
    {
      base: 'generated_report_div',
      select: 'generated_csv_select',
      target: null,
      reportListURI: 'availableReports=1',
      reportURI: 'report',
      closeFunction: hideSidebar,
      openFunction: null,
      reportDisplay: null,
    }, options);

  return {
    options: function(opts) {
      settings = Object.assign(settings, opts);
    },

    shown: function() {
      if (settings.openFunction !== null) {
        settings.openFunction();
      }
      var select = document.getElementById(settings.select);
      if (select.options.length == 0 && settings.target !== null) {
        basicBackendRequest('GET', settings.target, settings.reportListURI,
          function(response) {
            var data = JSON.parse(response.response);
            select.options.length = 0;
            data.forEach(function(entry) {
              var option = document.createElement('option');
              option.text = entry;
              select.add(option);
            });
          });
      }
    },

    hidden: function() {
    },

    generateReport: function() {
      var select = document.getElementById(settings.select);
      var report = select.options[select.selectedIndex].text;
      var args = '&' + settings.reportURI + '=' + report;
      window.location = 'index.php?Function=' + settings.target + args;
    },

    viewReport: function() {
      var select = document.getElementById(settings.select);
      var report = select.options[select.selectedIndex].text;
      var args = settings.reportURI + '=' + report;
      basicBackendRequest('GET', settings.target, args,
        function(response) {
          CSVReport.build(response.responseText,
            { base: settings.reportDisplay});
        });
    },

    open: function() {
      showSidebar(settings.base);
    },

    build: function() {
      var dom = document.getElementById(settings.base);
      if (!dom) {
        var body = document.createElement('DIV');
        body.id = settings.base;
        body.classList.add('UI-sidebar-hidden');
        body.classList.add('UI-fixed');
        var div = document.createElement('DIV');
        div.classList.add('UI-center');
        var title = document.createElement('H2');
        title.classList.add('UI-red');
        title.innerHTML = 'Generate CSV Report';
        div.appendChild(title);
        body.appendChild(div);

        div = document.createElement('DIV');
        div.classList.add('UI-center');
        var label = document.createElement('LABEL');
        label.classList.add('UI-label');
        label.htmlFor = settings.select;
        label.innerHTML = 'Report:';
        div.appendChild(label);
        var select = document.createElement('SELECT');
        select.classList.add('UI-padding');
        select.classList.add('UI-select');
        select.id = settings.select;
        div.appendChild(select);
        body.appendChild(div);

        div = document.createElement('DIV');
        div.classList.add('UI-center');
        var button = document.createElement('BUTTON');
        button.id = 'generate_csv';
        button.classList.add('UI-eventbutton');
        button.onclick = reportGenerationSidebar.generateReport;
        button.innerHTML = 'Download .CSV';
        div.appendChild(button);
        if (settings.reportDisplay != null) {
          button = document.createElement('BUTTON');
          button.classList.add('UI-eventbutton');
          button.classList.add('UI-margin');
          button.onclick = reportGenerationSidebar.viewReport;
          button.innerHTML = 'View Report';
          div.appendChild(button);
        }
        button = document.createElement('BUTTON');
        button.classList.add('UI-redbutton');
        if (settings.reportDisplay == null) {
          button.classList.add('UI-margin');
        }
        button.onclick = settings.closeFunction;
        button.innerHTML = 'Close';
        div.appendChild(button);
        body.appendChild(div);

        body.addEventListener('sidebarShow', reportGenerationSidebar.shown,
          false);
        body.addEventListener('sidebarHidden', reportGenerationSidebar.hidden,
          false);

        document.body.appendChild(body);
      }
    }
  };
}) ();

if (window.addEventListener) {
  window.addEventListener('load', reportGenerationSidebar.build);
} else {
  window.attachEvent('onload', reportGenerationSidebar.build);
}
