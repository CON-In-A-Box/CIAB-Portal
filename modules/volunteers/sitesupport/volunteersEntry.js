/*
 * Javacript for the Volunteer Hour Entry
 */

/* jshint esversion: 6 */
/* globals apiRequest, Vue */

import lookupUser from '../../../sitesupport/lookupUser.js'

var volApp = Vue.createApp({
  created() {
    console.log('load volunteer hours pane');
    this.setEndDate();
    apiRequest('GET', '/department','max_results=all')
      .then(this.gotDepartments);
    apiRequest('GET', '/member')
      .then(this.gotMember);
    apiRequest('GET', '/permissions/generic/volunteers/post/admin')
      .then(this.gotAdmin);
    const queryString = window.location.search
    const urlParams = new URLSearchParams(queryString);
    this.member = urlParams.get('memberId');
    if (this.member == '') {
      this.member = null;
    }
    if (this.endHour < 12) {
      this.endAmPm = 'AM';
    } else {
      this.endAmPm = 'PM';
      this.endHour %= 12;
    }
    this.error = false;
  },
  data() {
    return {
      member: null,
      actualHours: 1,
      actualMinutes: 0,
      endDate: null,
      endHour: null,
      endMinutes: null,
      endAmPm: null,
      timeModifier: 1,
      authorizedBy: null,
      enteredBy: null,
      departmentWorked: null,
      departmentsHighlight: null,
      departments: [],
      message: null,
      admin: false,
      error: false,
      totalHours: '1 hour',
      actualHoursWorked: 0.0,
      timeInvalid: false,
      volPast: null,
      lookupMessage: null,
      lookupError: false,
      volunteerName: 'a Volunteer'
    }
  },
  methods: {
    gotDepartments(response) {
      const result = JSON.parse(response.responseText);
      result.data.forEach((entry) => {
        this.departments[entry.id] = entry;
      });
      this.departmentWorked =  this.departments[Number(Object.keys(this.departments)[0])].name;
    },
    gotMember(response) {
      const result = JSON.parse(response.responseText);
      this.enteredBy = result.id;
      this.authorizedBy = result.id;
    },
    gotAdmin(response) {
      const result = JSON.parse(response.responseText);
      this.admin = result.data[0].allowed;
    },
    async submitForm() {
      this.message = null;
      var request = 'member=' + this.member;
      request += '&department=' + this.departmentWorked;
      if (this.actualMinutes > 0) {
        request += '&hours=' + (this.actualHours + (this.actualMinutes / 60)).toString();
      } else {
        request += '&hours=' + this.actualHours;
      }
      var hours = parseInt(this.endHour);
      if (this.endAmPm == 'PM' && hours < 12) {
        hours += 12;
      }
      request += '&end=' + this.endDate + 'T' + hours + ':' + ('0' + this.endMinutes).slice(-2) + ':00';
      request += '&authorizer=' + this.authorizedBy;
      request += '&enterer=' + this.enteredBy;
      request += '&modifier=' + this.timeModifier;
      apiRequest('POST', '/volunteer/hours', request)
        .then((response) => {
          console.log(response.responseText);
          this.error = false;
          this.message = 'Hours Recorded';
        })
        .catch((error) => {
          if (error instanceof Error) { throw error; }
          this.message = 'Hour Recording Failed!!  Error: ' + error.responseText;
          this.error = true;
        });
    },
    setEndDate() {
      const today = new Date();
      this.endDate = today.getFullYear() + '-' +
                     ('0' + (today.getMonth() + 1)).slice(-2) +
                     '-' + ('0' + today.getDate()).slice(-2);
      this.endHour = today.getHours();
      this.endMinutes = (Math.round(today.getMinutes() / 15) * 15) % 60;
    },
    calculateHours() {
      var creditHours = 0;
      var creditMinutes = this.actualHours * this.timeModifier * 60 +
                          this.actualMinutes * this.timeModifier;

      var r = Math.floor(creditMinutes / 60);
      if (r > 0) {
        creditHours += r;
        creditMinutes = creditMinutes % 60;
      }

      var tag = ' hours';
      var mtag = ' minutes';
      if (creditHours == 1) {
        tag = ' hour';
      }
      if (creditMinutes == 1) {
        mtag = ' minute';
      }
      this.totalHours = '';
      if (creditHours > 0) {
        this.totalHours = creditHours + tag;
      }
      if (creditMinutes > 0) {
        this.totalHours += ' ' + creditMinutes + mtag ;
      }
      this.actualHoursWorked = Number(this.actualHours) + Number(this.actualMinutes) / 60;
      this.checkHours();
    },
    resetForm() {
      this.$refs.lookup.clear();
      this.lookupMessage = null;
      this.departmentsHighlight = null;
      this.message = null;
      this.actualHours = 1;
      this.actualMinutes = 0;
      this.setEndDate();
      this.modifier = 1;
      this.departmentWorked =  this.departments[Number(Object.keys(this.departments)[0])].name;
      this.timeInvalid = false;
      this.lookupError = false;
      this.volunteerName = 'a Volunteer';
    },
    handleResult(userLookup, response) {
      userLookup.possibleMembers = null;
      this.timeInvalid = false;
      this.message = null;

      this.volunteerName = response['First Name'] + ' ' + response['Last Name'];
      var uid = response.Id;
      if (response.ConCom && response.ConCom.length > 0) {
        userLookup.markFailure();
        this.lookupMessage = this.volunteerName + ' is on ConCom (' + uid + ')';
        this.lookupError = true;
        this.departmentsHighlight = null;
      } else {
        userLookup.clearFailure();
        userLookup.set(uid);
        this.lookupMessage = this.volunteerName + ' (' + uid + ')';
        this.lookupError = false;
        this.departmentsHighlight = null;
        this.member = uid;

        if ('volunteer' in response) {
          for (var i = 0, len = response.volunteer.length; i < len; i++) {
            var dept = response.volunteer[i];
            if (this.departmentsHighlight == null) {
              this.departmentsHighlight = new Array(dept['Department ID']);
            } else if (!this.departmentsHighlight.includes(dept['Department ID'])) {
              this.departmentsHighlight.push(dept['Department ID']);
            }
          }
        }
        if ('volunteer' in response) {
          this.volPast = response.volunteer;
          this.checkHours();
        }
      }
    },
    onFail(userLookup, target, resp, name, code) {
      userLookup.markFailure();
      this.member = null;
      this.volunteerName = 'a Volunteer';

      if (code == 404) {
        this.lookupMessage = name + ' not found.';
        this.lookupError = true;
      }
      else if (code == 409) {
        this.lookupMessage = 'There are too many matches.'
        this.lookupError = true;
      }
      else {
        this.lookupMessage = name + ' invalid name lookup. (' + code + ')';
        this.lookupError = true;
      }
    },
    formatTime(time) {
      var days = ['Sunday','Monday','Tuesday','Wednesday','Thursday','Friday',
        'Saturday'];
      var pmam = null;
      if (time.getHours() >= 12) {
        pmam = ' PM';
      } else {
        pmam = ' AM';
      }
      var hours = time.getHours() % 12;
      if (hours === 0) {
        hours = 12;
      }
      var min = time.getMinutes();
      if (min < 10) {
        min = '0' + min;
      } else {
        min = min.toString();
      }

      return (days[time.getDay()] + ' ' + hours.toString() + ':' + min + pmam);
    },
    buildEndDate() {
      var str = this.endDate + ' ' + this.endHour + ':' + this.endMinutes + ':00 ' + this.endAmPm;
      return new Date(str);
    },
    checkHours() {
      this.timeInvalid = false;
      this.message = null;

      if (this.volPast && this.volPast.length) {
        var hours = Number(this.actualHours);
        var mins  = Number(this.actualMinutes);

        var newEnd = this.buildEndDate();
        var newBegin = this.buildEndDate();
        newBegin.setHours(newBegin.getHours() - hours);
        newBegin.setMinutes(newBegin.getMinutes() - mins);

        for (var i = 0; i < this.volPast.length; i++) {
          var _shift = this.volPast[i];
          var _end = new Date(_shift['End Date Time']);
          var _begin = new Date(_shift['End Date Time']);
          _begin.setHours(_begin.getHours() - Math.floor(_shift['Actual Hours']));
          _begin.setMinutes(_begin.getMinutes() -
            Math.floor((_shift['Actual Hours'] % 1) * 60));

          var  gBegin = (newBegin > _begin) ? newBegin : _begin;
          var  lEnd = (newEnd < _end) ? newEnd : _end;

          if (gBegin < lEnd) {
            this.timeInvalid = true;
            this.message = 'Overlapping with ' + _shift['Department Worked'] +
                           ' ( ' + this.formatTime(_begin) + ' - ' + this.formatTime(_end) + ' ) ';
            break;
          }
        }
      }
    }
  }
});

volApp.component('lookup-user', lookupUser);
volApp.mount('#vol-entry');

export default volApp;
