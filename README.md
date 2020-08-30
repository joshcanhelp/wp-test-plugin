# WordPress Test Plugin

This is an installable sample plugin for showing running tests in a WordPress environment. See [the introduction post](https://www.joshcanhelp.com/wordpress-unit-testing-tactics/) for more details about how this is used.

## Getting Started

The purpose of this plugin is to show example tests against functional code. I used the [WP-CLI unit test helper](https://make.wordpress.org/cli/handbook/misc/plugin-unit-tests/) to generate what I needed and made changes from there. 

In order to run tests, you'll need a locally running database server (I used Brew for this following [this guide](https://getgrav.org/blog/macos-catalina-apache-mysql-vhost-apc)). Using WP-CLI link above, look for the command `bin/install-wp-tests.sh` and follow the instructions to get the environment up and running. I **always** have issues getting this running from the start so give yourself some time (and grace) if it's your first time through the process!

Once that's working, the rest is Composer:

```bash
$ composer install
Loading composer repositories with package information
Updating dependencies (including require-dev)
Installing lots of stuff ...

$ composer test
> "vendor/bin/phpunit"

......................                                            22 / 22 (100%)

Time: 1.3 seconds, Memory: 28.00 MB

OK (22 tests, 70 assertions)
```