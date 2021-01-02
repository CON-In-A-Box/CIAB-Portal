/* jshint esversion: 6 */

// Todo: make a reusable table component instead, or steal one off the Intarwebz

Vue.component('store-list-item', {
  props: { store: Object },
  template: `
  <tr>
    <td>{{ store.id }}</td>
    <td>{{ store.Name }}</td>
    <td>{{ store.StoreSlug }}</td>
    <td>{{ store.Description }}</td>
  </tr>
  `
});

Vue.component('store-list-body', { 
  props: { stores: Array },
  template: `
  <tbody>
    <store-list-item v-for="store in stores" :store="store"></store-list-item>
  </tbody>
  `
});

Vue.component('store-list-head', {
  template: `
  <thead>
    <tr>
      <th>ID</th>
      <th>Name</th>
      <th>Slug</th>
      <th>Description</th>
    </tr>
  </thead>
  `
});

Vue.component('store-list', {
  props: { stores: Array },
  template: `
    <table class="UI-stripedtable">
      <store-list-head></store-list-head>
      <store-list-body :stores="stores"></store-list-body>
    </table>
  `
});

Vue.component('store-form', {
  props: { store: Object, action: String },
  template: `
  <div>
    <form id="store_form">
      <input class="UI-hiddeninput" name="store_id" v-model="store.id" readonly />
      <label class="UI-label" for="store_name">Store Name:</label>
      <input class="UI-input" name="store_name" v-model="store.name" placeholder="Store Name" />
      <label class="UI-label" for="store_slug">Store Slug (short lowercase identifier like 'registration' or 'merch')"</label>
      <input class="UI-input" name="store_slug" v-model="store.slug" placeholder="slug" />
      <label class="UI-label" for="store_description">Description</label>
      <textarea class="UI-input" v-model="store.description" name="store_description"/>
      <button class="UI-eventbutton" :onclick="action" type="submit">Save</button> <!-- colon before onclick means treat action as a variable -->
      <button class="UI-redbutton" onclick="hideSidebar()">Close</button>
    </form>  
  </div>
  `
});

var formApp = null;

function newStore() {
  formApp = new Vue({
    el: '#edit_store',
    data: { store: { id: -1, name: 'New Meeting', slug: '', description: '' }, action: 'addStore(this)' }
  });
  showSidebar('edit_store');
}

function handleErrors(e) {
  hideSidebar();
  if (formApp) { 
    formApp.$destroy();
  }
  alert("Something went wrong");
  location.reload();
}

function addStore() {  
  event.preventDefault();
  var storeForm = document.getElementById('store_form');
  var formData = new FormData(storeForm);
  confirmbox(
    'Confiring Store Details',
    'Are these details correct?')
    .then(function() {
      apiRequest('POST', 'stores', formData)
      .then(function() {
        hideSidebar();
        formApp.$destroy();
        location.reload();
      }).catch(handleErrors);
    })
    .catch(handleErrors);
}


function loadStores() {
  return apiRequest('GET', '/stores')
      .then(function(resp) {
          var json = JSON.parse(resp.responseText);
          var app = new Vue({
              el: '#storesTable',
              data: { stores: json.data },
          });
      }).catch(handleErrors);
}


document.addEventListener('DOMContentLoaded', loadStores);
