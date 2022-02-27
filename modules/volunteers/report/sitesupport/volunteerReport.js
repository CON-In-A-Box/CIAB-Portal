/* jshint esversion: 6 */
/* globals apiRequest,  hideSidebar, basicBackendRequest, CSVReport, showSidebar */

export default {
  props: {
    reportDisplay: String,
    haveEvent: Boolean,
    header: String
  },
  created() {
    if (this.haveEvent) {
      apiRequest('GET', '/event/current', null)
        .then(this.gotCurrentEvent);
      apiRequest('GET', '/event', 'max_result=all')
        .then(this.gotEvent);
    }
  },
  data() {
    return {
      minHours: 30,
      selectedEvent: null,
      events: [],
      uri: 'volunteers/report'
    }
  },
  methods: {
    gotEvent(response) {
      const result = JSON.parse(response.responseText);
      result.data.forEach((entry) => {
        this.events.push(entry);
      });
    },
    gotCurrentEvent(response) {
      const result = JSON.parse(response.responseText);
      this.selectedEvent = result.id;
    },
    generateHourReport() {
      var target = this.uri + '&min_hour=' + this.minHours;
      if (this.selectedEvent) {
        target = target + '&event=' + this.selectedEvent;
      }
      window.location = 'index.php?Function=' + target;
    },
    viewReport() {
      var target = 'min_hour=' + this.minHours;
      if (this.selectedEvent) {
        target = target + '&event=' + this.selectedEvent;
      }
      basicBackendRequest('GET', this.uri, target,
        (response) => {
          CSVReport.build(response.responseText,
            { base: this.reportDisplay});
        });
    },
    hideSidebar() {
      hideSidebar();
    },
    open() {
      showSidebar('hour_report_div');
    }
  },
  template: `
  <div class='UI-sidebar-hidden UI-fixed' id='hour_report_div'>
    <div class='UI-center'>
      <h2 class='UI-red'>{{header}}</h2>
    </div>
    <div v-if="haveEvent" class='UI-center'>
         <label class='UI-label'>
         Event:</label>
        <select class='UI-select UI-padding' v-model="selectedEvent">
        <option v-for="(item, index) in events" :value="item.id">
          {{item.name}}</option>
        </select>
    </div>
    <div class='UI-center'>
      <label class='UI-label'>
      Minimum hours:</label>
      <input class="UI-input" type="number" v-model="minHours">
    </div>
    <div class='UI-center'>
      <button v-if="reportDisplay !== undefined" class='UI-eventbutton' @click='viewReport'>
         View Report
      </button>
      &nbsp;
      <button class='UI-eventbutton' @click='generateHourReport'>
         Generate Report &lt;CSV&gt;
      </button>
      <button class='UI-redbutton' @click='hideSidebar'>
        Close
      </button>
    </div>
  </div>
`
};
