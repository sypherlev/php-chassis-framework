# The Chassis Framework

Chassis is a microframework/collection of stuff that's designed to be dangerously flexible, and aimed at rapid development. It uses FastRoute and dotENV to do the initial request bootstrapping, and after that, it's all you, baby. You build whatever Frankenstein business logic you need to get things done.

It's largely the result of my streamlining my own development process, following my own rules for OOP and the ADR design pattern. This particular version consists of the framework itself and a few other things in /app so I can keep all it all somewhat organized.

Apart from FastRoute and dotENV, Chassis has a pretty robust query builder for interacting with MySQL/MariaDB databases (optional), a migration tool built on top of that (optional), and a set of Request classes to get shit into your domain (not optional), and a set of Response classes that build various responses for output (optional).

Use it at your own risk.

## Installation

* Download it or clone it from here.
* Set your web root to the /web folder.
* Run composer install.
* Generate a new .env file from .env_sample.

## The various bits of the framework

Chassis is composed of two main parts - the /src folder, where all the magical framework crap lives, and the /app folder, where YOUR magical crap lives.

You've also got the /migrations folder, which the Migrate tool uses. There's one migration in there right now that'll make a few user tables, if you need to get going quickly. 

The /web folder has all the front-end goodness, like your JS and CSS assets, and your HTML templates.

## Setup

* Open your .env file.
* Change the APP_NAME to whatever.
* Change the APP_NAMESPACE to match the PS-4 autoload for the /app folder in composer.json. This is super important because it makes everything go.
* **Seriously, do NOT forget this or none of the routing will work.**
* If you're feeling silly, you can leave them both at 'MyApp\\\\'.

## Your first route

All your routes are stored in /app/RouteCollection.php. RouteCollection has one method, the constructor, which contains a list of routes. RouteCollection is basically just an extension of the FastRoute dispatcher that registers all your routes before the framework kicks off. List them all here - follow the examples there if you're not sure - and group them using comments. They all follow the same FastRoute style:

    $this->addRoute('POST', '/this_is_a_pattern', 'Namespace\\Classname:methodname');
    
The router matches the route, creates an object of type Classname, and triggers the method called methodname. Classname must be a class that implements Chassis\Action\ActionInterface, and for convenience there are two versions available: WebAction and CliAction. So it's easiest to have your Classname extend one of those two classes and you're good to go.

## The Actions

WebAction and CliAction correspond to whether Chassis has received a request from either a browser or the command line. If it's come from a browser, your WebAction-extending Classname will have access to a Request object that contains methods to pull stuff out of $_POST, $_GET, $FILE, $_COOKIE, php://input, and the list of URL segments (if any). If it's come from the command line, your CliAction-extending Classname will get a Request object that has methods for getting the command line arguments and a few other things.

You can't call a CliAction from a browser, or a WebAction from the command line, without Chassis giving you fatal errors about getting the wrong request type. You can create your own Action class as long as you implement Chassis\Action\ActionInterface; just be aware that calling it from the browser or command line will cause a Request object of the corresponding type to be injected into it.

## Inside your new Classname

At this point, the router has created an object of type Classname and invoked the method methodname. **This method cannot have any arguments.** Technically nothing needs to be passed into it because you already have access to whatever you need in the object's injected request.

The most important thing to remember is that everything from this point on is optional, and can be replaced with anything you'd prefer to use instead of whatever comes with Chassis. All you have to do is shove it into your composer.json, and instantiate it here. You can arrange your code in any half-assed way you like, following any design pattern, and whether it works or not is all on you. Chassis hands off control to your Classname object and does absolutely nothing else, not even logging.

So now I'm just going to explain how I do things. Whether you want to follow along or do your own thing is up to you.

## The ADR Pattern

ADR stands for Action-Domain-Response. This is an evolution of MVC proposed by Paul M. Jones that I find useful, so that's what I go with in a really vague kind of way. (Google it if you're curious.) The basic idea of my set up is as follows:

 * A request comes in and triggers an action.
 * The action knows two things: the command to get a response from the domain, and what kind of responder object to give the response to.
 * The action makes a call into the business domain by instantiating a particular business domain object and invoking one of its methods.
 * The action creates the responder and gives it the response from the domain.
 * The responder does whatever to produce the expected output.

The point of all this is that each part - the action, the domain object, and the responder - doesn't need to know anything about the other parts except what to do with an input or output.

## Example

In /app/Auth, I've got some classes that do user signin and creation (somewhat half-assedly, sorry). Here's how it would work for a form, submitted through AngularJS, with the username and password.

1. The route is defined: $this->addRoute('POST', '/auth/login', 'MyApp\\Auth\\AuthAction:login');
2. Chassis matches the route, creates an instance of MyApp\\Auth\\AuthAction, injects a WebRequest object into it, and triggers the login() method.
3. AuthAction bootstraps itself in the constructor - it sets up the AuthResponder, and the AuthService. The WebRequest is already available in $this->request.
4. In the login() method, AuthAction grabs the username and password from the WebRequest and passes it to AuthService method for logging in users, which is also called login().
5. AuthService is a little black box of logic that takes the info, does whatever it needs to do to communicate with the database, and tosses back either the user's information if the login was successful, or false if it wasn't.
6. AuthAction does nothing with the response other than give it to the AuthResponder.
7. The AuthResponder extends the ApiResponse class, so it handles whatever it's given and emits an API response - a nicely formatted JSON that Angular will recognize.

## Response types

Right now I have the following types built-in:

 * API - formatted JSON
 * Email - uses mail() to send an email
 * File - uses readfile() to throw files at the browser
 * Web - slightly complicated, uses a recursive DOM manipulation to merge data into a HTML template. Still experimental.
 * CLI - command line output.