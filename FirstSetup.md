# How to get setup for your very first install and run.

1. Make sure you have this on your box with a web server and you have a database server setup. Ideally this project should be at the document root for the web server. You of course need PHP enabled on your web server.
    1. Note: the apache server needs the headers and php7 modules
    1. Note: php needs the libcurl integration (php7-curl)

1. On your MySQL server make a user, such as `con_concomsi-dev`, and a database, such as `con_concomsi`. Give the user full access to the database. 

1. Use a web client to access the web server. You will get redirected to `http://<host>/configure_system.php`. There you will need to fill out all the information about your configuration. Make sure the database information matches the database you setup in the above step.

1. Once all that is filled in. You should get redirected to `http://<host>/index.php?Function=public`. The updates will proceed automatically.

1. Finally, If you want to fill the database with dummy volunteer data then you can use the php script by loading `http://<host>/test/populate_volunteer.php`
