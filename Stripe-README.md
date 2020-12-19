# Setup

1. Create a free account with Stripe. Anyone can do this at any time. https://stripe.com/docs/account
2. Once you're logged in to your Stripe dashboard, you'll want to switch it to viewing test data. I believe you'll have to do this each time you log in to see anything you create while experimenting.
3. We already include the necessary library in `composer.json`, but you will probably also want the `stripe-cli` https://stripe.com/docs/stripe-cli#install . Among other things, this will allow you to set up a tunnel so that webhooks can reach you without fuss. (Note: if you are following the advice of the [Windows README](Windows-README.md), then you should follow the LINUX install directions for Stripe-CLI)
4. From the Stripe Dashboard, grab your TEST API keys by clicking on Developers->API keys
5. In your .env file, add two lines:
```
STRIPE_API_KEY=pk_test_whateveryourpublishablekeywas
STRIPE_PRIVATE_KEY=sk_test_whateveryoursecretkeywas
```


# What the heck are webhooks?

Webhooks are just another name for callbacks--and yes, before you ask, I'll explain that, too.

When we initiate an action via Stripe's API, we get an answer back synchronously. However, there are several situations under which information we need will have to come to us asynchronously. Examples include:

* Payment--while we will be showing the form, payment will actually go directly to Stripe and we'll never even see it. But we still need to know it happened.
* Various card events--someone's card expires, for example.
* If we were to introduce any kind of subscription model, any events relating to that.
* If someone changes something at the console, we'd get a notification.

Of course, in order to send those webhooks to us, Stripe needs a URL to post it to. Setting this up for a public server is easy. Setting this up for your home development system is harder. Enter `stripe-cli`, which, among many other things, can sit there and act as a relay.

```
$ stripe listen
```

will listen for webhooks and report on them without taking action. For example, here's what it output when I created a product on my dashboard

```
 stripe listen
> Ready! Your webhook signing secret is <redatcted> (^C to quit)
2020-12-19 13:37:31   --> product.created [evt_1I0BF9GKK5TpEWCXQy9Dk5Vp]
2020-12-19 13:37:31   --> price.created [evt_1I0BF9GKK5TpEWCXO2UtZjmD]
```

In an actual webhook handler, those `evt_...` IDs would be used to make a call to retrieve what actually happened.

```
$ stripe listen --forward-to localhost:8080/api/registration/stripe_webhook
```

will forward webhooks to the webhook handler!

