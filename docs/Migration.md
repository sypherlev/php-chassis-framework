## Migration

**The migration tool is one-way only; no rollbacks are possible unless you add another SQL script file that un-does something in a previous migration.**

Chassis includes a very basic database migration tool based on Blueprint. It must be run from the command line.
 
`$ bin/chassis`

`Starting Chassis Migration...`

`No function specified. Choose one of the following:`

`    -b <database_prefix>      -create a backup of the database specified`

`    -m <database_prefix>      -run all unapplied migrations on the database specified`
`                                        by the prefix in the .env file`

`    -c <database_prefix>`
`       <migration_name>       -create a migration in the migrations folder with the`
`                                        given name (A-Za-z_0-9) for the database specified in`
`                                        the prefix in the .env file`

`   -s <database_prefix>`
`      <bootstrap_file.sql>   -run a bootstrap file on the database specified by the`
`                                        prefix in the .env file`
`



The bootstrap.sql file in /migrations has user tables which plug into the Auth system in this sample, but the only one that's really required is the migrations table.

In the project root, run `bin/chassis` to see options for migrations. (You may need to make it executable first.)