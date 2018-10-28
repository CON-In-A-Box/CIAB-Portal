/*
 * Javacript for the Volunteers page
 */

/* jshint browser: true */
/* jshint -W097 */
/* globals confirmbox, userId, escapeHtml, userEmail, checkAuthentication,
           kioskMode, groupData, checkAuthentication, adminMode, unclaimed,
           hoursRemain, userLookup */

'use strict';

var checkout = [];
var hoursSpent = 0;
var groupsNow = [];
var sidebarMainDiv = 'info_div';
var returnCart = [];

function lookupFail(target, resp, name, code) {
  userLookup.markFailure();

  if (code == 404) {
    document.getElementById('userLookup_message').innerHTML =
      name + ' not found.';
  }
  else if (code == 409) {
    document.getElementById('userLookup_message').innerHTML =
      'There are too many matches.';
  }
  else {
    document.getElementById('userLookup_message').innerHTML =
      name + ' invalid name lookup.(' + code + ')';
  }
}

function showHideSoldOut() {
  var unhide = !(document.getElementById('soldoutcheck').checked);
  var table = document.getElementById('prizes');
  for (var i = 0; i < table.rows.length; i++) {
    var row = table.rows[i];
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
      cell.innerHTML = escapeHtml(txt);
    } else {
      cell.innerHTML = escapeHtml(item.name);
    }
  }
}

function applyReward() {
  confirmbox.close();
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
  xhttp.open('POST', 'index.php?Function=volunteers/admin', true);
  xhttp.setRequestHeader('Content-type', 'application/x-www-form-urlencoded');
  xhttp.send('rewardId=' + userId + '&rewards=' + JSON.stringify(checkout));
}

function processCheckout() {
  confirmbox.start('Confirm Distribute Gifts',
    'Are the selected gifts correct?', applyReward);
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
  for (index in output) {
    var row = table.insertRow(-1);
    item = output[index];
    row.setAttribute('data-prizeid', item.prize.PrizeID);
    row.classList.add('w3-hover-red');
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
  showSidebar('checkout_div');
  groupsNow = JSON.parse(groupData);

  var hours = document.getElementById('hours_left');
  var remain = hoursRemain - hoursSpent;
  hours.innerHTML = Math.round(remain * 100) / 100;

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

function enterAdmin() {
  setTimeout(location.reload(), 1000);
}

function failAdmin(error) {
  document.getElementById('admin_slider').checked = false;
  if (error) {
    window.alert('Login Failed (' + error + ')');
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

function showEditHours(data) {
  showSidebar('edit_user_hour_div');
  var item = JSON.parse(atob(data));
  document.getElementById('edit_name').value = item.Volunteer;
  document.getElementById('edit_hours').value = item['Actual Hours'];
  var options = document.getElementById('edit_mod');
  var value = parseFloat(item['Time Modifier']);
  options.selectedIndex = 0;
  for (var i = 0, n = options.length; i < n ; i++) {
    if (options[i].value == value) {
      options.selectedIndex = i;
      break;
    } else if (options[i].value < value) {
      options.selectedIndex = i;
    } else {
      break;
    }
  }
  document.getElementById('edit_enter').value = item['Entered By'];
  document.getElementById('edit_auth').value = item['Authorized By'];
  document.getElementById('edit_dept').value = item['Department Worked'];
  var date = item['End Date Time'];
  date = date.replace(/\s+/g, 'T');
  document.getElementById('edit_end').value = date;
  document.getElementById('edit_data').value = data;
}

function commitHours() {
  if (!window.confirm('=============================\n' +
                      'Please! double check entries!\n' +
                      '=============================\n' +
                      '\n' +
                      'Proceed with Volunteer Hour Update?')) {
    return;
  }

  var data = document.getElementById('edit_data').value;
  var item = JSON.parse(atob(data));

  item['Actual Hours'] = parseFloat(
    document.getElementById('edit_hours').value);
  item['End Date Time'] = document.getElementById('edit_end').value;
  var e = document.getElementById('edit_mod');
  item['Time Modifier'] = e.options[e.selectedIndex].value;
  item['Department Worked'] = document.getElementById('edit_dept').value;
  item['Authorized By'] = document.getElementById('edit_auth').value;

  var xhttp = new XMLHttpRequest();
  xhttp.onreadystatechange = function() {
    if (this.readyState == 4 && this.status == 200) {
      location.reload();
    }
    else if (this.status == 404) {
      window.alert('ERROR 404!');
    }
    else if (this.status == 409) {
      window.alert('ERROR 409!');
    }
  };
  xhttp.open('POST', 'index.php?Function=volunteers/admin', true);
  xhttp.setRequestHeader('Content-type', 'application/x-www-form-urlencoded');
  xhttp.send('update_hour=' + JSON.stringify(item));
}

function deleteHours() {
  if (!window.confirm('DELETE Volunteer Entry?')) {
    return;
  }

  var data = document.getElementById('edit_data').value;
  var item = JSON.parse(atob(data));

  var xhttp = new XMLHttpRequest();
  xhttp.onreadystatechange = function() {
    if (this.readyState == 4 && this.status == 200) {
      location.reload();
    }
    else if (this.status == 404) {
      window.alert('ERROR 404!');
    }
    else if (this.status == 409) {
      window.alert('ERROR 409!');
    }
  };
  xhttp.open('POST', 'index.php?Function=volunteers/admin', true);
  xhttp.setRequestHeader('Content-type', 'application/x-www-form-urlencoded');
  xhttp.send('delete_hour=' + item.EntryID);
}

function showEditPrize(data) {
  groupsNow = JSON.parse(groupData);
  showSidebar('edit_prize_div');

  var item;
  if (!data) {
    document.getElementById('edit_prize_title').innerHTML = 'Enter New Gift';
    document.getElementById('delete_prize_button').style.visibility = 'hidden';
    item = {};
  } else {
    document.getElementById('edit_prize_title').innerHTML = 'Edit Gift Entry';
    document.getElementById('delete_prize_button').style.visibility = 'visible';
    item = JSON.parse(atob(data));
  }

  var grp = document.getElementById('edit_prize_group');
  if (grp.length == 1) {
    var option;
    for (var key in groupsNow) {
      option = document.createElement('option');
      option.text = 'Group #' + key + ' : Limit ' + groupsNow[key];
      option.value = key;
      grp.add(option);
    }
    option = document.createElement('option');
    option.text = 'New Group';
    option.value = 'new';
    grp.add(option);
  }

  if (data) {
    item = JSON.parse(atob(data));
    document.getElementById('edit_prize_name').value = item.Name;
    document.getElementById('edit_prize_value').value = item.Value;
    document.getElementById('edit_prize_promo').value = item.Promo;
    if (item.RewardGroupID) {
      grp.value = item.RewardGroupID;
    } else {
      grp.value = 'none';
    }
    prizeGroupChange();

    document.getElementById('edit_prize_count').value = item.Remaining;

    document.getElementById('prize_data').value = data;
  } else {
    document.getElementById('edit_prize_name').value = '';
    document.getElementById('edit_prize_value').value = 0.0;
    document.getElementById('edit_prize_count').value = 0;
    document.getElementById('edit_prize_promo').value = 'no';
    grp.value = 'none';
    prizeGroupChange();
    document.getElementById('prize_data').value = null;
  }
}

function deletePrize() {
  if (!window.confirm('DELETE Gift Entry?\n\n' +
                      'WARNING!!!  Only do this if NONE of this gift ' +
                      'has been distributed. It will lead to corrupt ' +
                      'reward records. To DELETE a gift that has been ' +
                      'rewarded set inventory to \'0\'')) {
    return;
  }

  var data = document.getElementById('prize_data').value;
  var item = JSON.parse(atob(data));

  var xhttp = new XMLHttpRequest();
  xhttp.onreadystatechange = function() {
    if (this.readyState == 4 && this.status == 200) {
      location.reload();
    }
    else if (this.status == 404) {
      window.alert('ERROR 404!');
    }
    else if (this.status == 409) {
      window.alert('ERROR 409!');
    }
  };
  xhttp.open('POST', 'index.php?Function=volunteers/admin', true);
  xhttp.setRequestHeader('Content-type', 'application/x-www-form-urlencoded');
  xhttp.send('delete_prize=' + item.PrizeID);
}

function commitPrize() {
  var data = document.getElementById('prize_data').value;
  var item = null;

  if (data) {
    if (!window.confirm('=============================\n' +
                        'Please! double check entries!\n' +
                        '=============================\n' +
                        '\n' +
                        'Proceed with Volunteer Gift Update?')) {
      return;
    }
    item = JSON.parse(atob(data));
  } else {
    if (!window.confirm('=============================\n' +
                        'Please! double check entries!\n' +
                        '=============================\n' +
                        '\n' +
                        'Proceed with Addition of new Volunteer Gift?')) {
      return;
    }
    item = {Name:'', Value:0, RewardGroupID:null, GroupLimit:0,
            Promo:'no', TotalInventory:0, Remaining:0};
  }

  item.Name = document.getElementById('edit_prize_name').value;
  item.Value = parseFloat(document.getElementById('edit_prize_value').value);
  var grp = document.getElementById('edit_prize_group').value;
  if (grp === 'none') {
    item.RewardGroupID = '';
  } else {
    item.RewardGroupID = grp;
  }

  item.GroupLimit = parseInt(
    document.getElementById('edit_prize_group_count').value);

  var e = document.getElementById('edit_prize_promo');
  item.Promo = e.options[e.selectedIndex].value;

  if (item.Remaining != document.getElementById('edit_prize_count').value) {
    var amount = parseInt(item.Remaining) -
        parseInt(document.getElementById('edit_prize_count').value);
    if (amount !== 0) {
      var newValue = parseInt(item.TotalInventory) - amount;
      item.TotalInventory = newValue;
    }
  }

  var xhttp = new XMLHttpRequest();
  xhttp.onreadystatechange = function() {
    if (this.readyState == 4 && this.status == 200) {
      location.reload();
    }
    else if (this.status == 404) {
      window.alert('ERROR 404!');
    }
    else if (this.status == 409) {
      window.alert('ERROR 409!');
    }
  };
  xhttp.open('POST', 'index.php?Function=volunteers/admin', true);
  xhttp.setRequestHeader('Content-type', 'application/x-www-form-urlencoded');
  if (data) {
    xhttp.send('update_prize=' + JSON.stringify(item));
  } else {
    xhttp.send('new_prize=' + JSON.stringify(item));
  }
}

function prizeGroupChange() {
  var grp = document.getElementById('edit_prize_group').value;
  var cnt = document.getElementById('edit_prize_group_count');
  if (grp === 'none') {
    cnt.disabled = true;
    cnt.value = '';
    cnt.classList.add('w3-disabled');
  } else if (grp === 'new') {
    cnt.value = 1;
    cnt.disabled = false;
    cnt.classList.remove('w3-disabled');
  } else {
    cnt.value = groupsNow[grp];
    cnt.disabled = false;
    cnt.classList.remove('w3-disabled');
  }
}

function generateHourReport() {
  var hours = document.getElementById('report_hour_min').value;
  window.location = 'index.php?Function=volunteers/admin&min_hour=' + hours;
}

function minHourReport() {
  showSidebar('hour_report_div');
  document.getElementById('report_hour_min').value = 30;
}

function generateDeptReport() {
  var name = document.getElementById('dept_data_name').value;
  var deptid = document.getElementById('dept_data').value;
  window.location = 'index.php?Function=volunteers/admin&dept_report=' +
                    deptid + '&dept_name=' + name;
}

function departmentReport(name, dept) {
  showSidebar('department_report_div');
  document.getElementById('dept_name').innerHTML = name;
  document.getElementById('dept_data').value = dept;
  document.getElementById('dept_data_name').value = name;
}

function switchKiosk() {
  var xhttp = new XMLHttpRequest();
  xhttp.onreadystatechange = function() {
    if (this.readyState == 4 && this.status == 200) {
      setTimeout(function() {location.reload() ;}, 1000);
    }
  };
  xhttp.open('POST', 'index.php?Function=volunteers/kiosk', true);
  xhttp.setRequestHeader('Content-type', 'application/x-www-form-urlencoded');
  xhttp.send('&toggleKiosk=true');
}

function failKiosk(error) {
  document.getElementById('kiosk_slider').checked = true;
  if (error) {
    window.alert('Login Failed (' + error + ')');
  }
}

function toggleKioskMode() {
  if (kioskMode) {
    checkAuthentication(userEmail, switchKiosk, failKiosk,
                        {title: 'Exiting Kiosk Mode'});
  } else {
    setTimeout(function() {
        window.location = 'index.php?Function=volunteers/kiosk';
      }, 1000);
  }
}

function generateCSV() {
  showSidebar('csv_export_div');
}

function generatCSVReport() {
  var table = document.getElementById('csv_table').value;
  window.location = 'index.php?Function=volunteers/admin&generateCSV=' + table;
}

function generateDerivedCSV() {
  showSidebar('csv_export_derived_div');
}

function generateDerivedCSVReport() {
  var table = document.getElementById('der_csv_table');
  var text = table.options[table.selectedIndex].text;
  var sql = document.getElementById('der_csv_table').value;
  var args = '&CSVfromSQL=' + sql + '&name=' + text;
  window.location = 'index.php?Function=volunteers/admin' + args;
}

function markDelete(index, tableRow) {
  var table = document.getElementById('return_table');
  var row = table.rows[tableRow];

  if (returnCart[index].Returned) {
    row.classList.remove('w3-yellow');
    row.style.fontWeight = 'normal';
    returnCart[index].Returned = false;
  } else {
    row.classList.add('w3-yellow');
    row.style.fontWeight = 'bold';
    returnCart[index].Returned = true;
  }

  var total = 0;
  for (index in returnCart) {
    var item = returnCart[index];
    if (item.Returned) {
      total += parseFloat(item.item.Value);
    }
  }

  var hours = document.getElementById('credit_hours');
  hours.innerHTML = Math.round(total * 100) / 100;

}

function showReturn(json) {
  /* clear existing */
  var table = document.getElementById('return_table');
  while (table.hasChildNodes()) {
    table.removeChild(table.firstChild);
  }
  returnCart = [];

  var prizes = JSON.parse(atob(json));
  showSidebar('return_div');
  for (var index in prizes) {
    var item = prizes[index];
    for (var i = 0; i < item.Aquired; i++) {
      var row = table.insertRow(-1);
      row.setAttribute('data-prizeid', item.PrizeID);
      row.classList.add('w3-hover-red');
      row.setAttribute('onclick', 'markDelete(' + returnCart.length +
        ', ' + row.rowIndex + ');');
      var cell = row.insertCell(0);
      cell.innerHTML = escapeHtml(item.Name);
      cell = row.insertCell(1);
      cell.innerHTML = Math.round(item.Value * 100) / 100;
      var cartItem = [];
      cartItem.item = item;
      cartItem.Returned = false;
      cartItem.row = row;
      returnCart.push(cartItem);
    }
  }
}

function finishReturn() {
  var table = document.getElementById('return_list');

  while (table.hasChildNodes()) {
    table.removeChild(table.firstChild);
  }

  var list = {};
  returnCart.forEach(function(item, index) {
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

function doReturn() {
  confirmbox.close();
  var xhttp = new XMLHttpRequest();
  xhttp.onreadystatechange = function() {
    if (this.readyState == 4 && this.status == 200) {
      finishReturn();
      document.getElementById('return_success_dlg').style.display = 'block';
    }
    else if (this.status == 404) {
      window.alert('404!');
    }
    else if (this.status == 409) {
      window.alert('409!');
    }
  };
  xhttp.open('POST', 'index.php?Function=volunteers/admin', true);
  xhttp.setRequestHeader('Content-type', 'application/x-www-form-urlencoded');
  var data = [];
  for (var index in returnCart) {
    var item = returnCart[index];
    if (item.Returned) {
      data.push(item.item.PrizeID);
    }
  }
  xhttp.send('refundId=' + userId + '&rewards=' + JSON.stringify(data));
}

function processReturn() {
  confirmbox.start('Confirm Gift Return',
    'Are the returned gifts correct?', doReturn);
}
