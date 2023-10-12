/* jshint esversion: 6 */
/* globals apiRequest */

const COLUMNSBASE = [
  {value:'name', title:'Name', type: String},
  {value:'promo', title:'Promotional', type: Number},
  {value:'inventory', title:'Total Inventory', type: Number},
  {value:'value', title:'Value', type: Number},
  {value:'remaining', title:'Remaining', type: Number},
  {value:'limit', title:'Limit', type:Number, unsortable:true}
];

const COLUMNSUSER = [
  {value:'name', title:'Name', type: String},
  {value:'promo', title:'Promotional', type: Number},
  {value:'inventory', title:'Total Inventory', type: Number},
  {value:'value', title:'Value', type: Number},
  {value:'remaining', title:'Remaining', type: Number},
  {value:'limit', title:'Limit', type: Number, unsortable:true},
  {value:'acquired', title:'Acquired', type: Number, unsortable:true}
];

const GROUPSTYLES = [
  'VOL-color-amber', 'VOL-color-aqua', 'VOL-color-brown',
  'VOL-color-cyan', 'VOL-color-indigo', 'VOL-color-khaki',
  'VOL-color-lime', 'VOL-color-orange', 'VOL-color-pink',
  'VOL-color-purple', 'VOL-color-sand',
  'VOL-color-teal', 'VOL-color-yellow', 'VOL-color-deep-purple',
  'VOL-color-deep-orange', 'VOL-color-light-blue',
  'VOL-color-light-grey', 'VOL-color-light-green'
];

const sortPrizes = (items, column, direction) => {
  items.sort((a,b) => {
    let result = (parseFloat(a[column.value]) - parseFloat(b[column.value])) * direction;
    if (column.value === 'remaining') {
      result = ((a.inventory - a.claimed) - (b.inventory - b.claimed)) * direction;
    } else if (column.type === String) {
      result = a[column.value].localeCompare(b[column.value]) * direction;
    }

    if (result === 0.0) {
      if (a.name ===  b.name) {
        return (parseFloat(a.value) - parseFloat(b.value)) * direction;
      }
      return a.name.localeCompare(b.name) * direction;
    }
    return result;
  });
};

const mapEntry = (record, entry, groups) => {
  if (record.id === entry.reward.id) {
    record.acquired = record.acquired ? record.acquired + 1 : 1;

    // Assuming value is actually null
    if (record.reward_group !== null) {
      groups[record.reward_group.id].acquired += 1;
    }
  }
  return record;
};

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
      groups:[],
      loaded: false,
      sortColumn: null,
      decendingSort: true
    }
  },
  beforeUpdate() {
    this.lastGroup = null;
    this.hoursRemaining = this.totalHours - this.hoursSpent;
  },
  methods: {
    load() {
      this.records = [];
      this.claims = [];
      this.groups = [];
      this.decendingSort = true;
      this.loaded = false;

      this.columns = COLUMNSBASE;
      if (this.$parent.userId != null) {
        this.columns = COLUMNSUSER;
      }

      apiRequest('GET', '/volunteer/rewards','max_results=all&sold_out=1')
        .then((response) => {
          const result = JSON.parse(response.responseText);
          let colorIdx = 0;
          result.data.forEach((item) => {
            item['type'] = 'item';
            if (item.reward_group) {
              this.$parent.reward_groups[item.reward_group.id].count += 1;

              if (!(item.reward_group.id in this.groups)) {
                this.groups[item.reward_group.id] = {
                  type: 'group',
                  name: this.$parent.reward_groups[item.reward_group.id].name ?? 'Reward Group #' + item.reward_group.id,
                  count: 1,
                  promo: item.promo,
                  acquired: 0,
                  inventory: parseInt(item.inventory),
                  claimed: parseInt(item.claimed),
                  limit: this.$parent.reward_groups[item.reward_group.id].reward_limit,
                  reward_group: item.reward_group,
                  value: item.value,
                  open: false,
                  valueLow: item.value,
                  valueHigh: item.value,
                  items: [ item ],
                  color: GROUPSTYLES[colorIdx]
                };
                colorIdx += 1;
                this.records.push(this.groups[item.reward_group.id]);
              } else {
                this.groups[item.reward_group.id].count += 1;
                this.groups[item.reward_group.id].inventory += parseInt(item.inventory);
                this.groups[item.reward_group.id].claimed += parseInt(item.claimed);
                this.groups[item.reward_group.id].items.push(item);
                this.groups[item.reward_group.id].valueLow = Math.min(this.groups[item.reward_group.id].valueLow, item.value);
                this.groups[item.reward_group.id].valueHigh = Math.max(this.groups[item.reward_group.id].valueHigh, item.value);
                this.groups[item.reward_group.id].value = this.groups[item.reward_group.id].valueLow;
                if (item.promo != this.groups[item.reward_group.id].promo) {
                  this.groups[item.reward_group.id].promo = null;
                }
              }
            } else {
              this.records.push(item);
            }
          });

          this.sortTable(COLUMNSBASE[3]);

          if (this.$parent.userId != null) {
            apiRequest('GET', '/member/' + this.$parent.userId + '/volunteer/claims', 'max_results=all')
              .then((response) => {
                const result = JSON.parse(response.responseText);
                this.claims = result.data;
                result.data.forEach((entry) => {
                  this.records = this.records.map((record) => { return mapEntry(record, entry, this.groups) });
                  this.groups.forEach((group) => {
                    group.items = group.items.map((record) => { return mapEntry(record, entry, this.groups) });
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
      if (record.type === 'group') {
        this.groups[record.reward_group.id].open = !this.groups[record.reward_group.id].open;
        return;
      }
      if (this.$parent.userId == null && this.$parent.isAdmin) {
        this.$parent.$refs.edprz.show(record);
      }
      else if (this.$parent.userId != null && !this.$parent.isAdmin && this.acquirable(record)) {
        this.$parent.$refs.chkout.addToCheckout(record);
      }
    },
    acquirable(record) {
      if (record['claimed'] >= record['inventory']) {
        return false;
      }
      if (record.reward_group != null &&
        (this.groups[record.reward_group.id].acquired >= record.reward_group.reward_limit)) {
        return false;
      }
      if (record.promo == '1') {
        if (record.reward_group == null && record.acquired > 0) {
          return false;
        }
        if (this.$parent.userId) {
          return (parseFloat(record.value) <= this.totalHours);
        }
      }
      if (this.$parent.userId) {
        return (parseFloat(record.value) < this.hoursRemaining);
      }
      return true;
    },
    printValue(record, column) {
      if (!record || !column) {
        return;
      }

      if (record.type === 'group') {
        return this.printGroupValue(record, column);
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

      if (column.value === 'remaining') {
        return record['inventory'] - record['claimed'];
      }

      if (column.value == 'limit') {
        if (record.reward_group) {
          if (record.reward_group.id != this.lastGroup) {
            this.lastGroup = record.reward_group.id;
            if (this.groups[record.reward_group.id].count <= 1) {
              return record['reward_group'].reward_limit;
            }
          }
          return '|';
        }
        return '∞';
      }

      if (column.value in record) {
        return record[column.value];
      }
      return '';
    },
    getRowStyle(row) {
      var base = '';
      if (row.claimed >= row.inventory) {
        if (!this.$parent.isAdmin && row.type !== 'group') {
          return 'VOL-color-red UI-disabled';
        }
        return 'VOL-color-red'
      }

      if (this.$parent.userId && !this.acquirable(row) && row.type !== 'group') {
        base = 'UI-disabled';
      }
      if (row.reward_group != null) {
        base = `${base} ${this.groups[row.reward_group.id].color}`;
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
    },
    sortTable(column) {
      if (column.unsortable) {
        return;
      }
      if (column.value === this.sortColumn) {
        this.decendingSort = !this.decendingSort;
      } else {
        this.decendingSort = false;
      }

      const sortDirection = this.decendingSort ? -1.0 : 1.0;
      this.sortColumn = column.value;

      sortPrizes(this.records, column, sortDirection);
      this.groups.forEach((group) => {
        sortPrizes(group.items, column, sortDirection);
      });
    },
    printGroupValue(record, column) {
      if (column.value === 'value') {
        if (this.groups[record.reward_group.id].valueLow === this.groups[record.reward_group.id].valueHigh) {
          return this.$parent.printHours(this.groups[record.reward_group.id].valueLow);
        }
        return `<${this.$parent.printHours(this.groups[record.reward_group.id].valueLow)} -- \
          ${this.$parent.printHours(this.groups[record.reward_group.id].valueHigh)}>`;
      }

      if (column.value === 'promo') {
        if (this.groups[record.reward_group.id].promo !== null) {
          if (parseInt(record.promo) === 1) {
            return 'yes';
          }
          return 'no';
        }
        return 'varies';
      }

      if (column.value === 'inventory') {
        return `Click to ${this.groups[record.reward_group.id].open ? 'close' : 'open'}`;
      }

      if (column.value === 'remaining') {
        return record['inventory'] - record['claimed'];
      }

      if (column.value == 'limit') {
        return this.groups[record.reward_group.id].limit;
      }

      if (column.value in this.groups[record.reward_group.id] &&
        this.groups[record.reward_group.id][column.value] !== null) {
        return this.groups[record.reward_group.id][column.value];
      }

      return '---';
    },
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
            <div class="UI-table-cell"></div>
            <div v-for="c in columns" class="UI-table-cell" @click="sortTable(c)">
              <template v-if="sortColumn === c.value">
                <span v-if="decendingSort">
                  <em class="fas fa-arrow-down"></em>
                </span>
                <span v-else>
                  <em class="fas fa-arrow-up"></em>
                </span>
              </template>
              {{c.title}}
            </div>
          </div>
          <template v-if="records" v-for="r in records">
            <div class="UI-table-row" :class="getRowStyle(r)" >
              <template v-if="r != null &&
                ((r.type === 'item' && (!hideSoldOut || r.claimed < r.inventory)) || r.type !== 'item')">
                  <div class="UI-table-cell"  @click="clicked(r)">
                    <div v-if="acquirable(r) === false">
                      <em class="fas fa-times"></em>
                    </div>
                    <div v-else-if="r.type === 'group' && r.open">
                      <em class="w3-left fas fa-level-down-alt fa-flip-horizontal"></em>
                    </div>
                    <div v-else-if="r.type === 'group' && !r.open">
                      <em class="w3-left fas fa-caret-down"></em>
                    </div>
                    <div v-else>
                      <em class="far fa-circle"></em>
                    </div>
                  </div>
                  <div v-for="c in columns"
                    class="UI-table-cell" @click="clicked(r)">{{printValue(r, c)}}
                  </div>
              </template>
            </div>
            <template v-if="r.type === 'group' && r.open">
              <div  v-for="gr in r.items" class="UI-table-row" :class="getRowStyle(r)" >
                <div v-if="acquirable(r) === false">
                  <em class="fas fa-times"></em>
                </div>
                <div v-else>
                  <em class="far fa-circle"></em>
                </div>
                <div v-for="c in columns"
                  class="UI-table-cell" @click="clicked(gr)">{{printValue(gr, c)}}
                </div>
              </div>
            </template>
          </template>
        </div>
      </div>
      <div class='UI-tablefooter event-color-secondary'>&nbsp;</div>
    </div>
  </div>
  `
}
