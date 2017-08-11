## How I use it

I'm just going to explain how I do things. Whether you want to follow along or do your own thing is up to you. Again, Chassis is flexible and you can set it up any weird way you like, once you're inside an Action object.

I usually have Chassis set up in a broad ADR pattern. (ADR stands for Action-Domain-Response. This is an evolution of MVC proposed by Paul M. Jones that I find useful. Google it if you're curious.) The basic set up is as follows:

 * A request comes in and triggers an action object.
 * The action knows two things: the commands to get a response from the domain, and what kind of responder objects to give the response to. (You can have more than one responder in a single Action cycle. I usually use this to trigger email responses before sending back a web response.)
 * The action runs any pre-processing middleware queues I've defined.
 * The action makes a call into the business domain by instantiating a particular service object and invoking one of its methods.
 * The action runs any post-processing middleware queues I've defined.
 * The action creates the responders and gives them the response from the domain.
 * The responders do whatever to produce the expected output.

The point of all this is that each part - the action, the domain object, and the responder - doesn't need to know anything about the other parts except what to do with an input or output.

## Really Simple Example

I keep Actions and Responders grouped with their respective Domain objects, but you can arrange these however.

In /src/Domain/Auth, I've got some classes that do user signin and creation. Here's how it would work for a form, submitted through AngularJS, with the username and password.

1. The route is defined: `$this->addRoute('POST', '/auth/login', 'App\\Auth\\Domain\\AuthAction:login');`
2. Chassis matches the route, creates an instance of `App\\Auth\\Domain\\AuthAction`, injects a Web object into it along with the collection objects, and triggers the `login()` method.
3. AuthAction bootstraps itself in its `init()` method - it sets up the AuthResponder, and the AuthService. The Web is already available in `$this->request()`.
4. In the `login()` method, AuthAction grabs the username and password from the Web object and passes it to the AuthService method for logging in users, which is also called `login()`.
5. AuthService is a little black box of logic that takes the info, does whatever it needs to do to communicate with the database, and tosses back either the user's information if the login was successful, or false if it wasn't.
6. AuthAction does nothing with the response other than give it to the AuthResponder.
7. The AuthResponder extends the ApiResponse class, so it handles whatever it's given and emits an API response - a nicely formatted JSON that Angular will recognize.