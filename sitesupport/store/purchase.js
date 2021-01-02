/*
 * Purchase support code
 */

/* jshint browser: true */
/* jshint -W097 */
/* jshint esversion: 6 */

var purchaseSubmitHook = (e, formElem, purchaseSystemKey) => {
  var stripe = Stripe(purchaseSystemKey);
  var formData = new FormData(formElem);
    
  console.log(formElem);
  console.log(formData);

  e.preventDefault();
  showSpinner();

  apiRequest('POST', '/payment/checkout', formData)
  .then(function(response) {
    return JSON.parse(response.responseText);
  })
  .then(function(session) {
    return stripe.redirectToCheckout({ sessionId: session.id });
  })
  .then(function(result) {
    // If `redirectToCheckout` fails due to a browser or network
    // error, you should display the localized error message to your
    // customer using `error.message`.
    if (result.error) {
      alert(result.error.message);
    }
  })
  .catch(function(error) {
    console.error('Error:', error);
  });
};
