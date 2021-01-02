<div id="page" class="UI-cell-row UI-padding UI-maincontent">

  <script src="https://js.stripe.com/v3/" integrity="sha384-MOhPwl1HbGAiHgNwYj5lY9IjAl4YVf49QVnnJOrisNPkSJPFivf3cJKp43dbP1Sv" crossorigin='anonymous'></script>
  <script src="sitesupport/store/purchase.js"></script>

  <div id="main_content" class="UI-main-content-div">
    <form id="regPurchase">
      <div>Would you like to buy a snowman?</div>
      <input type="hidden" name="successFunction" value="store/purchased" />
      <input type="hidden" name="storeSlug" value="reg" />
      <input type="submit" value="Checkout" />
    </form>
  </div>

  <script type="text/javascript">
    const regPurchase = document.getElementById('regPurchase');
    const purchaseSystemKey = "<?= $_ENV['STRIPE_API_KEY'] ?>"
    regPurchase.addEventListener('submit', (e) => purchaseSubmitHook(e, regPurchase, purchaseSystemKey));
  </script>
</div>
