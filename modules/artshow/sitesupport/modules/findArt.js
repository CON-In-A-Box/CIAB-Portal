/*
 * Search for an art piece
 */
/* jshint esversion: 6 */
/* globals apiRequest, showSpinner, hideSpinner*/

export default {
  props: {
    target: {
      type: String,
      default: null
    },
    hideInput: {
      type: Boolean,
      default: false
    },
    partialLabel: {
      type: String,
      default: 'Partial Match'
    },
    findButtonLabel: {
      type: String,
      default: null
    },
    findButtonIcon: {
      type: String,
      default: 'fas fa-search'
    },
  },
  emits: [
    'result'
  ],
  created() {
  },
  mounted() {
  },
  data() {
    return {
      searchTarget: null,
      results: null,
      partial: false,
    }
  },
  methods: {
    search() {
      var query = 'q=';
      if (this.target) {
        query += this.target;
      } else {
        query += this.searchTarget;
      }
      query += '&from=all';
      if (this.partial) {
        query += '&partial=true';
      }
      this.results = null;
      showSpinner();
      apiRequest('GET', '/artshow/art/find', query)
        .then((result) => {
          var response = JSON.parse(result.responseText);
          this.results = response.data;
        })
        .finally(() => {
          hideSpinner();
        });
    },
    generateArtistName(artist) {
      if (artist.company_name_on_sheet == '1') {
        return artist.company_name;
      }
      return artist.member.first_name + ' ' + artist.member.last_name;
    },
    clickTarget(item) {
      this.$emit('result', item);
    },
    close() {
      this.results = null;
    },
  },
  template:`
  <div class="UI-container UI-bar">
    <div v-if="target == null && !hideInput" class='UI-bar-item UI-container'>
      <label class='UI-label'>Search String</label>
      <input class="UI-input" v-model='searchTarget'>
    </div>
    <div class='UI-bar-item UI-container'>
      <button type="button" class="UI-input UI-eventbutton" @click="search">
        <em v-if="findButtonIcon" :class="findButtonIcon"></em>
        <span v-if="findButtonIcon && findButtonLabel">&nbsp;</span>
        <span v-if="findButtonLabel">{{findButtonLabel}}</span>
      </button>
    </div>
    <div class='UI-bar-item UI-container'>
      <input type="checkbox" class="UI-checkbox" v-model=partial>
      <span class="UI-label">&nbsp;{{partialLabel}}</span>
    </div>
  </div>
  <div v-if="results && results.length>0" class="UI-lookup-user-dropdown">
    <div v-for="r in results" class="UI-bar-item UI-button" @click="clickTarget(r)">
      "{{r.name}}" a '{{r.art_type}}' piece by {{generateArtistName(r.artist)}}
    </div>
  </div>
  `
}
