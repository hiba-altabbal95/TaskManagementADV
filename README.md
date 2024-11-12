# TaskManagementSystemADV


## Description
This project is a **Security TASK MANAGEMENT SYSTEM ADVANCED** built with **Laravel 10** that provides a **RESTful API** for managing tasks and users.
 It allows admin to create tasks , user  and assign a task to user . In this Project we handle Task Dependency 
 


### Technologies Used:
- **Laravel 10**
- **PHP**
- **MySQL**
- **XAMPP** (for local development environment)
- **Composer** (PHP dependency manager)
- **Postman Collection**: Contains all API requests for easy testing and interaction with the API.

## Features
- we add securityHeaderMiddlware to prevent attack from hackers.
- user POLICY for Task and User Model for authorization action in our project 
- JWT Authentication
- Job Queue , Caching Indexing to enhance DB working 
- Soft delete method for Task
- add attachment to task in a very Secure way
- Error Handling for all action 




## Setting up the project

1. Clone the repository 

   git clone https://github.com/hiba-altabbal95/TaskManagementADV
   
2. navigate to the project directory
  
    cd Task-Management-system  

3. install Dependencies: composer install 

4. create environment file  cp .env.example .env
  
5. edit .env file (DB_DATABASE=taskdb)

6. Generate Application Key php artisan key:generate

7. Run Migrations To set up the database tables, run: php artisan migrate

8. Run this command to generate JWT Secret
   
   php artisan jwt:secret
   
9. Seed the Database
   
    php artisan db:seed
	
10. Run the Application
   
    php artisan serve

11. in file (TaskManagementADV.postman_collection) there are a collection of request to test api.




