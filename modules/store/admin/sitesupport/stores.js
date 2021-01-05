/* jshint esversion: 6 */

// Todo: make a reusable table component instead, or steal one off the Intarwebz

let thisApp = null;

Vue.component('store-list-item', {
  props: { store: Object },
  methods: {
    edit: function(event) {
      thisApp.store = { ...this.store };
      showSidebar('edit_store');
    },
    manage: function() {
      location.assign('/index.php?Function=store/admin/products&store_id=' + this.store.id);
    }
  },
  template: `
  <tr>
    <td>{{ store.id }}</td>
    <td>{{ store.Name }}</td>
    <td>{{ store.StoreSlug }}</td>
    <td>{{ store.Description }}</td>
    <td><button class="UI-eventbutton" :store="store" @click="manage">Manage Products</button></td>
    <td><button class="UI-yellowbutton" :store="store" @click="edit">Edit</button></td>
  </tr>
  `
});

Vue.component('store-list-body', { 
  props: { stores: Array },
  template: `
  <tbody>
    <store-list-item v-for="store in stores" :key="store.id" :store="store"></store-list-item>
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
  props: { store: Object },
  methods: {
    save: function() {
      /* We pass the store but we're not using it right now, we're using FormData */ 
      if (this.store.id == -1) { addStore(this.store); }
      else { saveStore(this.store); }
    },
    close: function() {
      hideSidebar();
      thisApp.store = { ...thisApp.newStoreData };
    }
  },
  template: `
  <form id="store_form">
    <input class="UI-hiddeninput" name="store_id" v-model="store.id" readonly />
    <label class="UI-label" for="store_name">Store Name:</label>
    <input class="UI-input" name="store_name" v-model="store.Name" placeholder="Store Name" />
    <label class="UI-label" for="store_slug">Store Slug (short lowercase identifier like 'registration' or 'merch')"</label>
    <input class="UI-input" name="store_slug" v-model="store.StoreSlug" placeholder="slug" />
    <label class="UI-label" for="store_description">Description</label>
    <textarea class="UI-input" v-model="store.Description" name="store_description"/>
    <button class="UI-eventbutton" @click.prevent="save" type="submit">Save</button>
    <button class="UI-redbutton" @click.prevent="close">Close</button>
  </form>  
  `
});

var formApp = null;

function newStore() {
  thisApp.store = { ...thisApp.newStoreData };
  showSidebar('edit_store');
}

function handleErrors(e) {
  hideSidebar();
  alert("Something went wrong");
  location.reload();
}

function addStore() {  
  var storeForm = document.getElementById('store_form');
  var formData = new FormData(storeForm);
  confirmbox(
    'Confiring Store Details',
    'Are these details correct?')
    .then(function() {
      apiRequest('POST', 'stores', formData)
      .then(function() {
        location.reload();
      }).catch(handleErrors);
    }).catch(handleErrors);
}

function saveStore(store) {
  console.log(store);
  var storeForm = document.getElementById('store_form');
  var formData = new FormData(storeForm);
  var id = formData.get('store_id');
  console.log(id);
  confirmbox(
    'Confiring Store Details',
    'Are these details correct?')
    .then(function() {
      apiRequest('PUT', 'stores/' + id, formData)
      .then(function() {        
        location.reload();
      }).catch(handleErrors);
    }).catch(handleErrors);
}

function loadStores() {
  return apiRequest('GET', '/stores')
      .then(function(resp) {
          var json = JSON.parse(resp.responseText);
          thisApp = new Vue({
              el: '#page',
              computed: {
                newStoreData: function() {
                  return { id: -1, Name: 'New Store', StoreSlug: '', Description: '' };
                }
              },
              data: { stores: json.data, store: { ...this.newStoreData } },
          });
      }).catch(handleErrors);
}


document.addEventListener('DOMContentLoaded', loadStores);
