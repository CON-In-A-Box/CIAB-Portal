/*
 * Base function to lookup artshow prints
 */
/* jshint esversion: 6 */

import lookupCommon from './lookupCommon.js';

export default {
  extends: lookupCommon,
  created() {
    this.lookupUri = 'artshow/print/';
    this.searchReference = 'fndprnt'
  },
  template:`
  <div class="UI-bar">
    <label class="UI-padding UI-bar-item">{{prompt}}</label>
    <div class="UI-bar">
      <input class="UI-input UI-bar-item UI-padding" @change="handleChanged"
        @keydown="handleKeydown" placeholder="(Id)"
        required="" autocomplete="off"  v-model="id">
      <button type="button" class="icon-barcode button-scan UI-lookup-user-button"
        @click="handleBarcodeClick">
      </button>
      <div class="UI-half UI-bar-item UI-container">
        <find-print class="UI-bar-item" :ref="searchReference" :target="id" hide-input @result="foundResult"></find-print>
      </div>
      <span class="UI-bar-item" :class="messageClass" >{{message}}</span>
    </div>
  </div>
  `
}
