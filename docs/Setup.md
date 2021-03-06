# Setup

## Requirements

* postfix (if you intend to use the built-in EmailResponse)
* PHP 7.0

## Installation

* Download it or clone it from here.
* Set your web root to the /public folder.
* Run `composer install`.
* Copy a new .env file from .env_sample, adding your own parameters.
* Bootstrap the database using the migration tool.
* If you're using the built-in EmailResponse, [install Postfix](https://www.digitalocean.com/community/tutorials/how-to-install-and-configure-postfix-on-ubuntu-16-04).

Open the .env file.

Set `devmode=true` if you need it to be, otherwise leave it at `devmode=false`.

Add your database details in the following format, if you're using Blueprint:

    prefix_engine=mysql
    prefix_host=localhost
    prefix_username=user
    prefix_password=pass
    prefix_dbname=dbname
    
The prefix is used to identify the database. See the .env_sample for how it should look. You can add any number of databases in here as long as they all have different prefixes.

You can add any environment variables here; see the docs for [dotENV](https://github.com/vlucas/phpdotenv) for more information. Retrieve them with `getenv()`.

## Logging

The framework has a very simple Logger class. There is one all-purpose `log()` method which can accept a string, array, or Exception. Anything else will be cast to a string before it attempts to create a log entry.

There are two constants, TO_FILE or TO_SLACK, which determine where data is sent. Use `setLogType()` to switch between them. Unless specified in the constructor or using this method, the Logger will default to TO_FILE.

### File Logging

In the .env file, specify a log location and name:

    logfile=/var/www/example/error.log
    
The default location and name is in the site root, and called `chassis.log`.

### Slack Logging

In the .env file, specify the Slack channel and webhook:

    slack_webhook="https://hooks.slack.com/services/XXXXXXXXX/XXXXXXXXX/XXXXXXXXXXXXXXXXXXXXXXXX"
    slack_channel="#channelname"

