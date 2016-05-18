# Slim Framework 3 Url shortening

Use this url shortening application to quickly setup and start working on a new Slim Framework 3/url shortening application. This application uses the latest Slim 3 with the PHP-View template renderer. It also uses the Monolog logger.

This s application was built for Composer. This makes setting up a new Slim Framework application quick and easy.

## Install the Application

Run this command from the directory in which you want to install your new Slim Framework application.

    php composer.phar create-project slim/url_shortening [my-app-name]

Replace `[my-app-name]` with the desired directory name for your new application. You'll want to:

* Point your virtual host document root to your new application's `public/` directory.
* Ensure `logs/` is web writeable.

