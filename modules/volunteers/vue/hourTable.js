/* jshint esversion: 6 */
/* globals apiRequest, showSpinner, hideSpinner */

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
  },
  emits: [
    'hourChange'
  ],
  mounted() {
    this.load();
  },
  created() {
  },
  data() {
    return {
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
    load() {
      this.records = null;
      this.totalHours = 0;
      this.totalSpentHours = 0;
      this.hours = [];
      this.totalVolunteers = null;

      showSpinner();
      this.summeryColumns = [
        {value:'name', title:'Department Worked', source:null},

        {value:'entry_count', title:'Total Entries', source:this.hours},
        {value:'volunteer_count', title:'Total Volunteers', source:this.hours},
        {value:'total_hours', title:'Total Hours', source: this.hours}
      ];
      this.detailColumns = [
        {value:'name', title:'Department Worked', source:null},
        {value:'end', title:'Ended', source: this.hours, type: 'Date'},
        {value:'hours', title:'Hours', source: this.hours}
      ];

      apiRequest('GET', '/department','max_results=all')
        .then((response) => {
          const result = JSON.parse(response.responseText);
          this.departments = result.data;
          this.departments.sort((a,b) => (a.name > b.name) ? 1 : -1);

          if (this.$parent.userId != null) {
            this.columns = this.detailColumns;
            this.records = this.hours;
            apiRequest('GET', '/member/' + this.$parent.userId + '/volunteer/hours','max_results=all')
              .then((response) => {
                const result = JSON.parse(response.responseText);
                result.data.forEach((entry) => {
                  this.hours.push(entry);
                })
                this.hours.sort((a,b) => (a.department.name > b.department.name) ? 1 : -1);
                this.totalHours = result.total_hours;
              })
              .finally(() => {
                this.totalSpentHours = 0;
                if (this.footer && this.$parent.userId != null) {
                  apiRequest('GET', '/member/' + this.$parent.userId + '/volunteer/claims/summary','max_results=all')
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
          if (column.value == 'total_hours' || column.value == 'hours') {
            return this.printHours(this.hours[record['id']][column.value]);
          } else {
            return this.hours[record['id']][column.value];
          }
        }
      } else {
        if (column.value == 'total_hours' || column.value == 'hours') {
          return this.printHours(record[column.value]);
        } else {
          if (column.type == 'Date') {
            const constructedDate = new Date(record[column.value]);
            return constructedDate.toLocaleString();
          } else {
            return record[column.value];
          }
        }
      }

      return 0;
    }
  },
  template: `
  <div class="UI-container UI-center event-color-primary" :style="styles">
    <div class="UI-stripedtable">
      <div class='UI-tabletitle'>{{title}}
        <span v-if="$parent.userData"> ({{$parent.userData.first_name}}  {{$parent.userData.last_name}})</span>
      </div>
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
