/* jshint esversion: 6 */
/* globals apiRequest, Vue */

Vue.component('badge-menu-pane', {
  props: {
    open: Boolean,
    eventName: String,
    tickets: Object
  },
  computed: {
    canCheckIn() {
      return this.open && this.tickets.canCheckIn.length > 0;
    },
    canPickUp() {
      return this.open && this.tickets.haveBoardingPass.length > 0;
    },
    pickedUp() {
      return this.open && this.tickets.pickedUp.length > 0;
    }
  },
  template: `
  <div class="UI-container event-color-primary">
    <div class="REG-title event-color-primary">Membership Menu</div>
    <div class="UI-container">
      <div class="UI-table UI-table-padded">
        <div v-if="canCheckIn" class="UI-table-row UI-white">
          <div class="UI-table-cell"><a href="/index.php?Function=registration/checkin">Check in for {{eventName}}</a></div>
        </div>
        <div v-if="canPickUp" class="UI-table-row UI-white">
          <div class="UI-table-cell"><a href="/index.php?Function=registration/boarding">Pick up badge(s) for {{eventName}}</a></div>
        </div>
        <div v-if="pickedUp" class="UI-table-row UI-white">
          <div class="UI-table-cell"><a href="/index.php?Function=registration/lost">Report lost badge(s) for {{eventName}}</a></div>
        </div>
        <div class="UI-table-row UI-white">
        <div class="UI-table-cell"><a href="/index.php?Function=registration/manage">Manage registration(s)</a></div>
        </div>
        <div class="UI-table-row UI-white">
          <div class="UI-table-cell"><a href="/index.php?Function=profile">Manage Account</a></div>
        </div>
      </div>
    </div>
    <div class="event-color-primary">&nbsp;</div>
  </div>
  `
});

var app = new Vue({
  el: '#componentland',
  created() {
    this.getCheckinStatus()
      .then(this.getEventInfo)
      .then(this.getTicketInfo)
      .catch((error) => {
        console.log('Unable to determine open/closed status of checkin');
        console.log(error);
      });
  },
  data() {
    return {
      open: false,
      event: null,
      eventName: '',
      tickets: {
        canCheckIn: [],
        haveBoardingPass: [],
        pickedUp: []
      }
    };
  },
  methods: {
    getCheckinStatus() {
      return apiRequest('GET', 'registration/open', null)
        .then((response) => {
          const result = JSON.parse(response.responseText);
          this.open = result.open;
          this.event = result.event;
        });
    },

    getEventInfo() {
      apiRequest('GET', `event/${this.event}`, null)
        .then((response) => {
          const result = JSON.parse(response.responseText);
          this.eventName = result.name;
        });
    },

    getTicketInfo() {
      apiRequest('GET', 'registration/ticket/list', null)
        .then((response) => {
          const result = JSON.parse(response.responseText);
          result.data.forEach((ticket) => {
            if (ticket.badges_picked_up > 0) {
              this.tickets.pickedUp.push(ticket);
            } else if (ticket.boarding_pass_generated) {
              this.tickets.haveBoardingPass.push(ticket);
            } else {
              this.tickets.canCheckIn.push(ticket);
            }
          });
        });
    }
  }
});

export default app;
