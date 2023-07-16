# Con In A Box

Starting as a random collection of tools to help administrate a Midwest fan run science fiction and fantasy convention, [CONvergence](http://www.convergence-con.org/), CIAB is evolving to be a multi-faceted web portal for convention administration. 

The goal is to have a fairly generic and skin-able web portal that can be used for a variety of conventions to aid in a number of administrative tasks as well as be useful to the event attendee to get information about their account.

The project is still young and we had the first parts of it in actual usage for the 2018 convention year and expanding from there. 

Additionally the push to make the project generic enough for multiple convention usage is also a work in progress. However it informs the design decisions made. 

# Overview

CIAB is presently designed to run on a apache web server accessing a [mySQL](https://www.mysql.com/) and [Neon CRM](https://www.neoncrm.com/) back ends.  The CRM is not required and new CRM back end engines are in the plan to be written.  The system operates cleanly with no CRM as well.

The vision is to have a core set of functionality and then optional modules that can be enabled/disabled or replaced based on the needs of the given event.

# CIAB Core

The core of the system focuses on allowing members to log into the system and view the information about their account as well as register new accounts in the system. It also provides the framework to load the modules to expand functionality.  This presently includes:

* User Login
* Display / Edit profile information
* Register a new account

# CIAB Modules

A number of optional modules are also being written to aid in a number of other tasks at the event. The goal is for these modules to be easy to enable or disable as any given events needs. Also there should be a way for an event to write its own custom modules for the framework.

* **Administrative Module (modules/admin)**

This module is a generic administrative module, a sort of dumping ground to put various tools that the site admin may need or desire to keep the site tuned up and running.

* **concom (modules/concom)**

This module is focused around the display and administration of a group of staff in charge of running the event. Members can be added to departments and given positions in those departments. Additionally due to presence in a given department other functionality on the CIAB portal can be enabled or disabled. 

* **registration (modules/registration)**

The registration module is focused on what members are registered for what events and the display of information around those registrations. 

* **volunteers (modules/volunteers)**

At present the most extensively fleshed out module. The goal is to have a pair of tools to help staff at a convention track and properly reward members who volunteer at the event. This includes a way for all staff at the event to enter hours for attendees of the event as well as a master terminal for the people in charge of the volunteer department to be able to track all those hours and give proper rewards to the attendees who have volunteered hours. 

* **Google Documents (modules/documents)**

This module will allow you to hook up a google drive folder and have the contents of that drive displayed to all the ConCom members. This is very helpful in sharing minutes or reports with the rest of the staff. 

# Assets / Theming / SCSS

We know all events and Conventions do not look the same. So in order for this package to be useful across events there needs to be a way to theme the colors and images to match the convention. We have only begun here but the plans are to expand this as we have time and there is demand. 

CIAB makes use of SCSS to help dynamically build style sheets to allow for skinning of the site.

At present in `scss/event.scss` is where the even specific styles are generated. Values we expect to be changing from event to event should be imported there. Main colors for the event are present in the configuration, and can be set on the configure_system page. 

There is a basic asset library engine included in CIAB to try to manage long-lasting but event or convention specific graphical assets. This asset manager is accessed in the `Administrator->Assets` menu option.

There are important templates for the back end located in `api/src/Templates`. These Templates are meant to be as generic as possible in order to be used across events. But it is very possible to modify the language and format of the template for a given event. 

# First Steps

For a step-by-step overview of process of getting a local instance of the project setup look at [FirstSetup.md](FirstSetup.md). 

If you would like and are able to use docker then you should read [Docker.md](Docker.md). That will give you a quick and easy way to get started in that way.

# Code Checking

In order to try to keep the code as orderly as possible we are having GitHub lint code as it is submitted. The goal is to help catch errors and keep some coding consistency.

For JavaScript files we are using [ESLint](https://eslint.org/). This should be relatively straight forward for to configure to be used on your platform.

For PHP file we are using [PHPCS](https://github.com/squizlabs/PHP_CodeSniffer). Again pretty easy to get installed on most platforms.

There are rules defined for each tool as well as simple wrapper tools in the tools/ directory. Expect that these tools will be run against any code submitted and it will have to pass.

It recommend you make use of some githooks that are also defined in .githooks which you can use by running this command `git config core.hooksPath .githooks`

The code is also monitored by [SonarCloud](https://sonarcloud.io/dashboard?id=CON-In-A-Box_CIAB-Portal). That is a great place to find first issues or things to work on. Once the code has become clean there it will start becoming a manditory check for pull requests as well. 

# CRM backend

* There is a facility to specify a CRM backend to track users and events. This is likely in connection to a registration system. The current system defines [Neon CRM](https://www.neoncrm.com/) but new backends can be written as integration with other CRM systems are required.

* The system operates fully without any CRM defined as a fully self standing system as well. 

# Next Steps

* Additionally we want to continue to expand the cross-event structure to try to encourage other events to use this tool. We believe it can be very power and helpful for many events and would like our work to be welcome in as many places as it fits.  This likely involves making it easier to skin the site and pull out many of the CONvergence assumptions and making them configurable based on the event. 

# How you can help!

We are always happy for help from other developers. Please contact us and we are happy to try to help provide direction or even submit patches and we will review them. 

Additional information about contributing can be found in our [CONTRIBUTING docs](./doc/CONTRIBUTING.md).

***Thank you for your interest***
