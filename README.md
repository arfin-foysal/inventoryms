## BacBon CRM (Customer Relationship Management)
## Develop By: [BacBon Limited](https://bacbonltd.com/)

## Developer Team 
- [Foysal Ahmad](https://linkedin.com/in/arfinfoysal/)

## Features
- JWT Authentication
- Role Based Authentication
- User Management
- Role Management
- Permission Management
- CRM Functionality
- API Documentation
- Swagger API Documentation
- Docker Support


## Instructions

- Clone the repository
- Run `composer install`
- ENV `cp .env.example .env or copy .env.example .env.`
- Run `php artisan key:generate`
- Run `php artisan jwt:secret`
- Create a database and update the `.env` file
- if use sqlite Database touch database/database.sqlite
- Run `php artisan migrate` or `php artisan migrate:fresh --seed`
- Run `php artisan storage:link`
- Run `php artisan serve`
- Open Postman and import the collection from the `postman` directory
- Swagger API Documentation: `http://localhost:8000/swagger/documentation`



## ========Thank You========
