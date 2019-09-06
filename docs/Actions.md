## The Actions

`WebAction` and `CliAction` correspond to whether Chassis has received a request from either a browser or the command line, which is accessed with `getRequest()`. You can't call a `CliAction` from a browser, or a `WebAction` from the command line, without Chassis giving you fatal errors about getting the wrong request type. (You can create your own Action class as long as you implement Chassis\Action\ActionInterface; just be aware that calling it from the browser or command line will cause a Request object of the corresponding type to be injected into it.)

## The ObjectCollection

ObjectCollection is a wrapper around `league/container`. [See the docs](http://container.thephpleague.com/) for how to use it. You can safely ignore it if you'd rather use something else. It's not much more than a basic service locator doodad.

## Inside an Action object

Okay, fire up a request from the web or CLI, and you get an Action object. In both cases the object is created, the `init()` method is invoked (if you're using WebAction/CliAction-extended classes), and the method `methodname()` is triggered. **This method cannot have any arguments.**

The most important thing to remember is that everything from this point on is optional, and can be replaced with anything you'd prefer to use instead of whatever comes with Chassis. All you have to do is shove it into your composer.json, and instantiate it in this class. You can arrange your code in any half-assed way you like, following any design pattern, and whether it works or not is all on you. Chassis hands off control to your Action object and does absolutely nothing else, not even logging.