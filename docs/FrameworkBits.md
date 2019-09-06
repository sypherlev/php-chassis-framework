## Basic layout

Chassis is composed of several folders which broadly conform to the [PHP-PDS Skeleton](https://github.com/php-pds/skeleton).  

You've also got the /migrations folder, which the `bin/chassis` migration tool uses. There's one migration in there right now that'll make a few user tables, if you need to get going quickly. 

If your .env file sets `devmode=true` then an /emails folder will appear with copies of emails sent by the EmailResponse object, and no emails will actually be sent. (Attachments not included.) Using `devmode` will also set Twig to `debug=true`. 

The /public folder has all the front-end goodness, like your JS and CSS assets. The /cache folder is where the Twig cache stuff is stored. The /templates folder contains all the Twig templates.

Inside the /src folder, I've added the following:

* /Common: classes which may be used in a few different places; a catch-all for stuff that doesn't fit easily anywhere else
* /DBAL: data classes, or anything that interacts with the database
* /Domain: the business domain, where most of your app's logic is going to run
* Class ObjectCollection: the very basic wrapper around `league/container`
* Class RouteCollection: the routing list

The location of the various Collection classes here can't be changed, but the rest can take on any structure you like.