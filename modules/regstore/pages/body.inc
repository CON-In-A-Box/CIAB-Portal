<div id="page" class="UI-cell-row UI-padding UI-maincontent">

  <script src="https://js.stripe.com/v3/"></script>

  <div id="main_content" class="UI-main-content-div">
    <div>Would you like to buy a snowman?</div>
    <button id="checkout-button">Checkout</button>
  </div>

  <script type="text/javascript">
    var stripe = Stripe("<?= $_ENV['STRIPE_API_KEY'] ?>");
    var checkoutButton = document.getElementById('checkout-button');

    checkoutButton.addEventListener('click', function() {
      // Create a new Checkout Session using the server-side endpoint you
      // created in step 3.

      showSpinner();
      apiRequest('POST', '/payment/checkout', 'success=regstore/purchased&session_id={CHECKOUT_SESSION_ID}')
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
    });
  </script>
</div>
