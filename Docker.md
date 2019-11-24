# How to get setup to use Docker from the source tree

The goal here is to try to get a development environment on a machine that may not be configured for php or apache. By using docker we can create containers for all those running pieces and modifications can be made to the code base that are then directly reflected in the running instance.

#Setting up Docker

1. Make sure you have [docker](https://docker.com)  installed for your platform.

1. You are going to need [composer](https://getcomposer.org/download/) setup and make sure to do `php composer.phar install` or the command you need to do to get the vendor directory properly populated.

1. You can now start docker running the command:  `./docker_instance.sh up`. Give it a few minutes to initialize and it will have the instance at "localhost:8080". If you are knowledgable about docker you can modify the `docker-compose.yml` file to change the port.

1. Once you visit <http://localhost:8080> and get a login screen you need to do the first time configuration. Visit <http://localhost:8080/configure_system.php> and that will get things setup. _DO NOT CHANGE_ any of the prepopulated database values. *ALSO PLEASE NOTE:* The `configure_system.php` redirect will not happen automatically like in normal bare-metal first time runs.

1. Now you have configured an admin email address with a password. You can log into the image using that email address and password and you have a functioning base install of Con-in-a-box!

1. If you have a local install with no Neon backend you can fill in a dummy event with people and everything to get started with by loading <http://localhost:8080/test/populate_event.php>

1. If you want to fill the database with dummy volunteer data then you can use the php script by loading <http://localhost:8080/test/populate_volunteer.php>

1. When you are done and want to bring docker down you can use the command `./docker_instance.sh down`

1. If you are a docker poweruser feel freee to add other `docker-compose` parameters to the `docker_instance.sh` commands and they should be passed through. 

1. When using docker, mail will be delivered to an ephemeral service called `mailcatcher`. You can see what mailcatcher has caught at <http://localhost:1080>.
