## The Actions

`WebAction` and `CliAction` correspond to whether Chassis has received a request from either a browser or the command line, which is accessed with `getRequest()`. You can't call a `CliAction` from a browser, or a `WebAction` from the command line, without Chassis giving you fatal errors about getting the wrong request type. (You can create your own Action class as long as you implement Chassis\Action\ActionInterface; just be aware that calling it from the browser or command line will cause a Request object of the corresponding type to be injected into it.)

Both Action classes also have the MiddlewareCollection injected into them, which is accessed using `getMiddleware()`. You can override their constructors if you want, but they also include an init() bootstrapper function to save you having to write some code.

## The ObjectCollection and MiddlewareCollection classes

ObjectCollection is a wrapper around `league/container`. [See the docs](http://container.thephpleague.com/) for how to use it. You can safely ignore it if you'd rather use something else. It's not much more than a basic service locator doodad.

MiddlewareCollection is a bit more substantial. It defines middleware queues for use with the request objects that are injected into actions. Each request has a `WithMiddlewareVars` trait for manipulating variables; the incoming request information is NOT affected.
 
The middleware queues are composed of a processing object and any number of callables added to it. Chassis has two processors, WebProcess and CliProcess, which can only run on their corresponding Web/Cli objects. The callables can be anything that can operate on the Web/Cli objects.

Running a queue works as follows:

 * Define the queue in `MiddlewareCollection` using `loadQueue()`; give it a unique name and add the callables.
 * In your Action, start the queue by calling `$this->getMiddleware()->run('queuename', $this->getRequest()`;
 * The request object now has whatever data should have been added after the callables have processed it.
 * Get data out of the request with `getMiddlewareVar('name')` (for specifics) or `getAllMiddlewareVars` (for everything).
 
You can ignore this middleware implementation if you feel like it, but bear in mind that using the same middleware for Web and Cli might cause things to break.

### A note on PHP middleware

This implementation is somewhat based on Laravel's middleware, with one major difference: you can conditionally run multiple middleware queues on a single request. You don't have to pick a queue per route and just live with it - you can choose to run any number of queues on the Web/Cli object at any time within the Action method. Line them up, fire the object through them, exit early with Response objects or save stuff or log things or whatever.

I usually keep the logic heavy lifting to various Service classes and keep the middleware for basic stuff like adding metadata, but that's just me.

## Inside an Action object

Okay, fire up a request from the web or CLI, and you get an Action object. In both cases the object is created, the `init()` method is invoked (if you're using WebAction/CliAction-extended classes), and the method `methodname()` is triggered. **This method cannot have any arguments.**

The most important thing to remember is that everything from this point on is optional, and can be replaced with anything you'd prefer to use instead of whatever comes with Chassis. All you have to do is shove it into your composer.json, and instantiate it in this class. You can arrange your code in any half-assed way you like, following any design pattern, and whether it works or not is all on you. Chassis hands off control to your Action object and does absolutely nothing else, not even logging.