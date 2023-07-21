/*
 * Javacript for the Volunteers page
 */

/* jshint browser: true */
/* jshint -W097 */
/* globals confirmbox, userId, escapeHtml, userEmail,
           groupData, checkAuthentication, adminMode, unclaimed, hoursRemain,
           hideSidebar, showSidebar, alertbox, basicBackendRequest
           */
/* exported processReturn,  markDelete, generateDeptReport,
            clearReturnCart, toggleAdminMode, addPromoToCheckout, removeFromCheckout,
            processCheckout, sidebarMainDiv */

'use strict';

var checkout = [];
var hoursSpent = 0;
var groupsNow = [];
var sidebarMainDiv = 'info_div';
var returnCart = [];

function basicVolunteersRequestAdmin(parameter, finish) {
  basicBackendRequest('POST', 'volunteers/admin', parameter, finish);
}


function fillReward() {
  var table = document.getElementById('reward_list');
  while (table.hasChildNodes()) {
    table.removeChild(table.firstChild);
  }

  var list = {};
  checkout.forEach(function(item) {
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
      cell.innerHTML = escapeHtml(txt);
    } else {
      cell.innerHTML = escapeHtml(item.name);
    }
  }
}

function processCheckout() {
  confirmbox('Confirm Distribute Gifts',
    'Are the selected gifts correct?').then(function() {
    var parameter = 'rewardId=' + userId + '&rewards=' +
      JSON.stringify(checkout);
    basicVolunteersRequestAdmin(parameter, function() {
      fillReward();
      document.getElementById('success_dlg').style.display = 'block';
    });
  });
}

function clearCheckout() {
  hideSidebar();
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
  hours.innerHTML = Math.round(remain * 100) / 100;

  var hoursU = document.getElementById('hours_used');
  hoursU.innerHTML = Math.round(hoursSpent * 100) / 100;
}

function updateCheckout() {
  var table = document.getElementById('checkout_table');
  while (table.hasChildNodes()) {
    table.removeChild(table.firstChild);
  }

  var output = {};
  var item;
  for (var index in checkout) {
    item = checkout[index];
    if (item.PrizeID in output) {
      output[item.PrizeID].count += 1;
    } else {
      output[item.PrizeID] = {prize: item, count: 1};
    }
  }

  table = document.getElementById('checkout_table');
  for (var index2 in output) {
    var row = table.insertRow(-1);
    item = output[index2];
    row.setAttribute('data-prizeid', item.prize.PrizeID);
    row.classList.add('VOL-hover-red');
    row.setAttribute('onclick', 'removeFromCheckout(' + item.prize.PrizeID +
      ', ' + item.prize.cost + ', ' + item.prize.RewardGroupID + ');');
    var cell = row.insertCell(0);

    if (item.count > 1) {
      var txt = item.prize.Name + ' (x' + item.count + ')';
      cell.innerHTML = escapeHtml(txt);
    } else {
      cell.innerHTML = escapeHtml(item.prize.Name);
    }

    cell = row.insertCell(1);
    cell.innerHTML = Math.round(item.prize.cost * 100) / 100;
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
  var item = JSON.parse(atob(json));
  var cost = parseFloat(item.Value);
  if (item.Promo == 'yes') {
    var found = checkout.find(function(element) {
      return element.PrizeID == item.PrizeID;
    });
    if (found) {
      alertbox('Promo item \'' + item.Name + '\' cannot be added twice');
      return;
    }
    cost = 0;
  }
  if (hoursRemain < hoursSpent + cost) {
    alertbox('Volunteer does not have enough hours for the ' + item.Name);
    return;
  }
  if (groupsNow[item.RewardGroupID] + 1 > item.GroupLimit) {
    alertbox('Too many items from limited group');
    return;
  }
  var count = 0;
  for (var i = 0; i < checkout.length; i++) {
    if (checkout[i].PrizeID == item.PrizeID) {
      count++;
    }
  }
  if (count + 1 > item.Remaining) {
    alertbox('Not enough items in inventory!');
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
  showSidebar('checkout_div');
  groupsNow = JSON.parse(groupData);

  var hours = document.getElementById('hours_left');
  var remain = hoursRemain - hoursSpent;
  hours.innerHTML = Math.round(remain * 100) / 100;

  var hoursU = document.getElementById('hours_used');
  hoursU.innerHTML = hoursSpent;
}

function  addPromoToCheckout() {
  unclaimed.forEach(function(item) {
    var found = checkout.find(function(element) {
      return element[0] == item.PrizeID;
    });
    if (!found) {
      addToCheckout(item.Json);
    }
  });
}

function enterAdmin() {
  setTimeout(location.reload(), 1000);
}

function failAdmin(error) {
  document.getElementById('admin_slider').checked = false;
  if (error) {
    alertbox('Login Failed (' + error + ')');
  }
}

function toggleAdminMode() {
  document.cookie = 'CIAB_VOLUNTEERADMIN=;expires=Thu, 01 Jan 1970 ' +
                    '00:00:01 GMT;';
  var target = '';
  if (userId) {
    target = 'index.php?Function=volunteers/admin&volunteerId=' + userId;
  } else {
    target = 'index.php?Function=volunteers/admin';
  }
  if (!adminMode) {
    checkAuthentication(userEmail, enterAdmin, failAdmin,
      {target: 'volunteers/admin'});
  } else {
    setTimeout(function() {window.location = target;}, 1000);
  }
}


function generateDeptReport() {
  var name = document.getElementById('dept_data_name').value;
  var deptid = document.getElementById('dept_data').value;
  window.location = 'index.php?Function=volunteers/report&dept_report=' +
                    deptid + '&dept_name=' + name;
}

function markDelete(index, tableRow) {
  var table = document.getElementById('return_table');
  var row = table.rows[tableRow];

  if (returnCart[index].Returned) {
    row.classList.remove('UI-yellow');
    row.style.fontWeight = 'normal';
    returnCart[index].Returned = false;
  } else {
    row.classList.add('UI-yellow');
    row.style.fontWeight = 'bold';
    returnCart[index].Returned = true;
  }

  var total = 0;
  for (var index2 in returnCart) {
    var item = returnCart[index2];
    if (item.Returned) {
      total += parseFloat(item.item.Value);
    }
  }

  var hours = document.getElementById('credit_hours');
  hours.innerHTML = Math.round(total * 100) / 100;

}

function clearReturnCart() {
  returnCart = [];

}


function finishReturn() {
  var table = document.getElementById('return_list');

  while (table.hasChildNodes()) {
    table.removeChild(table.firstChild);
  }

  var list = {};
  returnCart.forEach(function(item) {
    if (item.Returned) {
      if (item.item.PrizeID in list) {
        list[item.item.PrizeID].count += 1;
      } else {
        list[item.item.PrizeID] = {name: item.item.Name, count: 1};
      }
    }
  });

  for (var key in list) {
    var item = list[key];
    var row = table.insertRow(-1);
    var cell = row.insertCell(0);
    if (item.count > 1) {
      var txt = item.name + ' (x' + item.count + ')';
      cell.innerHTML = escapeHtml(txt);
    } else {
      cell.innerHTML = escapeHtml(item.name);
    }
  }
}

function processReturn() {
  confirmbox('Confirm Gift Return',
    'Are the returned gifts correct?').then(function() {
    var data = [];
    for (var index in returnCart) {
      var item = returnCart[index];
      if (item.Returned) {
        data.push(item.item.PrizeID);
      }
    }
    var parameter = 'refundId=' + userId + '&rewards=' + JSON.stringify(data);
    basicVolunteersRequestAdmin(parameter, function() {
      finishReturn();
      document.getElementById('return_success_dlg').style.display = 'block';
    });
  });
}
