
<h1 align="center">Expense App Using Laravel </h1>
<h3 align="center">A baisc split payment app in laravel using api.</h3>

## Built with
- [Laravel 9](https://github.com/laravel/framework)
- [Laravel Sanctumn](https://github.com/laravel/sanctum)
- [PHP 8.1](https://www.php.net/releases/8.1/en.php)
- [SQLITE3]

## Sqlite3  Configuration
- Install php dependency  
```bash 
  sudo apt-get install php-sqlite3
  ```

- Create sqlite3 database file database.sqlite in expenseapp/database
  file path: - expenseapp/database/database.sqlite

- .env Configuration
```env
     DB_CONNECTION=sqlite  
     #DB_HOST=127.0.0.1  
     #DB_PORT=3306  
     #DB_DATABASE=laravel  
     #DB_USERNAME=root  
     #DB_PASSWORD=  
```

## PostMan Collection 
[![Run in Postman](https://run.pstmn.io/button.svg)](https://app.getpostman.com/run-collection/1703458-9b87e705-7709-4e71-a8d8-fdf54ecd0da6?action=collection%2Ffork&collection-url=entityId%3D1703458-9b87e705-7709-4e71-a8d8-fdf54ecd0da6%26entityType%3Dcollection%26workspaceId%3D493d307c-7d94-48dc-8f0a-e77eb6cd153f)

## Api Sequence 

- Register User
    {{base_url}}/api/register 

- Login User (for token) (you cna use register api token)
    {{base_url}}/api/login

####         All Endpoint require  {{token}} for  getting user id 

- Split Payment  Api
    {{base_url}}/api/splitpayment

- User Balance Api
    {{base_url}} /api/getUserBalance

- Get Every User Api
    {{base_url}}/api/getEveryoneBalance

- Logout User 
    {{base_url}}/api/logout

## License
The Laravel framework is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).
