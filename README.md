# The Chassis Framework

Chassis is a microframework/collection-of-stuff-loosely-held-together-with-string that's designed to be dangerously flexible. It will not hold your hand, or prevent you from making horrible choices. It will get requests from the web or command line into your business domain, and do something with the data that comes out, and otherwise stay out of your way. It uses FastRoute and dotENV to do the initial request bootstrapping, and after that, it's all you, baby. You build whatever Frankenstein code you need to get things done. 

Chassis was designed for the kind of complex data-driven applications I create, so it's heavily geared towards PHP developers who also manange their own relational databases. If you're not completely comfortable with database management and SQL, this is not the framework for you. 

The database layer is completely decoupled and can be swapped out with anything. It includes the Blueprint extended query builder for interacting with MySQL/MariaDB databases (optional), a migration tool built on top of that (optional), and a set of Request classes to get shit into your domain (not optional), and a set of Response classes that build various responses for output (optional). The EmailResponse class uses PHPMailer to make things go. The WebResponse uses Twig templates.

It uses the bare minimum of code to wire together some common packages to handle web and command line requests.

It has the following out of the box:

* A router wrapper around FastRoute
* A .env config file
* Stuff to handle incoming requests
* Five basic response types: API, Email, File, CLI, and Web (optional)
* A very simple service locator (optional)
* Basic middleware implementation (optional)
* The Blueprint query builder + `bin/architect` to generate data files (optional)

It does not have the following out of the box:

* An ORM
* A logging system
* Input Validation
* Anything to do with encryption
* Probably a bunch more stuff

If you want these features, you'll have to add them yourself.

Use it at your own risk.

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

## The various bits of the framework

Chassis is composed of several folders which broadly conform to the [PHP-PDS Skeleton](https://github.com/php-pds/skeleton).  

You've also got the /migrations folder, which the `bin/chassis` migration tool uses. There's one migration in there right now that'll make a few user tables, if you need to get going quickly. 

If your .env file sets `devmode=true` then an /emails folder will appear with copies of emails sent by the EmailResponse object, and no emails will actually be sent. Using `devmode` will also set Twig to `debug=true`. 

The /public folder has all the front-end goodness, like your JS and CSS assets. The /cache folder is where the Twig cache stuff is stored. The /templates folder contains all the Twig templates.

Inside the /src folder, I've added the following:

* /Common: classes which may be used in a few different places; a catch-all for stuff that doesn't fit easily anywhere else
* /DBAL: data classes, or anything that interacts with the database
* /Domain: the business domain, where most of your app's logic is going to run
* /Middleware: the various middleware classes
* Class MiddlewareCollection: where you can store middleware queues, to be used in Actions later
* Class ObjectCollection: the very basic wrapper around `league/container`
* Class RouteCollection: the routing list

## Setup

Open your .env file.

Set `devmode=true` if you need it to be, otherwise leave it at `devmode=false`.

Add your database details in the following format:

    prefix_engine=mysql
    prefix_host=localhost
    prefix_username=user
    prefix_password=pass
    prefix_dbname=dbname
    
The prefix is used to identify the database. See the .env_sample for how it should look. You can add any number of databases in here as long as they all have different prefixes.

## Migration

**The migration tool is one-way only; no rollbacks are possible unless you add another SQL script file that un-does something in a previous migration.**

The bootstrap.sql file in /migrations has user tables which plug into the Auth system in this sample, but the only one that's really required is the migrations table.

In the project root, run `bin/chassis` to see options for migrations. (You may need to make it executable first.)

## Your first route

All your routes are stored in /src/RouteCollection.php. RouteCollection has one method, the constructor, which contains a list of routes. RouteCollection is basically just an extension of the FastRoute dispatcher that registers all your routes before the framework kicks off. List them all here - follow the examples there if you're not sure - and group them using comments. They all follow the same FastRoute style:

    $this->addRoute('POST', '/this_is_a_pattern', 'Namespace\\Domain\\Folder\\Classname:methodname');
    
The router matches the route, creates an object of type Classname, and triggers the method called methodname. Classname must be a class that implements Chassis\Action\ActionInterface, and for convenience there are two versions available: WebAction and CliAction. So it's easiest to have your Classname extend one of those two classes and you're good to go.

## The Actions

WebAction and CliAction correspond to whether Chassis has received a request from either a browser or the command line, which is accessed with `getRequest()`. If it's come from a browser, your WebAction-extending Classname will have access to a Request object that contains methods to pull stuff out of $_POST, $_GET, $FILE, $_COOKIE, php://input, and the list of URL segments (if any). If it's come from the command line, your CliAction-extending Classname will get a Request object that has methods for getting the command line arguments.

Both Action classes also have the MiddlewareCollection and ObjectCollection injected into them, which are accessed using `getMiddleware()` and `getContainer()` . You can override their constructors if you want, but they also include an init() bootstrapper function to save you having to write some code.

You can't call a CliAction from a browser, or a WebAction from the command line, without Chassis giving you fatal errors about getting the wrong request type. You can create your own Action class as long as you implement Chassis\Action\ActionInterface; just be aware that calling it from the browser or command line will cause a Request object of the corresponding type to be injected into it.

## Inside your new Classname

At this point, the router has created an object of type Classname, invoked the `init()` method, and invoked the method `methodname()`. **This method cannot have any arguments.** Technically nothing needs to be passed into it because you already have access to whatever you need in the Actions's injected dependencies.

The most important thing to remember is that everything from this point on is optional, and can be replaced with anything you'd prefer to use instead of whatever comes with Chassis. All you have to do is shove it into your composer.json, and instantiate it here. You can arrange your code in any half-assed way you like, following any design pattern, and whether it works or not is all on you. Chassis hands off control to your Classname object and does absolutely nothing else, not even logging.

So now I'm just going to explain how I do things. Whether you want to follow along or do your own thing is up to you.

## The ADR Pattern

ADR stands for Action-Domain-Response. This is an evolution of MVC proposed by Paul M. Jones that I find useful, so that's what I go with in a vague kind of way. (Google it if you're curious.) The basic set up in Chassis is as follows:

 * A request comes in and triggers an action.
 * The action knows two things: the command to get a response from the domain, and what kind of responder objects to give the response to. (You can have more than one responder in a single Action cycle. I usually use this to trigger email responses before sending back a web response.)
 * The action makes a call into the business domain by instantiating a particular business domain object and invoking one of its methods.
 * The action creates the responders and gives them the response from the domain.
 * The responders do whatever to produce the expected output.

The point of all this is that each part - the action, the domain object, and the responder - doesn't need to know anything about the other parts except what to do with an input or output.

## Example

I keep Actions and Responders grouped with their respective Domain objects, but you can arrange these however.

In /src/Domain/Auth, I've got some classes that do user signin and creation (somewhat half-assedly, sorry). Here's how it would work for a form, submitted through AngularJS, with the username and password.

1. The route is defined: `$this->addRoute('POST', '/auth/login', 'App\\Auth\\Domain\\AuthAction:login');`
2. Chassis matches the route, creates an instance of `App\\Auth\\Domain\\AuthAction`, injects a WebRequest object into it along with the collection objects, and triggers the `login()` method.
3. AuthAction bootstraps itself in its `init()` method - it sets up the AuthResponder, and the AuthService. The WebRequest is already available in $this->request.
4. In the `login()` method, AuthAction grabs the username and password from the WebRequest and passes it to the AuthService method for logging in users, which is also called `login()`.
5. AuthService is a little black box of logic that takes the info, does whatever it needs to do to communicate with the database, and tosses back either the user's information if the login was successful, or false if it wasn't.
6. AuthAction does nothing with the response other than give it to the AuthResponder.
7. The AuthResponder extends the ApiResponse class, so it handles whatever it's given and emits an API response - a nicely formatted JSON that Angular will recognize.

## Response types

Right now I have the following types built-in:

 * API - outputs a formatted JSON
 * Email - uses PHPMailer to send an email
 * File - uses readfile() to throw files at the browser
 * Web - Puts data into Twig templates
 * Cli - command line output
 
## The ObjectCollection and MiddlewareCollection classes

ObjectCollection is a wrapper around `league/container`. [See the docs](http://container.thephpleague.com/) for how to use it.

MiddlewareCollection is a very basic middleware implementation which I will likely try to replace with something else later. The current class holds an example of a queue - a Process object with a bunch of callables added to it.

Running a queue works as follows:

 * Create an Entity object in your Action.
 * Shove data into it using `addData`.
 * $this->getMiddleware()->run('queuename', $entity);
 * Get data out of $entity with `getData` (for specifics) or `getAllData` (for everything).

## A note on PSR-7

I apologise for not implementing PSR-7 for WebRequests. I may do so in future, but having spent a week trying to get it to work, I've left it alone for now. As PSR-7 only specifies HTTP requests, and Chassis handles both HTTP and CLI in the same stack, I found that I couldn't implement middleware (as per PSR-15) that could handle both.

I'll revisit it later.