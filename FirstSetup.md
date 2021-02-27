# How to get setup for your very first install and run.

**IMPORTANT** If you are using Docker, read [Docker.md](Docker.md) instead!

1. Make sure you have this on your box with a web server and you have a database server setup. Ideally this project should be at the document root for the web server. You of course need PHP enabled on your web server.
    1. Note: the apache server needs the headers and php7 modules
    1. Note: php needs the libcurl integration (php7-curl)

1. On your MySQL server make a user, such as `con_concomsi-dev`, and a database, such as `con_concomsi`. Give the user full access to the database.  If you want to take a shortcut there is a script `tools/create_database.sh` that take your database root password and sets up a base database and generates a base .env file.

1. You are going to need [composer](https://getcomposer.org/download/) setup and make sure to do `php composer.phar install` or the command you need to do to get the vendor directory properly populated.

1. Use a web client to access the web server. You will get redirected to `http://<host>/configure_system.php`. There you will need to fill out all the information about your configuration. Make sure the database information matches the database you setup in the above step.

1. Here you can decide if you are connecting to a Neon Instance or not. If you do not fill out all the Neon fields then the CIAB installation will only use the locale database. Giving you an initial empty install.

1. Once all that is filled in. You should get redirected to `http://<host>/index.php?Function=public`. The updates will proceed automatically.

1. If you have a local install with no Neon backend you can fill in a dummy event with people and everything to get started with by loading `http://<host>/test/populate_event.php`

1. Otherwise, If you want to only fill the database with dummy volunteer data then you can use the php script by loading `http://<host>/test/populate_volunteer.php`

# Setup-up automatic Neon imports
The goal is to import NEON data, that we cannot random access read, in a regular interval. There is not an automatic way to set this up. It will require you modifying the crontab for the user where all of this is installed.

To your crontab add this line
`*/5 * * * * php <full path to package>/tools/sync_neon_event_to_db.php 1>/dev/null`

That will then sync the Neon data every 5 minutes. Note that sync_neon_event_to_db.php will not run if it is already running. So you will not have to worry about overwhelming the system resources. But it does mean that worst case is that there is a 5 minute delay between the updates, if an update takes several minutes that will determine what your out-of-date factor will be.
