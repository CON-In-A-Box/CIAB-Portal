/*
 * Javacript for the Donut org chart
 */

/* jshint browser: true */
/* jshint -W097 */
/* global d3 */
/* exported drawDonutChart */

function setMultiLevelData(data) {
  var multiLevelData = [];
  if (data == null) {
    return null;
  }
  var level = data.length;
  var counter = 0;
  var currentLevelData = [];
  var queue = [];
  for (var i = 0; i < data.length; i++) {
    queue.push(data[i]);
  }

  while (queue.length !== 0) {
    var node = queue.shift();
    currentLevelData.push(node);
    level--;

    if (node.subData) {
      for (i = 0; i < node.subData.length; i++) {
        queue.push(node.subData[i]);
        counter++;
      }
    }
    if (level == 0) {
      level = counter;
      counter = 0;
      multiLevelData.push(currentLevelData);
      currentLevelData = [];
    }
  }
  return multiLevelData;
}

function wrap(text, width) {
  text.each(function() {
    var text = d3.select(this);
    var words = text.text().split(/\s+/).reverse();
    var word;
    var line = [];
    var lineNumber = 0;
    var lineHeight = 1.1;
    var x = 0;
    var y = text.attr('y');
    var dy = 0;
    var tspan = text.text(null)
      .append('tspan')
      .attr('x', x)
      .attr('y', y)
      .attr('dy', dy + 'em');
    word = words.pop();
    while (word) {
      line.push(word);
      tspan.text(line.join(' '));
      if (tspan.node().getComputedTextLength() > width) {
        line.pop();
        tspan.text(line.join(' '));
        line = [ word ];
        if (!lineNumber) {
          ++lineNumber;
        }
        tspan = text.append('tspan')
          .attr('x', x)
          .attr('y', y)
          .attr('dy', lineNumber * lineHeight + dy + 'em')
          .text(word);
      }
      word = words.pop();
    }
  });
}

function drawPieChart(svg, _data, index, pieWidth) {
  var colorScale = d3.scaleOrdinal(d3.schemeTableau10);

  var pie = d3.pie()
    .sort(null)
    .value(function(d) {
      return d.nodeData.dsize;
    });
  var arc = d3.arc()
    .outerRadius(_data[0].nodeData.pieOuter)
    .innerRadius(_data[0].nodeData.pieInner);

  var g = svg.selectAll('.arc' + index).data(pie(_data)).enter()
    .append('a').attr('xlink:href', function(d) {
      if (Object.prototype.hasOwnProperty.call(d.data.nodeData, 'link')) {
        return d.data.nodeData.link;
      }
      return null;
    })
    .append('g')
    .attr('class', 'arc' + index);

  g.append('path').attr('d', arc)
    .style('fill', function(d) {
      if (Number.isInteger(d.data.nodeData.colorIndex))
      {return colorScale(d.data.nodeData.colorIndex);}
      else
      {return d.data.nodeData.colorIndex;}
    })
    .style('stroke', 'black')
    .style('stroke-width', function(d) {
      return d.data.nodeData.strokeWidth;
    });

  g.append('text').attr('transform', function(d) {
    var v = ((d.endAngle - d.startAngle) / 2) + d.startAngle;
    var r = (v * (180.0 / Math.PI)) + -90;
    if (!Object.prototype.hasOwnProperty.call(d.data.nodeData, 'noRotate') &&
       (r >= 90)) {
      r = r - 180;
    }
    return 'translate(' + arc.centroid(d) + ') rotate(' + r + ')';
  })
    .attr('dy', '.35em').style('text-anchor', 'middle')
    .attr('font-size', function(d) {
      if (Object.prototype.hasOwnProperty.call(d.data.nodeData, 'label')) {
        var v = d.data.nodeData.label.length / (500 / (index * index));
        var s = pieWidth / 140;
        return ((1 - v) * s).toString() + 'em';
      }
      return '1em';
    })
    .text(function(d) {
      if (Object.prototype.hasOwnProperty.call(d.data.nodeData, 'label')) {
        return d.data.nodeData.label;
      }
      return '';
    })
    .call(wrap, pieWidth);

}

function drawDonutChart(width, height, data, target) {

  var fixedWidth = 0;
  var floatWidth = 1;
  var _cData;

  /* Parse Data */
  var multiLevelData = setMultiLevelData(data);
  for (var i = 0; i < multiLevelData.length; i++) {
    _cData = multiLevelData[i][0];
    if (Object.prototype.hasOwnProperty.call(_cData.nodeData, 'pieWidth')) {
      fixedWidth += _cData.nodeData.pieWidth;
    } else {
      floatWidth++;
    }
  }

  var maxRadius = (Math.min(width, height) / 2) - fixedWidth;
  var svg = d3.select(target).append('svg')
    .attr('width', width)
    .attr('height', height)
    .append('g')
    .attr('transform',
      'translate(' + width / 2 + ',' + height / 2 + ')');

  var pieWidth = parseInt((maxRadius / (floatWidth)) - floatWidth);

  /* Calculated Data */
  var inner = pieWidth;
  for (i = 0; i < multiLevelData.length; i++) {
    _cData = multiLevelData[i][0];
    var ringWidth = pieWidth;
    if (Object.prototype.hasOwnProperty.call(_cData.nodeData, 'pieWidth')) {
      ringWidth = _cData.nodeData.pieWidth;
    }
    for (var j = 0; j < multiLevelData[i].length; j++) {
      _cData = multiLevelData[i][j];
      _cData.nodeData.pieWidth = ringWidth;
      _cData.nodeData.pieInner = inner;
      _cData.nodeData.pieOuter = inner + ringWidth;
    }
    inner += ringWidth;
  }

  for (i = 0; i < multiLevelData.length; i++) {
    _cData = multiLevelData[i];
    drawPieChart(svg, _cData, i + 1, pieWidth);
  }
}
