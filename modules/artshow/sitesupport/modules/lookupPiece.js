/*
 * Base function to lookup artshow piece
 */
/* jshint esversion: 6 */

import lookupCommon from './lookupCommon.js';

export default {
  extends: lookupCommon,
  created() {
    this.lookupUri = 'artshow/art/piece/';
    this.searchReference = 'fndart'
  },
  template:`
  <div class="UI-bar">
    <label class="UI-margin UI-padding UI-bar-item">{{prompt}}</label>
    <div class="UI-bar UI-bar-item UI-container">
      <div class="UI-half UI-bar UI-bar-item UI-container">
        <input class="UI-input UI-bar-item UI-padding" @change="handleChanged"
          @keydown="handleKeydown" placeholder="(Id)"
          required="" autocomplete="off"  v-model="id">
        <button type="button" class="icon-barcode button-scan UI-lookup-user-button UI-bar-item"
          @click="handleBarcodeClick">
        </button>
      </div>
      <div class="UI-half UI-bar-item UI-container">
        <find-art class="UI-bar-item" :ref="searchReference" :target="id" hide-input @result="foundResult"></find-art>
      </div>
    </div>
    <span class="UI-bar-item" :class="messageClass" >{{message}}</span>
  </div>
  `
}
