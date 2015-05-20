[![Stories in Ready](https://badge.waffle.io/MichaelJ2324/UNBOX_API.png?label=ready&title=Ready)](https://waffle.io/MichaelJ2324/UNBOX_API)
[![Build Status](https://travis-ci.org/MichaelJ2324/UNBOX_API.svg?branch=master)](https://travis-ci.org/MichaelJ2324/UNBOX_API)
[![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/MichaelJ2324/UNBOX_API/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/MichaelJ2324/UNBOX_API/?branch=master)

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
$ git clone https://github.com/<username>/UNBOX_API.git
$ cd UNBOX_API/
```

[Optional]
Change to a branch
```
$ git checkout [branch]
```

Ignore any local file permission changes
```
$ git config --add core.filemode false
```

Add Upstream for easy fetch capabilities
```
$ git remote add upstream https://github.com/MichaelJ2324/UNBOX_API.git
```

Configure Settings
```
$ vi fuel/app/config/install.php
    //Update hostname, port, database, username, and password to match your local mysql settings.
```

Reset File Permissions
```
$ sudo chmod -R 775 ../UNBOX_API
$ sudo chown -R www:www ../UNBOX_API
```

Don't track changes to db files
```
$ git update-index --assume-unchanged fuel/app/config/db.php
$ git update-index --assume-unchanged fuel/app/config/development/db.php
$ git update-index --assume-unchanged fuel/app/config/install.php
$ git update-index --assume-unchanged fuel/app/config/production/db.php
$ git update-index --assume-unchanged fuel/app/config/staging/db.php
$ git update-index --assume-unchanged fuel/app/config/test/db.php
$ git update-index --assume-unchanged fuel/app/config/unbox.php
```

Install and Seed Data
```
$ composer install
```
Navigate to where UNBOX is installed, http://localhost/UNBOX/public/
 * Login credentials
  * Username: unbox_demo
  * Password: unbox
