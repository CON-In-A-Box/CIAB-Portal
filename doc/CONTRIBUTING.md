# Contributing

Thank you for your interest in contributing to our project!

# Pre-requisites

Before getting started with writing and running code, there are several pre-requisites that must be set up.

## Developing using Windows PC and WSL2 (Windows only)

If you are developing on a Windows PC, it is recommended to use WSL2. You can find detailed instructions on [this Microsoft page](https://learn.microsoft.com/en-us/windows/wsl/install) or [this other Microsoft page](https://learn.microsoft.com/en-us/windows/wsl/setup/environment#set-up-your-linux-username-and-password)

### Set up VSCode for use with WSL2 (Windows only, Optional)

Follow along with the instructions on [this Microsoft page](https://learn.microsoft.com/en-us/windows/wsl/tutorials/wsl-vscode) to install VSCode and get it set up to work with WSL2. It is free to use and offers many extensions to make coding easier.

## Download Docker

Find the appropriate Docker Desktop installation for your OS. You can find the application on [Docker's install page](https://docs.docker.com/get-docker/)

**Note:** If you are installing on Windows for use with WSL2, make sure you have already installed WSL2 so Docker can find it during setup.

## Code

Make sure you can run `git clone` to copy this repository to your machine, preferably using the SSH mechanism.

### Setting up SSH Key

If you need to set up an SSH Key, you can follow [Github's instructions](https://docs.github.com/en/authentication/connecting-to-github-with-ssh/generating-a-new-ssh-key-and-adding-it-to-the-ssh-agent) to do this for your machine.

## Install NVM

There are some parts of this application that depend on `node` and NPM modules, such as our linter. NVM is a tool that allows you to easily manage different versions of `node` on your machine. The [NVM Github Page](https://github.com/nvm-sh/nvm) has detailed information about what it can do.

To quickly get started with this, you can:

1. Open a terminal and paste in the command `curl -o- https://raw.githubusercontent.com/nvm-sh/nvm/v0.39.3/install.sh | bash` which will download and install NVM from their Github repository.
2. Close your terminal and open a new one.
3. Run `nvm --help` to ensure that NVM was installed correctly, you should see the help menu.
4. Run `nvm ls-remote` to see a list of available `node` versions.
5. Run `nvm install <version>` to install a specific version of `node`, for example, `nvm install v18.16.1`.
6. Confirm that node has been installed by running `node --version`.

You can install as many versions of `node` as you would like. Switching between them is as simple as `nvm use <version>`.

# Setup

Now that you have setup WSL2 (if applicable), downloaded and installed Docker Desktop, and pulled down the code, you're ready to complete the next steps to setup your application and start contributing!

## Notes

- While using Docker, if you would like to continue to have just a single terminal open you can add the `--detach` or `-d` parameter to the script in [bin/docker_instance.sh](../bin/docker_instance.sh) on line 7.
  - Doing this should result in a line that looks like `docker-compose -f docker-compose.yml -f docker-compose.phpmyadmin.yml up -d $@;`
- If you are running on Windows with WSL2, you may need to modify the PHP container's Dockerfile found in [docker/php/Dockerfile](../docker/php/Dockerfile) if you encounter errors like "no xdebug release package available"
  - On line 7, you will want to remove the extra commands that have `&&` and the `\` at the end of the line. Your result should look like `RUN apk add $PHPIZE_DEPS`

You will need to create an `.env` file in the project root and add the following:

```
export DBUSER=ciabuser
export DBPASS=ciabpass
export DBROOTPW=deathllama
export DBNAME=ciab
export TEST_DBNAME=ciab-test
export DB_BACKEND=mysqlpdo.inc
```

This file will be used when developing locally and does not need to be checked in. It is required for the application to run successfully.

# Starting Up

You're ready to start the application. It is highly recommended to use Docker while developing as this will be the easiest way to ensure that your environment is ready to go with minimal effort or changes.

Everything below assumes you have made no changes to exposed ports in the `docker-compose` configuration files.

## Docker Containers

You can spin up the Docker containers with Docker Desktop running by using your terminal to navigate to the root directory for this project and running `./bin/docker_instance.sh up`.

You will notice your terminal contains output. It should look like what is described in "CLI Output" below when it is successful.

<details>

<summary>CLI Output</summary>

```
 => [php internal] load build definition from Dockerfile                                                           0.1s
 => => transferring dockerfile: 490B                                                                               0.0s
 => [php internal] load .dockerignore                                                                              0.0s
 => => transferring context: 2B                                                                                    0.0s
 => [php internal] load metadata for docker.io/library/php:7.2.7-fpm-alpine3.7                                     0.9s
 => [php 1/7] FROM docker.io/library/php:7.2.7-fpm-alpine3.7@sha256:21b0cbbbca911423c2fcc5896336cdc6adf61d4181a2b  0.0s
 => [php internal] load build context                                                                              0.0s
 => => transferring context: 55B                                                                                   0.0s
 => CACHED [php 2/7] RUN apk update     && apk upgrade     && apk add git bash msmtp freetype libpng libjpeg-turb  0.0s
 => CACHED [php 3/7] RUN docker-php-ext-install mysqli pdo_mysql                                                   0.0s
 => CACHED [php 4/7] RUN docker-php-ext-install gd                                                                 0.0s
 => CACHED [php 5/7] RUN apk add autoconf   dpkg-dev dpkg   file   g++   gcc   libc-dev   make   pkgconf   re2c    0.0s
 => CACHED [php 6/7] COPY msmtprc /.msmtprc                                                                        0.0s
 => CACHED [php 7/7] COPY php.ini /usr/local/etc/php/php.ini                                                       0.0s
 => [php] exporting to image                                                                                       0.0s
 => => exporting layers                                                                                            0.0s
 => => writing image sha256:f269071b139ccfe5120ec96bea840c5b8d389700336c86532adaf3e82669ee0a                       0.0s
 => => naming to docker.io/library/ciab-portal-php                                                                 0.0s
 => [apache internal] load .dockerignore                                                                           0.0s
 => => transferring context: 2B                                                                                    0.0s
 => [apache internal] load build definition from Dockerfile                                                        0.1s
 => => transferring dockerfile: 471B                                                                               0.0s
 => [apache internal] load metadata for docker.io/library/httpd:2.4.33-alpine                                      0.7s
 => [apache 1/6] FROM docker.io/library/httpd:2.4.33-alpine@sha256:cd4598d3397ed391b8c996d686a3f939cd8e672d31b758  0.0s
 => [apache internal] load build context                                                                           0.0s
 => => transferring context: 38B                                                                                   0.0s
 => CACHED [apache 2/6] RUN apk update;     apk upgrade;                                                           0.0s
 => CACHED [apache 3/6] RUN echo "LoadModule rewrite_module modules/mod_rewrite.so"     > /usr/local/apache2/conf  0.0s
 => CACHED [apache 4/6] RUN echo "Include /usr/local/apache2/conf/rewrite.conf"     >> /usr/local/apache2/conf/ht  0.0s
 => CACHED [apache 5/6] COPY demo.apache.conf /usr/local/apache2/conf/demo.apache.conf                             0.0s
 => CACHED [apache 6/6] RUN echo "Include /usr/local/apache2/conf/demo.apache.conf"     >> /usr/local/apache2/con  0.0s
 => [apache] exporting to image                                                                                    0.0s
 => => exporting layers                                                                                            0.0s
 => => writing image sha256:d380dbbb149b44b19bc15abaa3b39317d2783eea6b890e640ab4be3f8771119f                       0.0s
 => => naming to docker.io/library/ciab-portal-apache                                                              0.0s
[+] Running 11/11
 ✔ Network ciab-portal_frontend         Created                                                                    0.1s
 ✔ Network ciab-portal_backend          Created                                                                    0.1s
 ✔ Network ciab-portal_default          Created                                                                    0.1s
 ✔ Volume "ciab-portal_mysql"           Created                                                                    0.0s
 ✔ Container ciab-portal-mailcatcher-1  Started                                                                   11.2s
 ✔ Container ciab-portal-mysql-1        Started                                                                   10.6s
 ✔ Container ciab-portal-swagger-1      Started                                                                   10.8s
 ✔ Container ciab-portal-composer-1     Started                                                                   10.6s
 ✔ Container ciab-portal-phpmyadmin-1   Started                                                                    2.2s
 ✔ Container ciab-portal-php-1          Started                                                                    2.6s
 ✔ Container ciab-portal-apache-1       Started                                                                    3.1s
```

</details>

## Installing node_modules

To install the linter module for our JS code, from the project root directory in your terminal use `npm install`. This should create a `node_modules` folder with the appropriate dependencies.

## Seeding the Database

You can navigate to `http://locahost:8000` to view a PHPMyAdmin portal that shows your database. You should see `ciab` listed as a database if you used the defaults for `.env` listed above. If you named it something else, confirm that the database exists as expected.

## The Site

You can navigate to `http://localhost:8080`. On your first visit, you should see a page that describes database schema updates that were run and at the bottom you should see a button labeled "Proceed". Clicking "Proceed" should direct you to a login page.

You can now navigate to `http://localhost:8080/configure_system.php` to complete set up for your local development environment. Do not change any of the fields that are pre-filled, but fill in any empty form fields and click "Submit Query". You should be redirected to the login page, and you should be able to login using the email and password that you configured for the admin user on the configuration page.

Once logged in, you should be able to see a page with more options and configurations available. You've successfully set up your first user and you're ready to proceed!

TODO: Additional event and volunteer database population instructions

# Additional Information

- If you'd like to turn off your containers you can do so from the root project directory by using `./bin/docker_instance.sh down`
- If you'd like to remove your containers and any of the data they contain, you can do so from the root project directory by using `./bin/docker_instance.sh nuke` to remove everything. You will need to go through the initial setup steps again when spinning up containers next time.
- There is functionality in this application that manages emails. The `docker-compose` files contain a definition for a service named `mailcatcher`, which will capture these emails so that none are actually sent out. Navigating to `http://localhost:1080` will let you see any emails that have been captured.
