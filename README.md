# ShyppyPro Test
### Prerequisites
* [Docker](https://www.docker.com/)

### Container
 - [nginx](https://hub.docker.com/_/nginx) 1.19.+
 - [php-fpm](https://hub.docker.com/_/php) 7.4.+
    - [composer](https://getcomposer.org/) 
    - [yarn](https://yarnpkg.com/lang/en/) and [node.js](https://nodejs.org/en/) (if you will use [Encore](https://symfony.com/doc/current/frontend/encore/installation.html) for managing JS and CSS)
- [mysql](https://hub.docker.com/_/mysql/) 5.7.+

### First steps
```
 git clone -b develop https://github.com/mrmnthn/test_shp.git
```
```
 cd test_shp
```
### Installing Docker containers

run docker and connect to container:
```
 docker-compose up --build -d
```
enter the php container
```
 docker-compose exec php sh
```
Now that we are in the php container last thinks to do
```
 touch .env (sorry!)
```
```
composer install
```
```
npm install
```
```
npm run dev
```
### Ready up
call [localhost](http://localhost/) in your browser
### Enjoy!
