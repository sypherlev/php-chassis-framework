# The Chassis Framework

* [Setup](https://github.com/sypherlev/php-chassis-framework/blob/master/docs/Setup.md)
* [Migration](https://github.com/sypherlev/php-chassis-framework/blob/master/docs/Migration.md)
* [The framework's basic layout](https://github.com/sypherlev/php-chassis-framework/blob/master/docs/FrameworkBits.md)
* [The database](https://github.com/sypherlev/php-chassis-framework/blob/master/docs/Database.md)
* [Web routing and CLI](https://github.com/sypherlev/php-chassis-framework/blob/master/docs/Routing.md)
* [Actions, the service locator, and middleware](https://github.com/sypherlev/php-chassis-framework/blob/master/docs/Actions.md)
* [Response types](https://github.com/sypherlev/php-chassis-framework/blob/master/docs/Responses.md)
* [Example Request-Response cycle](https://github.com/sypherlev/php-chassis-framework/blob/master/docs/MyLayout.md)
* [Future features to add](https://github.com/sypherlev/php-chassis-framework/blob/master/docs/Future.md)

Chassis is a microframework/collection-of-stuff-loosely-held-together-with-string that's designed to be dangerously flexible. It will not hold your hand, or prevent you from making horrible choices, or stop you from writing shitty code. It will get requests from the web or command line into your business domain, and do something with the data that comes out, and otherwise stay out of your way. It uses FastRoute and dotENV to do the initial request bootstrapping, and after that, it's all you, baby. You build whatever Frankenstein code you need to get things done.

It was designed with the following in mind:
* Swap out all the things if needed
* Load only what is required
* Handle web and command line together
* Separation of concerns is easy

It was designed as a data-processing rapid development framework that can be rearranged on the fly, so every part of it after the initial request bootstrapping is modular.

*Note: if you're not building large data-processing applications with evolving project requirements, then you're probably better off getting something like Laravel or CakePHP. Chassis can be used for CRUD work, but it's not really designed with that in mind, or for beginner developers in general.*

It uses the bare minimum of code to wire together some common packages to handle web and command line requests.

It has the following out of the box:

* A router wrapper around FastRoute
* A .env config file
* Stuff to handle incoming requests
* Five basic response types: API, Email, File, CLI, and Web
* A very simple service locator
* Basic middleware implementation
* Basic Logger class
* The Blueprint query builder + `bin/architect` to generate data files

It does not have the following out of the box:

* An ORM
* PSR-7 compliance
* Input Validation
* Anything to do with encryption
* Probably a bunch more stuff

If you want these features, you'll have to add them yourself.

Use it at your own risk. It's still a work in progress.
