# Con In A Box

Starting as a random collection of tools to help administrate a Midwest fan run science fiction and fantasy convention, [CONvergence](http://www.convergence-con.org/), CIAB is evolving to be a multi-faceted web portal for convention administration. 

The goal is to have a fairly generic and skin-able web portal that can be used for a variety of conventions to aid in a number of administrative tasks as well as be useful to the event attendee to get information about their account.

The project is still young and we hope to have the first parts of it in actual usage for the 2018 convention year and expanding from there. 

Additionally the push to make the project generic enough for multiple convention usage is also a work in progress. However it informs the design decisions made. 

# Overview

CIAB is presently designed to run on a apache web server accessing a [mySQL](https://www.mysql.com/).

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

# Theming

We know all events and Conventions do not look the same. So in order for this package to be useful across events there needs to be a way to theme the colors and images to match the convention. We have only begun here but the plans are to expand this as we have time and there is demand. 

At present in `sitesupport/event.css` are the values we expect to be changing from event to event. Changing the values there should be reflected across the web pages. 

# First Steps

For a fairly technical overview of process of getting an instance of the project setup look at [FirstSetup.md](FirstSetup.md). 


# What about Neon?

Previous versions of CIAB connected to a [Neon](https://www.neoncrm.com/) back end.
We moved away from Neon to rely completely on mySQL for all data storage after the 2018 convention. Before this time Neon was used for member/attendee information.

# Next Steps

* The present primary goal is to have the first pieces of CIAB finished and put into production for the CONvergence 2018 event in July of 2018. This would primarily be the volunteers module and the core system.

* Additionally we want to continue to expand the cross-event structure to try to encourage other events to use this tool. We believe it can be very power and helpful for many events and would like our work to be welcome in as many places as it fits.  This likely involves making it easier to skin the site and pull out many of the CONvergence assumptions and making them configurable based on the event.

# How you can help!

We are always happy for help from other developers. Please contact us and we are happy to try to help provide direction or even submit patches and we will review them. 

***Thank you for your interest***
