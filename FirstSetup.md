# How to get setup for your very first install and run.

1. Make sure you have this on your box with a web server and you have a database server setup. Ideally this project should be at the document root for the web server. You of course need PHP enabled on your web server.
    1. Note: the apache server needs the headers and php7 modules
    1. Note: php needs the libcurl integration (php7-curl)

1. On your MySQL server make a user `con_concomsi-dev`, and a database `con_concomsi`. Give the user full access to the database. If you want to do other username and database names you can but it requires more modifications to `.ht_meetingsignin_config.php`.

1. Copy `.ht_meetingsignin_config.php-EXAMPLE` to `.ht_meetingsignin_config.php` and edit the contents. Make sure the DBHOST is correct and DBPASS is the password for the DBUSER on the database.
    1. This config file should be set with permissions so that other users can't read your database credential while still letting the webserver read the file

1. Edit `data/DBSeed/Configuration.sql`
    1. Ensure your Neon account id in in the comma seperated list of `ADMINACCOUNTS`
    1. Ensure your `NEONKEY` and `NEONID` are correct for the Neon you are accessing

1. Use a web client to access the web server. You should get redirected to `http://<host>/index.php?Function=public`. Log in with your Neon ID.

	***There is a bug at present***. The site will *THINK* it has updated from Neon but it has not. (Aric Stewart is working on a fix)

1.  You can blank out the `DBSchemaVersion` configuration value to force a Neon update:

	> UPDATE con_concomsi.Configuration SET Value = '' WHERE Configuration.Field = 'DBSchemaVersion';

	Now you revisit most any page on the website and you will get a message about importing the data from Neon. Note: This will take a while!!  You should now be fully functional. It is best to visit the `concom` list to make sure that looks like the list you would expect.

1. Finally, If you want to fill the database with dummy volunteer data then you can use the php script by loading `http://<host>/test/populate_volunteer.php`
