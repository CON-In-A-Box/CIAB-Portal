# TLDR about Vue.js

So, first of all, Vue.js has some pretty good docco to begin with. At some point, if you're going to implement Vue components, go read that.

## Why Vue.js

Vue.js is an extremely lightweight Javascript framework, which can be used "casually" to include component-and-template behaviour on an ad hoc basis, or can be used like more heavy-weight frameworks like Ember to present a Single Page Application. Because we already have lots of prior art we don't necessarily want to reimplement all at once, we're going the former route. We're going to use it where it makes sense (and especially on newer stuff) and not worry about retrofitting unless we're already tearing a page apart to do new things to it.

## How does it work

For our purposes, Vue.js basically replaces a lot of manual DOM manipulation we were using to generate HTML in response to Javascript actions. It will also do a fair amount of the API interaction, which means that the frontend will increasingly be HTML and Javascript and not as much PHP. This is not dissing PHP, which will still be our backend and still be there for when we need it for front-end things, too.

There are really only two classes to worry about with Vue: `Vue` and `VueComponent`. 

A `Vue` represents a controller over part of the DOM. If this were a true single page app, one Vue object would basically be the root of everything for the whole application. Since we're doing it page by page, we have the root Vue tie in to some high-level part of our structure. Conveniently, all our pages start with a `<div id="page">` block. 

A `Vue` has at least two properties associated with it: `el` is the element the Vue is anchored to; `data` is whatever data you're displaying.

If you look at `modules/stores/admin/sitesupport/stores.js` (we'll be using that as our example), at the function `loadStores`, you'll see what we're doing is first making our API call, and then, as part of the promise resolution, we create our Vue app with the data we retrieved. The response from that becomes the `stores` data attribute. We also add a singluar `store` attribute to the data, and pre-fill it with what we want the form to show for example text.

In the `modules/stores/admin/pages/body.inc` template, you'll see we use two tags that are not standard HTML. These are named after components defined in `stores.js`. Search that file for `store-form` and `store-list` and you'll see where those components are defined.

Each one is defined in terms of properties they receive as arguments, and an HTML + Handlebars template. These template can, itself, contain references to components, which allows the whole structure to be composable.

In this case, `store-form` is self-contained, but `store-list` is defined in nested terms of several more components. This was done because HTML tables are apparently weird, although now that I understand Vue better, I can also simplify some of that, I'm pretty sure. What's more, a later iteration will probably abstract it out to a common component anyone can use to generate tables that look like our tables. After that, all you have to do to change the look of ALL our tables, even if the CSS class-names change, is change the common templates.

So, `store-list` is defined in terms of `store-list-head` and `store-list-body`, which gets passed the `stores` property.

`store-list-body` is defined in terms of `store-list-item`. The invocation here is a little odd to look at, so let's unpack it:

* `v-for` sets up a loop. Unlike most languages, Vue's template language embeds the loop command in the invocation of what you want to repeat, rather than around it. So this means we're going to get as many `store-list-item` as we have `stores`. 
* After that, we bind the specific properties each `store-list-item` will get -- a `key` (which is a Vue convention, it can be anything as long as it's unique within the invocation), and the individual `store`.

Each `store-list-item` is defined as a table row with its cells, which are filled in using Handlebars syntax. This component also defines two methods, which get bound to the two actions possible on each component: `Manage Products` and `Edit`. The one takes you to a page where you can define products for a store (not done yet); the other let's you edit the properties of the store itself (name, slug, description).

The two other key functions in this file, `addStore` and `saveStore`, are similar to functions in other modules for these functions, but now use `FormData` to parse the form, rather than explicitly pulling things out of the DOM.
