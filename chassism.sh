#!/usr/bin/env bash

echo "Starting Chassis Migration..."
if [ $# -eq 0 ]
    then echo "No function specified. Choose one of the following:"
    echo "    -b <database_prefix>      -create a backup of the database specified"
    echo "    -m <database_prefix>      -run all unapplied migrations on the database specified
                                        by the prefix in the .env file"
    echo "    -c <database_prefix>"
    echo "       <migration_name>       -create a migration in the migrations folder with the
                                        given name (A-Za-z_0-9) for the database specified in
                                        the prefix in the .env file"
    echo "    -s <database_prefix>"
    echo "       <bootstrap_file.sql>   -run a bootstrap file on the database specified by the
                                        prefix in the .env file"
    exit
fi
chphpexe=$(which php)
if [ $1 = "-b" ]; then
    if [ $# -eq 1 ]; then
        echo "Missing database prefix - please specify a database prefix from the .env file"
        exit 0
    fi
    output=$($chphpexe public/index.php "Chassis\\Migrate\\Migrate:backup" $2)
    echo "$output"
    exit 0
fi
if [ $1 = "-m" ]; then
    if [ $# -eq 1 ]; then
        echo "Missing database prefix - please specify a database prefix from the .env file"
        exit 0
    fi
    output=$($chphpexe public/index.php "Chassis\\Migrate\\Migrate:migrateUnapplied" $2)
    echo "$output"
    exit 0
fi
if [ $1 = "-c" ]; then
    if [ $# -eq 1 ]; then
        echo "Missing database prefix - please specify a database prefix from the .env file"
        exit 0
    fi
    if [ $# -eq 2 ]; then
        echo "Missing migration name - please specify a name using only (A-Za-z_0-9)"
        exit 0
    fi
    output=$($chphpexe public/index.php "Chassis\\Migrate\\Migrate:createMigration" $2 $3)
    echo "$output"
    exit 0
fi
if [ $1 = "-s" ]; then
    if [ $# -eq 1 ]; then
        echo "Missing database prefix - please specify a database prefix from the .env file"
        exit 0
    fi
    if [ $# -eq 2 ] || [ ! -f migrations/$3 ]; then
        echo "Missing bootstrap file - please specify a file within the migrations folder"
        exit 0
    fi
    output=$($chphpexe public/index.php "Chassis\\Migrate\\Migrate:bootstrap" $2 $3)
    echo "$output"
    exit 0
fi