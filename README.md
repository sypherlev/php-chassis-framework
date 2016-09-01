# The Chassis Framework

Chassis is a microframework that's designed to be dangerously flexible. It uses FastRoute and dotENV to do the initial request bootstrapping, and after that, it's all you, baby. You build whatever Frankenstein business logic you need to get things done.

The only caveat is that you need to use namespaces and proper object-oriented programming. If this is an issue for you, then you are going to hate Chassis and should probably leave now while you're still having a good day.

## Installation

* Download it or clone it here.
* Set your web root to the /web folder.
* Generate a new .env file from .env_sample

## The various bits of the framework

Chassis is composed of two main parts - the /src folder, where all the magical framework crap lives, and the /app folder, where YOUR magical crap lives.

You've also got the /migrations folder, which has a really basic migration tool. There's one migration in there right now that'll make an admin user record, if you need to get going quickly.

The /web folder has all the front-end goodness, like your JS and CSS assets, and your HTML templates.

## Setup

* Open your .env file.
* Change the APP_NAME to whatever.
* Change the APP_NAMESPACE to match the PS-4 autoload for the /app folder in composer.json. This is super important because it makes everything go.
* **Seriously, do NOT forget this or none of the routing will work.**
* If you're feeling silly, you can leave them both at 'MyApp\\\\'.