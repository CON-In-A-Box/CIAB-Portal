/*
 * Javacript for the Volunteer Hour Panel
 */

/* jshint esversion: 6 */
/* globals apiRequest, Vue */

var volApp = Vue.createApp({
  el: '#vol-div',
  created() {
    console.log('load volunteer hours pane');
    apiRequest('GET', '/member/current/volunteer/hours/summary','max_results=all')
      .then(this.volHours)
      .catch((error) => {
        console.log(error.responseText);
        if (error instanceof Error) { throw error; }
      });
    apiRequest('GET', '/member/current/volunteer/claims','max_results=all')
      .then(this.volPrizes)
      .catch((error) => {
        console.log(error.responseText);
        if (error instanceof Error) { throw error; }
      });
  },
  data() {
    return {
      open: false,
      totalHours: 0,
      totalSpent: 0,
      items: [],
      entries: []
    };
  },
  methods: {
    volHours(response) {
      const result = JSON.parse(response.responseText);
      this.totalHours = 0.0;
      result.data.forEach((entry) => {
        this.entries.push(entry);
      });
      this.totalHours = result.total_hours;
      this.open = true;
    },
    volPrizes(response) {
      this.totalSpent = 0.0;
      const result = JSON.parse(response.responseText);
      result.data.forEach((item) => {
        var done = false;
        for (var already in this.items) {
          if (this.items[already].reward.reward.id == item.reward.id) {
            this.items[already].count += 1;
            done = true;
          }
        }
        if (!done) {
          var entry = {reward: item, count: 1};
          this.items.push(entry);
        }
        if (!item.reward.promo) {
          this.totalSpent += parseFloat(item.reward.value);
        }
      });
      this.open = true;
    }
  }
});

volApp.component('vol-pane', {
  props: {
    open: Boolean,
    totalHours: Number,
    totalSpent: Number,
    entries: Array,
    items: Array
  },
  template: `
  <div v-if="open" class="UI-container event-color-primary">
    <div class="UI-container UI-tabletitle">Current Volunteer Status</div>
    <div class="UI-table UI-table-padded">
      <div class="UI-table-row UI-center event-color-primary">
        <div class="UI-table-cell">Total of <span id=total>{{totalHours}}</span>
        <span v-if="totalHours > 1"> hours </span>
        <span v-else> hour </span>
        earned</div>
      </div>
      <div v-for="entry in entries" class="UI-table-row UI-center UI-white">
        <div class="UI-table-cell">{{entry.department.name}} {{entry.total_hours}}
        <span v-if="entry.total_hours > 1">hours</span>
        <span v-else>hour</span>
        </div>
      </div>
    </div>
    <div class="UI-container UI-center event-color-secondary">Rewards claimed so far this year</div>
    <div class="UI-table UI-table-padded">
      <div v-if="items.length > 0" v-for="item in items" class="UI-table-row UI-center UI-white">
        <div class="UI-table-cell">{{item.reward.reward.name}}
        <span v-if="item.count > 1">({{item.count}})</span>
        </div>
      </div>
      <div v-else class="UI-table-row UI-center UI-white">
        <div class="UI-table-cell">&nbsp;</div>
      </div>
      <div class="UI-table-row UI-center event-color-seconday">
        <div class="UI-table-cell">Total of <span id=total>{{totalSpent}}</span>
        <span v-if="totalSpent > 1"> hours </span>
        <span v-else> hour </span>
        spent</div>
      </div>
    </div>
  </div>
`
});

volApp.mount('#vol-div');

export default volApp;
