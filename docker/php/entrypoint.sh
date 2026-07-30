#!/bin/sh
set -eu

if [ "${WAIT_FOR_DB:-false}" = "true" ]; then
    php -r '
        $attempts = 60;
        while ($attempts-- > 0) {
            try {
                new PDO(
                    sprintf(
                        "mysql:host=%s;port=%s;dbname=%s",
                        getenv("WAIT_DB_HOST"),
                        getenv("WAIT_DB_PORT") ?: "3306",
                        getenv("WAIT_DB_DATABASE")
                    ),
                    getenv("WAIT_DB_USERNAME"),
                    getenv("WAIT_DB_PASSWORD")
                );
                exit(0);
            } catch (PDOException) {
                sleep(2);
            }
        }
        fwrite(STDERR, "Database did not become ready in time.\n");
        exit(1);
    '
fi

exec "$@"
