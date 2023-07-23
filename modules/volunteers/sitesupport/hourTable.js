/* jshint esversion: 6 */
/* globals apiRequest, showSpinner, hideSpinner, userId */

export default {
  props: {
    title:  {
      type: String,
      default: 'Hours'
    },
    footer: {
      type: Boolean,
      default: false
    },
    styles: {
      type: String,
      default: null
    },
    uid: {
      type: String,
      default: null
    }
  },
  emits: [
    'hourChange'
  ],
  mounted() {
    showSpinner();
    this.summeryColumns = [
      {value:'name', title:'Department Worked', source:null},
      {value:'entry_count', title:'Total Entries', source:this.hours},
      {value:'volunteer_count', title:'Total Volunteers', source:this.hours},
      {value:'total_hours', title:'Total Hours', source: this.hours}
    ];
    this.detailColumns = [
      {value:'name', title:'Department Worked', source:null},
      {value:'end', title:'Ended', source: this.hours},
      {value:'hours', title:'Hours', source: this.hours}
    ];

    if (this.uid == null && typeof userId !== 'undefined') {
      this.user = userId;
    } else {
      this.user = this.uid;
    }
    apiRequest('GET', '/department','max_results=all')
      .then((response) => {
        const result = JSON.parse(response.responseText);
        this.departments = [];
        result.data.forEach((entry) => {
          this.departments.push(entry);
        });

        this.departments.sort((a,b) => (a.name > b.name) ? 1 : -1);

        if (this.user != null) {
          this.columns = this.detailColumns;
          this.records = this.hours;
          apiRequest('GET', '/member/' + this.user + '/volunteer/hours','max_results=all')
            .then((response) => {
              const result = JSON.parse(response.responseText);
              result.data.forEach((entry) => {
                this.hours.push(entry);
              })
              this.hours.sort((a,b) => (a.department.name > b.department.name) ? 1 : -1);
              this.totalSpentHours = 0;
              this.totalHours = result.total_hours;
              if (this.footer && this.user != null) {
                apiRequest('GET', '/member/' + this.user + '/volunteer/claims/summary','max_results=all')
                  .then((response) => {
                    const result = JSON.parse(response.responseText);
                    this.totalSpentHours = result.spent_hours;
                  })
                  .finally(() => {
                    hideSpinner();
                    this.$emit('hourChange', this.totalHours, this.totalSpentHours);
                  })
              } else {
                hideSpinner();
                this.$emit('hourChange', this.totalHours, this.totalSpentHours);
              }
            })
        } else {
          this.columns = this.summeryColumns;
          this.records = this.departments;
          apiRequest('GET', '/event/current/volunteer/hours/summary','max_results=all')
            .then((response) => {
              const result = JSON.parse(response.responseText);
              result.data.forEach((entry) => {
                this.hours[entry.department.id] = entry;
              })
              this.totalVolunteers = result.total_volunteer_count;
              this.totalHours = result.total_hours;
              apiRequest('GET', '/event/current/volunteer/claims/summary','max_results=all')
                .then((response) => {
                  const result = JSON.parse(response.responseText);
                  this.totalSpentHours = result.spent_hours;
                })
                .finally(() => {
                  hideSpinner();
                })
            })
            .finally(() => {
              hideSpinner();
            })
        }
      })
  },
  created() {
  },
  data() {
    return {
      user: null,
      columns: null,
      departments: null,
      records: null,
      hours: [],
      totalHours: 0,
      totalVolunteers: null,
      totalSpentHours: 0
    }
  },
  methods: {
    /*eslint no-unused-vars: ["error", { "argsIgnorePattern": "^_" }]*/
    clicked(_record) {},
    printHours(value) {
      return this.$parent.printHours(value);
    },
    printNumber(value) {
      return parseInt(value).toLocaleString('en-US');
    },
    printValue(record, column) {
      if (!record || !column) {
        return;
      }
      if (column.source == null) {
        if (this.records == this.departments) {
          return record[column.value];
        } else {
          return record.department.name;
        }
      }

      if (this.records == this.departments) {
        if (record['id'] in this.hours) {
          return this.hours[record['id']][column.value];
        }
      } else {
        return record[column.value];
      }

      return 0;
    },
  },
  template: `
  <div class="UI-container UI-center event-color-primary" :style="styles">
    <div class="UI-stripedtable">
      <div class='UI-tabletitle'>{{title}}</div>
      <div class="UI-padding">
        <div class="UI-table-all">
          <div class="UI-table-row">
            <div v-for="c in columns" class="UI-table-cell">{{c.title}}</div>
          </div>
          <div v-if="records" v-for="r in records" class="UI-table-row">
            <div v-if="r != null" v-for="c in columns" class="UI-table-cell" @click="clicked(r)">{{printValue(r, c)}}</div>
          </div>
        </div>
      </div>
    </div>
    <div class='UI-tablefooter'>&nbsp;</div>
  </div>
  <div v-if="footer" class='UI-event-sectionbar'>
    <span class='UI-margin'>Total : {{printHours(totalHours)}}</span>
    <span v-if="totalVolunteers != null" class='UI-margin'>Total Volunteers : {{printNumber(totalVolunteers)}}</span>
    <span class='UI-margin'>Total Spent : {{printHours(totalSpentHours)}}</span>
  </div>
  `
}
