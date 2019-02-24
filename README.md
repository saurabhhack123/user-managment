# User Managment

This app provides REST API for admin to manage users and groups .
* Manage users
  - Admin can create user
  - Admin can delete user
  - Admin can edit user
  - Admin can see users list

* Manage groups
  - Admin can create group
  - Admin can delete group when they have no longer members
  - Admin can edit group name
  - Admin can see group lists
  - Admin can add users to group
  - Admin can remove users from group

### Tech

* [Symfony 4.2] - Modern PHP Framework
* [MySQL 5.7] - Database
* [PHP version-7.2] - Programming language 
* [php-cs-fixer] - A tool to automatically fix PHP Coding Standards issues
* [serializer-bundle] - Serialize Objects
* [symfony/validator] - To Valid data
* [symfony/web-server-bundle] - Inbuilt Symfony Developement Server 


### Installation

Install the dependencies and devDependencies and start the server.

```sh
$ composer install
$ php bin/console do:sc:up --force
$ php bin/console security:encode-password
Update passoword with above encode password and role entry in db with serialize data => a:2:{i:0;s:10:"ROLE_ADMIN";i:1;s:9:"ROLE_USER";} 
$ php bin/console server:run
```



