/* jshint esversion: 6 */
/* globals apiRequest */

const COLUMNSBASE = [
  {value:'name', title:'Name '},
  {value:'promo', title:'Promotional'},
  {value:'inventory', title:'Total Inventory'},
  {value:'value', title:'Value '},
  {value:'remaining', title:'Remaining'},
  {value:'limit', title:'Limit'}
];

const COLUMNSUSER = [
  {value:'name', title:'Name '},
  {value:'promo', title:'Promotional'},
  {value:'inventory', title:'Total Inventory'},
  {value:'value', title:'Value '},
  {value:'remaining', title:'Remaining'},
  {value:'limit', title:'Limit'},
  {value:'acquired', title:'Acquired'}
];

const GROUPSTYLES = [
  'VOL-color-amber', 'VOL-color-aqua', 'VOL-color-brown',
  'VOL-color-cyan', 'VOL-color-indigo', 'VOL-color-khaki',
  'VOL-color-lime', 'VOL-color-orange', 'VOL-color-pink',
  'VOL-color-purple', 'VOL-color-red', 'VOL-color-sand',
  'VOL-color-teal', 'VOL-color-yellow', 'VOL-color-deep-purple',
  'VOL-color-deep-orange', 'VOL-color-light-blue',
  'VOL-color-light-grey', 'VOL-color-light-green'
];

export default {
  props: {
    styles: {
      type: String,
      default: null
    },
    totalHours: {
      type: Number,
      default: 0
    },
    hoursSpent: {
      type: Number,
      default: 0
    }
  },
  mounted() {
    this.load();
  },
  created() {
  },
  data() {
    return {
      title: 'Gifts',
      columns: null,
      records: [],
      claims: [],
      hideSoldOut: true,
      hoursRemaining: 0,
      unclaimed: null,
      groupCount: [],
      loaded: false,
    }
  },
  beforeUpdate() {
    this.groupIndex = -1;
    this.groupColorId = null;
    this.lastGroup = null;
    this.hoursRemaining = this.totalHours - this.hoursSpent;
  },
  methods: {
    load() {
      this.loaded = false;

      this.columns = COLUMNSBASE;
      if (this.$parent.userId != null) {
        this.columns = COLUMNSUSER;
      }

      apiRequest('GET', '/volunteer/rewards','max_results=all&sold_out=1')
        .then((response) => {
          const result = JSON.parse(response.responseText);
          this.records = result.data;

          this.records.sort((a,b) => {
            if (a.value === b.value) {
              return ((a.name > b.name) ? 1 : -1);
            }
            return ((parseFloat(a.value) > parseFloat(b.value)) ? 1 : -1);
          });

          this.records.sort((a,b) => {
            if (a.reward_group !== null && b.reward_group === null) {return -1;}
            if (b.reward_group !== null && a.reward_group === null) {return 1;}
            if (b.reward_group === null && a.reward_group === null) {return 0;}
            if (parseInt(b.reward_group.id) === parseInt(a.reward_group.id)) {
              if (a.value === b.value) {
                return ((a.name > b.name) ? 1 : -1);
              }
              return (parseFloat(a.value) > parseFloat(b.value)) ? 1 : -1;
            }
            return (parseInt(a.reward_group.id) > parseInt(b.reward_group.id) ? 1 : -1);
          });

          if (this.$parent.userId != null) {
            apiRequest('GET', '/member/' + this.$parent.userId + '/volunteer/claims', 'max_results=all')
              .then((response) => {
                const result = JSON.parse(response.responseText);
                this.claims = result.data;
                result.data.forEach((entry) => {
                  this.records.map((record) => {
                    if (record.id === entry.reward.id) {
                      record.acquired = record.acquired ? record.acquired + 1 : 1;

                      // Assuming value is actually null
                      if (record.reward_group !== null) {
                        this.groupCount[record.reward_group.id] =
                          this.groupCount.includes(record.reward_group.id) ? this.groupCount[record.reward_group.id] + 1 : 1;
                      }
                    }
                    return record;
                  });
                });
                this.loaded = true;
              })
              .catch(() => {
                this.loaded = true;
              });
          } else {
            this.loaded = true;
          }
        })
        .catch((e) => {
          console.log(e);
        });
    },
    clicked(record) {
      if (this.$parent.userId == null && this.$parent.isAdmin) {
        this.$parent.$refs.edprz.show(record);
      }
      else if (this.$parent.userId != null && !this.$parent.isAdmin && this.acquirable(record)) {
        this.$parent.$refs.chkout.addToCheckout(record);
      }
    },
    acquirable(record) {
      if (record.reward_group != null &&
        (this.groupCount[record.reward_group.id] >= record.reward_group.reward_limit)) {
        return false;
      }
      if (record.promo == '1') {
        if (record.reward_group == null && record.acquired > 0) {
          return false;
        }
        return (parseFloat(record.value) <= this.totalHours);
      }
      return (parseFloat(record.value) < this.hoursRemaining);
    },
    printNumber(value) {
      return parseInt(value).toLocaleString('en-US');
    },
    printValue(record, column) {
      if (!record || !column) {
        return;
      }

      if (column.value == 'value') {
        return this.$parent.printHours(record[column.value]);
      }

      if (column.value == 'promo') {
        if (parseInt(record[column.value])) {
          return 'yes';
        }
        return 'no';
      }

      if (column.value == 'remaining') {
        return record['inventory'] - record['claimed'];
      }

      if (column.value == 'limit') {
        if (record.reward_group) {
          if (record.reward_group.id != this.lastGroup) {
            this.lastGroup = record.reward_group.id;
            return record['reward_group'].reward_limit;
          } else {
            return '|';
          }
        }
        return '∞';
      }

      if (column.value in record) {
        return record[column.value];
      }
    },
    getRowStyle(row) {
      var base = '';
      if (row.claimed >= row.inventory) {
        if (!this.$parent.isAdmin) {
          return 'VOL-color-red UI-disabled';
        }
        return 'VOL-color-red'
      }

      if (this.$parent.userId && !this.acquirable(row)) {
        base = 'UI-disabled ';
      }
      if (row.reward_group != null) {
        if (row.reward_group.id != this.groupColorId) {
          this.groupIndex += 1;
          this.groupColorId = row.reward_group.id;
        }
        return base + GROUPSTYLES[this.groupIndex];
      }
      return base;
    },
    getSoldOutLabel() {
      if (this.$parent.userId) {
        return ' Hide volunteer\'s soldout items';
      }
      return ' Hide soldout items';
    },
    doReturn() {
      this.$parent.$refs.psrtn.show(this.claims);
    },
    getUnclaimed() {
      if (!this.$parent.userId) {
        return [];
      }
      if (this.unclaimed == null) {
        this.records.forEach((entry) => {
          if (entry.promo == '1' && entry.claimed < entry.inventory && this.acquirable(entry)) {
            if (this.unclaimed == null) {
              this.unclaimed = [ entry ];
            } else {
              this.unclaimed.push(entry);
            }
          }
        });
      }
      return this.unclaimed;
    },
    getUnclaimedCount() {
      var u = this.getUnclaimed();
      if (u) {
        var nonGroup = 0;
        u.forEach((entry) => {
          if (entry.reward_group == null) {
            nonGroup += 1;
          }
        });
        return [u.length, nonGroup];
      }
      return [0, 1];
    }
  },
  template: `
  <div class="UI-container UI-center event-color-secondary" :style="styles">
    <div class="UI-stripedtable">
      <div class='UI-tabletitle event-color-secondary'>
        <div class="VOL-row-padding">
          <div class="VOL-prize-sold-out">
            <input id='soldoutcheck' type='checkbox' class='VOL-check' v-model="hideSoldOut">
            <label class='UI-label' for='soldoutcheck'>{{getSoldOutLabel()}}</label>
          </div>
          <div class='VOL-prize-header'>
            {{title}}
            <span v-if="$parent.userData"> ({{$parent.userData.first_name}}  {{$parent.userData.last_name}})</span>
          </div>
          <div v-if="$parent.userId != null" class="VOL-prize-return-header">
            <input id='return_items' type='button' class='UI-orangebutton' @click='doReturn' value='Return Items'>
          </div>
          <div v-else class="VOL-prize-return-header"> </div>
        </div>
      </div>
      <div class="UI-padding">
        <div class="UI-table-all">
          <div class="UI-table-row">
            <div v-for="c in columns" class="UI-table-cell">{{c.title}}</div>
          </div>
          <div v-if="records" v-for="r in records" class="UI-table-row" :class="getRowStyle(r)" >
            <div v-if="r != null && (!hideSoldOut || r.claimed < r.inventory)" v-for="c in columns"
              class="UI-table-cell" @click="clicked(r)">{{printValue(r, c)}}</div>
            </div>
          </div>
        </div>
      </div>
    <div class='UI-tablefooter event-color-secondary'>&nbsp;</div>
  </div>
  `
}
