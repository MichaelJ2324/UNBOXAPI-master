UNBOX API
=========

API Management, Testing, and Documentation.

Prerequisites:
* PHP 5.4
* MySQL
* Composer
  * Please follow the installation guide [here](https://getcomposer.org/doc/00-intro.md#installation-nix) if you do not have composer installed already.

Steps to Install:

Fork UNBOXAPI-master repo.
```
$ cd <webroot>
$ git clone https://github.com/<username>/UNBOXAPI-master.git
$ cd UNBOXAPI-master/
```

Change to the alpha branch
```
$ git checkout alpha
```

Ignore any local file permission changes
```
$ git config --global core.filemode false
```

Add Upstream for easy fetch capabilities
```
$ git remote add upstream https://github.com/MichaelJ2324/UNBOXAPI-master.git
```

Install Fuel
```
$ cd ..
$ curl get.fuelphp.com/oil | sh
$ oil create UNBOXAPI
$ cd UNBOXAPI
$ cp -R ../UNBOXAPI-master/fuel/app fuel/app
$ cp -R ../UNBOXAPI-master/public/ public/
```

Install Fuel Dependencies
```
$ cd fuel/app/
$ composer update
```

Configure Settings
```
$ cd config/
$ vi install.php
    //Update hostname, port, database, username, and password to match your local mysql settings.
```

Navigate back to the Root UNBOX directory
```
$ cd ../../..
```

Reset File Permissions
```
$ sudo chmod -R 775 ../UNBOXAPI
$ sudo chown -R www:www ../UNBOXAPI
```

Install the Database
```
$ oil r Unbox:install
$ oil r Unbox:setupForeignKeys
$ oil r Unbox:seed
$ oil r Unbox:seed all null true true
```
Navigate to where UNBOX is installed, http://localhost/UNBOX/public/
 * Login credentials
  * Username: unbox_demo
  * Password: unbox
