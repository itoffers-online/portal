# Development

Before you start make sure all dependencies are on place. Vagrant is recommended for local development and
all instructions in this readme are prepared for it.  

```
$ vagrant ssh
$ cd /var/www/hireinsocial
$ composer install
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

* [Unit](tests/HireInSocial/Tests/Application/Unit)
* [Unit](tests/HireInSocial/Tests/Infrastructure/Unit) (infrastructure)
* [Integration](tests/HireInSocial/Tests/Application/Integration) (application)
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
