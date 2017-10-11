## The Database

Chassis uses a database package called [Blueprint](https://github.com/sypherlev/blueprint) out of the box. Blueprint was built for data-processing and granular control over relational databases.

If you want to use something else, you can. All you have to do is set up your own ORM and add it to the ObjectCollection.

### Blueprint and Chassis

Data classes (stored in /src/DBAL) are added to the ObjectCollection by their namespace. Chassis includes a bootstrapper class called SourceBootstrapper that can be used to create a Blueprint Source object.

First, the Source is added to the ObjectCollection. The SourceBootstrapper can determine what kind of Source (MySQL or PostgreSQL) from the Chassis .env file database parameters if you pass the database prefix to it.

`$this->add('local-source', SourceBootstrapper::generateSource('local'));`

The created Source object can generate a corresponding Query object:

`$this->add('local-query', $this->get('local-source')->generateNewQuery());`

The Source and Query are used to create all the Data objects which will be used by the application.

    $this->add('auth-local', new DBAL\AuthData(
        $this->get('local-source'),
        $this->get('local-query')
    ));

    $this->add('user-local', new DBAL\UserData(
        $this->get('local-source'),
        $this->get('local-query')
    ));
    
Chassis can handle any number of databases, but each should have their own copies of each Data class. A good habit is to append the database prefix to the object tag, e.g. `auth-local`.

Data classes are then used through the ObjectCollection, wherever it's created (for example in the BasicService class in /src/Common).