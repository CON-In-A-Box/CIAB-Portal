/*
 * Javacript for the Registration tickets
 */

/* jshint browser: true */
/* jshint -W097 */
/* globals apiRequest, showSpinner, hideSpinner, alertbox */

export class RegTicket {

  constructor(data) {
    this.data = data;
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
      })
  }

  pickupBadge() {
    showSpinner();
    apiRequest('PUT', 'registration/ticket/' + this.data.id + '/pickup','')
      .then(function(response) {
        location.reload();
        hideSpinner();
        console.log(response);
      })
      .catch(function(response) {
        hideSpinner();
        if (response instanceof Error) { throw response; }
      })
  }

  lostBadge() {
    showSpinner();
    apiRequest('PUT', 'registration/ticket/' + this.data.id + '/lost','')
      .then(function() {
        hideSpinner();
        alertbox('Badge has been re-printed!',
          'Please visit registration to claim your new badge.');
      })
      .catch(function(response) {
        hideSpinner();
        if (response instanceof Error) { throw response; }
      })
  }

}
