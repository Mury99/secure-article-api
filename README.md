## Getting Started

- Run `docker compose build --pull --no-cache` to build fresh images
- Run `docker compose up --wait -d`
- Open `https://localhost` [accept the auto-generated TLS certificate](https://stackoverflow.com/a/15076602/1352334)

- Run:
```
bin/console lexik:jwt:generate-keypair \
&& bin/console doctrine:database:create \
&& bin/console doctrine:migrations:migrate --no-interaction \
&& bin/console doctrine:query:sql "$(cat data/init-data.sql)"
```

```
curl --location 'https://localhost/auth/login_check' \
--header 'Content-Type: application/json' \
--data-raw '{
    "username": "admin@example.com",
    "password": "password123"
}'
```

```
curl --location 'https://localhost/auth/register' \
--header 'Content-Type: application/json' \
--data-raw '{
    "username": "user45623XX@gmail.com",
    "password": "pass123",
    "name": "Petr Novák",
    "roles": ["AUTHOR"]
}'
```

```
curl --location --request GET 'https://localhost/users' \
--header 'Content-Type: application/json' \
--header 'Authorization: Bearer ..token..'
```

## Rest of endpoints
```
 ------------------------------------------------- -------- -------- ------ ----------------------------------- 
  Name                                              Method   Scheme   Host   Path
 ------------------------------------------------- -------- -------- ------ -----------------------------------
  articles_list                                     GET      ANY      ANY    /articles
  articles_create                                   POST     ANY      ANY    /articles
  articles_detail                                   GET      ANY      ANY    /articles/{id}
  articles_update                                   PUT      ANY      ANY    /articles/{id}
  articles_delete                                   DELETE   ANY      ANY    /articles/{id}
  users_create                                      POST     ANY      ANY    /users
  users_list                                        GET      ANY      ANY    /users
  users_detail                                      GET      ANY      ANY    /users/{id}
  users_update                                      PUT      ANY      ANY    /users/{id}
  users_delete                                      DELETE   ANY      ANY    /users/{id}
```

## Further improvements like...
- fixtures
- zenstruck foundry /+ browser /+ messenger-test
- integration test, api test
- nelmio api doc
- ...

## Docs - TLS + Troubleshoot

1. [TLS Certificates](docs/tls.md)
2. [Troubleshooting](docs/troubleshooting.md)
