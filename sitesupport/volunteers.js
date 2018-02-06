/*
 * Javacript for the Volunteers page
 */

/* jshint browser: true */

'use strict';

var checkout = [];
var hoursSpent = 0;
var groupsNow = [];

function lookupId(id) {
  if (id) {
    var xhttp = new XMLHttpRequest();
    document.getElementById('spinner').innerHTML =
      '<i class=\'fa fa-spinner w3-spin\'></i>';
    document.getElementById('message').innerHTML = '';
    xhttp.onreadystatechange = function() {
      if (this.readyState == 4 && this.status == 200) {
        var response = JSON.parse(this.responseText);
        var uid = response.Id;
        document.getElementById('volunteer').classList.remove('w3-red');
        document.getElementById('message').innerHTML = 'Found ' +
          response['First Name'] + ' ' + response['Last Name'];
        window.location = 'index.php?Function=volunteers&volunteerId=' + uid;
      } else if (this.readyState == 4) {
        document.getElementById('volunteer').classList.add('w3-red');
        document.getElementById('spinner').innerHTML = '';
        if (this.status == 400) {
          document.getElementById('message').innerHTML = id +
            ' invalid lookup.';
        }
        else if (this.status == 404) {
          document.getElementById('message').innerHTML = id + ' not found.';
        }
        else if (this.status == 409) {
          document.getElementById('message').innerHTML = id +
            ' has too many matches.';
        }
      }
    };
    xhttp.open('GET', 'index.php?Function=volunteers&lookupId=' + id, true);
    xhttp.send();
  } else {
    window.location = 'index.php?Function=volunteers';
  }
}

function showHideSoldOut() {
  var unhide = (document.getElementById('soldoutcheck').checked);
  var table = document.getElementById('prizes');
  for (var i = 0, row; row = table.rows[i]; i++) {
    if (unhide && row.className.includes(' hiddenrow')) {
      row.className = row.className.replace(' hiddenrow', '');
    } else if (!unhide && row.className.includes('soldoutrow')) {
      if (row.className.indexOf(' hiddenRow') == -1) {
        row.className += ' hiddenrow';
      }
    }
  }
}

function fillReward() {
  var table = document.getElementById('reward_list');
  while (table.hasChildNodes()) {
    table.removeChild(table.firstChild);
  }

  var list = {};
  checkout.forEach(function(item, index) {
      if (item.PrizeID in list) {
        list[item.PrizeID].count += 1;
      } else {
        list[item.PrizeID] = {name: item.Name, count: 1};
      }
    });

  for (var key in list) {
    var item = list[key];
    var row = table.insertRow(-1);
    var cell = row.insertCell(0);
    if (item.count > 1) {
      var txt = item.name + ' (x' + item.count + ')';
      cell.innerHTML = txt;
    } else {
      cell.innerHTML = item.name;
    }
  }
}

function applyReward() {
  var xhttp = new XMLHttpRequest();
  xhttp.onreadystatechange = function() {
    if (this.readyState == 4 && this.status == 200) {
      fillReward();
      document.getElementById('success_dlg').style.display = 'block';
    }
    else if (this.status == 404) {
      window.alert('404!');
    }
    else if (this.status == 409) {
      window.alert('409!');
    }
  };
  xhttp.open('POST', 'index.php?Function=volunteers', true);
  xhttp.setRequestHeader('Content-type', 'application/x-www-form-urlencoded');
  xhttp.send('rewardId=' + userId + '&rewards=' + JSON.stringify(checkout));
}

function processCheckout() {
  applyReward();
}

function clearCheckout() {
  var section = document.getElementById('checkout_div');
  section.classList.add('w3-hide');
  section.classList.remove('w3-quarter');
  section = document.getElementById('info_div');
  section.classList.add('w3-rest');
  section.classList.remove('w3-threequarter');
  hoursSpent = 0;
  checkout = [];

  var table = document.getElementById('checkout_table');
  while (table.hasChildNodes()) {
    table.removeChild(table.firstChild);
  }

  groupsNow = JSON.parse(groupData);
}

function updateCost(cost) {
  hoursSpent += cost;

  var hours = document.getElementById('hours_left');
  var remain = hoursRemain - hoursSpent;
  hours.innerHTML = remain.toFixed(2);

  var hoursU = document.getElementById('hours_used');
  hoursU.innerHTML = hoursSpent.toFixed(2);
}

function updateCheckout() {
  var table = document.getElementById('checkout_table');
  while (table.hasChildNodes()) {
    table.removeChild(table.firstChild);
  }

  var output = {};
  for (var index in checkout) {
    var item = checkout[index];
    if (item.PrizeID in output) {
      output[item.PrizeID].count += 1;
    } else {
      output[item.PrizeID] = {prize: item, count: 1};
    }
  }

  table = document.getElementById('checkout_table');
  for (index in output) {
    var item = output[index];
    var row = table.insertRow(-1);
    row.setAttribute('data-prizeid', item.prize.PrizeID);
    row.classList.add('w3-hover-red');
    row.setAttribute('onclick', 'removeFromCheckout(' + item.prize.PrizeID +
      ', ' + item.prize.cost + ', ' + item.prize.RewardGroupID + ');');
    var cell = row.insertCell(0);

    if (item.count > 1) {
      var txt = item.prize.Name + ' (x' + item.count + ')';
      cell.innerHTML = txt;
    } else {
      cell.innerHTML = item.prize.Name;
    }

    cell = row.insertCell(1);
    cell.innerHTML = item.prize.cost.toFixed(2);
  }

  var rows = table.getElementsByTagName('tr').length;
  if (rows === 0) {
    clearCheckout();
  }
}

function removeFromCheckout(PrizeID, cost, group) {
  var found = checkout.findIndex(function(element) {
      return element.PrizeID == PrizeID;
    });
  if (found != -1) {
    updateCost(-1 * cost);
    checkout.splice(found, 1);
  }

  if (group !== null) {
    groupsNow[group] -= 1;
  }

  updateCheckout();
}

function addToCheckout(json) {
  var item = JSON.parse(json);
  var cost = parseFloat(item.Value);
  if (item.Promo == 'yes') {
    var found = checkout.find(function(element) {
        return element.PrizeID == item.PrizeID;
      });
    if (found) {
      window.alert('Promo item \'' + item.Name + '\' cannot be added twice');
      return;
    }
    cost = 0;
  }
  if (hoursRemain < hoursSpent + cost) {
    window.alert('Volunteer does not have enough hours for the ' + item.Name);
    return;
  }
  if (groupsNow[item.RewardGroupID] + 1 > item.GroupLimit) {
    window.alert('Too many items from limited group');
    return;
  }
  var count = 0;
  for (var i = 0; i < checkout.length; i++) {
    if (checkout[i].PrizeID == item.PrizeID) {
      count++;
    }
  }
  if (count + 1 > item.Remaining) {
    window.alert('Not enough items in inventory!');
    return;
  }

  item.cost = cost;
  showCheckout();
  checkout.push(item);
  updateCheckout();
  updateCost(cost);
  groupsNow[item.RewardGroupID] += 1;
}

function showCheckout() {
  var section = document.getElementById('checkout_div');
  section.classList.remove('w3-hide');
  section.classList.add('w3-quarter');
  section = document.getElementById('info_div');
  section.classList.remove('w3-rest');
  section.classList.add('w3-threequarter');

  groupsNow = JSON.parse(groupData);

  var hours = document.getElementById('hours_left');
  var remain = hoursRemain - hoursSpent;
  hours.innerHTML = remain.toFixed(2);

  var hoursU = document.getElementById('hours_used');
  hoursU.innerHTML = hoursSpent;
}

function  addPromoToCheckout(names) {
  unclaimed.forEach(function(item) {
    var found = checkout.find(function(element) {
        return element[0] == item.PrizeID;
      });
    if (!found) {
      addToCheckout(item.Json);
    }
  });
}
