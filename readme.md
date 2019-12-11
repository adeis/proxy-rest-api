# Proxy Rest API
## Description

just proxy for Rest API for passing blocking cors

### Instalation

``` bash
composer install

# copy env file
cp .env.example .env
# change BASE_API to your real API URL

php -S localhost:8080 -t public

# make sure you are use webserver apache for run .htaccess, if not, please convert it to your webserver config

``` 
