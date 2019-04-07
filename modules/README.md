# Optional Modules

Here is where the core built-in CIAB modules live and are developed. The design goal is to have each module as independent as possible however it is understood that it there will likely be dependancies between modules. So it will have to be handled that if a given module is disabled the other modules continue to function.

A module is enabled by default and can be disabled in the `Configuration` table in backend database. The database can define a `DISABLEDMODULES` field where the value will be a comma separate list of modules that are disabled in this instance. 

Within the module there are a few files of special importance. None of these file are required and only need to be present if the module need to have the behavior described.
 
* `init.inc`:  This file will be loaded with every web page processed. Here you can define functions that are being 'exported', for lack of a better term.
* `pages/panes.inc`: This is loaded when the main page is loaded. It describes all the panes to be added to the main screen. If your module displays panes on the main screen you will want to add the full function name to the `$homepage_panes` array. It is generally recommend you use name spacing. So something like `registration\panes\badges` for the function `badges` in the namespace `registration\panes`
* `pages/menubar.inc`: This is the file loaded when the menubar is being constructed. Generally if you implement this you will be wanting to add something to `$admin_menus` as that is what is used to construct the menubar at the top of the pages. If you use this then you will also have to define the html templates that are loaded when this menu item is selected:
 * `pages/pre.inc, pages/head.inc, pages/body.inc`: These are the HTML templates to generate the page that is loaded when the menu item is selected from the menubar. They are not required though if the `body.inc` file is missing then the user will get a blank page when they select the menu bar item. 

# Theming / SCSS

Modules can individually define their own styles and SCSS templates. If a file exists in the modules path: `scss/styles.scss` then that file will be loaded _INSTEAD_ of the default one for the site. This does mean when writing a module specific `scss/styles.scss` you will want to `@import "common";` to have a common feel with the rest of the site.