# System Setup

[↩️ back](/README.md)

Before you start make sure all dependencies are on place. Vagrant is recommended environment for local development and
all instructions in this readme are prepared for it.  

```
$ vagrant ssh
$ cd /var/www/itoffers/php/itoffers
$ composer install
$ composer assets:install
```

## Configuration

Check [.env.dist file](/php/itoffers/.env.dist) and create your own local copy:

```
$ cp /var/www/itoffers/php/itoffers/.env.dist /var/www/itoffers/php/itoffers/.env
```
 
## User interface

User interface is using [Bootstrap 4.0](https://getbootstrap.com/). Most javascripts are written directly in twig, in special
block `javascripts`, just like custom css are using `stylesheets` block. 

In order to rebuild bootstrap use 

```
composer assets:build
``` 
 
## Command Line Interface

All available CLI commands are defined in `composer.json` file, in scripts section.

### Test project

This command will execute whole testsuite at once, it will also clear all caches first.
Environment variables used in tests are defined in [.env.test](.env.test) file.

```
$ composer test
```

To check PHPUnit code coverage run:

```
$ composer test:coverage:unit
```

And open `./var/coverage/index.html` in your browser.

Test types:

* [Unit](tests/ITOffers/Tests/Application/Unit)
* [Unit](tests/ITOffers/Tests/Infrastructure/Unit) (infrastructure)
* [Integration](tests/ITOffers/Tests/Application/Integration) (application)
* [Functional](tests/App/Tests/Functional)


### Build project

This command prepares tar archive with latest version of the project. 
It will also optimize autoloads and remove dev dependencies first.
Output is saved to [build](build) folder.

```
$ composer run build
```

### Database

There are few commands you can use to manage database, the one defined in composer is most user frieldny

```
$ composer run db:reset
```

It resets whole database.

There are also few low level commands:

```
$ bin/db-drop
```

Drops all tables in database (this command does not remove the database).
This command is not available in deployment archive.

```
$ bin/db migrations:migrate
```

Execute all migrations


### Symfony Cache Clear

Cleans symfony cache (twig, routing, translation etc)

```
$ bin/symfony cache:clear
```

### Azure blob storage

In order to use azure blob storage in development you can set it up manually or through terraform
like described in [dev terraform readme](../../terraform/README.md).

---
[↩️ back](/README.md)