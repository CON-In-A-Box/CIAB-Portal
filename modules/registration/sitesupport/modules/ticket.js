/*
 * Javacript for the Registration tickets
 */

/* jshint browser: true */
/* jshint -W097 */
/* jshint esversion: 6 */
/* globals apiRequest, showSpinner, hideSpinner, alertbox */

export class RegTicket {

  constructor(data) {
    this.data = data;
  }

  display(e) {
    /* Override me! */
  }

  emailMe() {
    showSpinner();
    apiRequest('PUT', 'registration/ticket/' + this.data.id + '/email','')
      .then(function() {
        hideSpinner();
        alertbox('Email sent', 'We have emailed you your boarding pass.<br>' +
                 'Kindly check your email for further instructions.');
      })
      .catch(function(response) {
        hideSpinner();
        if (response instanceof Error) { throw response; }
      });
  }

  pickupBadge() {
    showSpinner();
    apiRequest('PUT', 'registration/ticket/' + this.data.id + '/pickup','')
      .then(function(response) {
        location.assign('/index.php?Function=main');
        hideSpinner();
      })
      .catch(function(response) {
        hideSpinner();
        if (response instanceof Error) { throw response; }
      });
  }

  lostBadge() {
    showSpinner();
    apiRequest('PUT', 'registration/ticket/' + this.data.id + '/lost','')
      .then(function() {
        hideSpinner();
        alertbox('Badge has been re-printed!',
          'Please visit registration to claim your new badge.')
          .then(() => location.assign('/index.php?Function=main'));
      })
      .catch(function(response) {
        hideSpinner();
        if (response instanceof Error) { throw response; }
      });
  }

  updateBadge() {
    showSpinner();
    apiRequest('PUT', 'registration/ticket/' + this.data.id,
      'badge_name=' + this.data.badge_name +
      '&emergency_contact=' + this.data.emergency_contact)
      .then(function() {
        hideSpinner();
        location.reload();
      })
      .catch(function(response) {
        hideSpinner();
        if (response instanceof Error) { throw response; }
      });
  }

}
