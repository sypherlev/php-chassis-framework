## Web routing

All your routes are stored in /src/RouteCollection.php. RouteCollection has one method, the constructor, which contains a list of routes. RouteCollection is basically just an extension of the FastRoute dispatcher that registers all your routes before the framework kicks off. List them all here - follow the examples there if you're not sure - and group them using comments. They all follow the same FastRoute style:

    $this->addRoute('POST', '/this_is_a_pattern', 'App\\Domain\\Folder\\Classname:methodname');
    
The route must point to any class within the App namespace that extends `SypherLev\Chassis\Action\WebAction`. The router matches the route, creates an object of type `Classname`, and triggers the method called `methodname`. Inside your `Classname`, parameter information is available through `$this->getRequest()`; the web request sets up convenient access methods for URL segments, variables stored in the body ($_POST or php://input), query parameters, cookie variables, and uploaded files. If you need stuff from $_SERVER, then you're on your own.

**Note:** $_POST and php://input are combined because I use Chassis with AngularJS about 90% of the time.

**Note on PSR-7:** I did implement a version of `WebAction` at one point using PSR-7, and abandoned it out of frustration when it broke everything to do with file uploads. So I took it out. If you want PSR-7 requests, then you'll need to extend `WebAction` and integrate your own PSR-7 implementation, because I am not going down that particular crazy-pants hellhole again.

## CLI triggers

Invoking a command line action is similar to the web routing, but must point to a class that extends `CliAction`:

    php /var/www/html/index.php "App\\Domain\\Folder\\Anotherclassname:methodname"

You can add any number of arguments after the namespace instruction, and access them in `$this->getRequest()`. This is just basic access to `$argv`, minus the script and namespace instruction at the start.