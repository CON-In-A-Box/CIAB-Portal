/* jshint esversion: 6 */
/* globals showSidebar, hideSidebar, basicBackendRequest, CSVReport */

export default {
  props: {
    base: {
      type: String,
      default: 'generated_report_div'
    },
    reportListURI: {
      type: String,
      default: 'availableReports=1'
    },
    reportURI: {
      type: String,
      default: 'report',
    },
    title: {
      type: String,
      default: 'Generate CSV Report'
    },
    target: String,
    closeFunction: String,
    reportDisplay: String
  },
  created() {
    if (this.target) {
      basicBackendRequest('GET', this.target, this.reportListURI,
        (response) => {
          var data = JSON.parse(response.response);
          data.forEach((entry) => {
            this.reportList.push(entry);
          });
          this.selectedReport = this.reportList[0];
        });
    }
  },
  data() {
    return {
      reportList: [],
      selectedReport: null
    }
  },
  methods: {
    generateReport() {
      var args = '&' + this.reportURI + '=' + this.selectedReport;
      window.location = 'index.php?Function=' + this.target + args;
    },
    viewReport() {
      var args = this.reportURI + '=' + this.selectedReport;
      basicBackendRequest('GET', this.target, args,
        (response) => {
          CSVReport.build(response.responseText,
            { base: this.reportDisplay});
        });
    },
    open() {
      showSidebar(this.base);
    },
    close() {
      if (this.closeFunction) {
        var fn = window[this.closeFunction];
        if (typeof fn === 'function') {
          return fn.apply();
        }
      }
      return hideSidebar();
    }
  },
  template: `
    <div :id="base" class="UI-fixed UI-sidebar-hidden">
      <div class="UI-center">
        <h2 class="UI-red">{{title}}</h2>
      </div>
      <div class="UI-center">
        <label class="UI-label">
            Report:
        </label>
        <select class="UI-padding UI-select" v-model="selectedReport">
        <option v-for="item in reportList" :value="item">{{item}}</option>
        </select>
      </div>
      <div class="UI-center">
        <button class="UI-eventbutton" @click="generateReport">
          Download .CSV
        </button>
        <button v-if="reportDisplay" class="UI-eventbutton UI-margin" @click="viewReport">
          View Report
        </button>
        <span v-else>&nbsp;</span>
        <button class="UI-redbutton" @click="close">
          Close
        </button>
      </div>
    </div>
  `
}
