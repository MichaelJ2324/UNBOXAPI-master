UNBOX API
=========

API Management, Testing, and Documentation.

Dev Environment:

1. Pull down this Repo to some working folder
- Repo does not contain Core FuelPHP files, so, edit your code and push it to your server for testing, overwriting the stock FuelPHP App and Public directories

Hurray you can now edit the code....now to setup your webserver to actually use and test it

Web Server:
Prereqs:
PHP 5.4 (for now)

1. In WebServer directory install FuelPHP
- curl get.fuelphp.com/oil | sh
- oil create [directory]

2. Copy/Push the above Repo to this folder, overwrite fuel/app and public directories

3. In fuel/app directory
- composer.phar update
--- Only needed after initial push of App directory
--- This will pull required PHP Libraries used by UNBOX API

4. Configure install.php file in fuel/app/config/
- Setup database creds and database name

5. Install Database
- oil r Unbox:install
- oil r Unbox:setupForeignKeys
- oil r Unbox:seed

6. Go to your localhost and view the site, a Login page should prompt you



