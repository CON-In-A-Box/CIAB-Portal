<?php
  use Monolog\Logger;
  use Stripe\Stripe;

  if (!array_key_exists('session_id', $_REQUEST))
  {
    echo "<h1>YOU SHOULD NOT BE HERE. THE FERRETS WILL EAT YOUR FACE";
    return;
  }

  \Stripe\Stripe::setApiKey($_ENV['STRIPE_PRIVATE_KEY']);

  $session = \Stripe\Checkout\Session::retrieve($_REQUEST['session_id']);
  $customer = \Stripe\Customer::retrieve($session->customer);
?>

<img src="/images/angry-ferret.jpg" />
<h1>CONGRATULATIONS <?= $customer->name ?></h1>
<h2>YOU PURCHASED AN ANGRY FERRET! YAY!</h2>
<h3>Checkout was <?= $_REQUEST['session_id'] ?></h3>
