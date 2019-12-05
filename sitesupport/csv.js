/*
 * Elements for CSV report visualization
 */

/* jshint browser: true */
/* globals d3 */
/* exported CSVReport */

var CSVReport = (function(options) {
  'use strict';

  var settings = Object.assign(
    {
      base: 'body',
    }, options);

  return {
    options: function(opts) {
      settings = Object.assign(settings, opts);
    },

    build: function(csvData, opts) {
      if (opts != null) {
        settings = Object.assign(settings, opts);
      }
      var parsedCSV = d3.csvParseRows(csvData);
      d3.select(settings.base)
        .selectAll('*').remove();
      d3.select(settings.base)
        .append('div')
        .attr('class', 'UI-table')
        .selectAll('div')
        .data(parsedCSV).enter()
        .append('div')
        .attr('class', 'UI-table-row')
        .selectAll('div')
        .data(function(d, i) {
          if (i == 0) {
            d3.select(this).classed('event-color-primary', true);
          }
          if (i % 2 == 0) {
            d3.select(this).classed('UI-light-gray', true);
          }
          return d;
        }).enter()
        .append('div')
        .attr('class', 'UI-table-cell')
        .text(function(d) { return d; });
    }
  };
}) ();
