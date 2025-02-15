# HRIS

Manage company employee information. Including timekeeping and wages management.

# Tools and Libraries used
1. Codeigniter 4
2. MySQL
3. CI4 shield library - For authentication, authorization and RBAC(role-based access controls)

# User administration
1. User administration is in a separate module(namespace) which folder name is Admin. This module has it's own route file, controllers and views as well as migrations to segregate logic for user administration.

# Session
Path - /app/Config/Session.php

Session expiration is 0 seconds so if we close the browser, session expires

# Security measures
1. CI4 shield library package - Authentication and Authorization
2. Using csrf token in forms - Configuration can be found in /app/Config/Security.php
3. Sanitizing inputs and displays by using CI4 esc function to prevent xss attacks
   a. Converts special characters into html entities
   b. Prevents malicious scripts from executing
4. Uses entity based access on tables to prevent sql injection. CI4 automatically uses prepared statements.
5. Input validation
6. Sensitive data encryption like bank account number as well as formatted display

# Setup info
1. Will need to create a .env file
2. Requires composer to install php dependencies
3. CI4 uses cli to run the public site. Uses port 8080
4. Requires mysql db. Configuration can be supplied in the .env file
5. Requires encryption key to be supplied in .env file. Encryption key requires at least a base64 string
6. Requires smtp account to be used for sending email. Credentials will be supplied in the .env file
7. There will only be 1 superadmin account and will be added by the seed file along the migration
   a. Superadmin credential is admin@example.com and password is hris@admin

# Env file configurations
1. You can modify the mysql db config to match your mysql credentials
2. You can modify the encryption key of what you want to use
3. Supply the username and password of the smtp that you will be using
   a. If you will be using gmail, base on experience, you will need to generate an app password cause you can't use your gmail password directly
4. You can change the email secret key of your choice

CI_ENVIRONMENT = development
app.baseURL = 'http://localhost:8080'
database.default.hostname = localhost
database.default.database = hris
database.default.username = root
database.default.password = 
database.default.port = 3306
encryption.key = afUZk3awWBI03h9EmjZUa2DKUKv2N6UAmlWfhfXsh3Q=
email.SMTPHost = smtp.gmail.com
email.SMTPUser = 
email.SMTPPass = 
email.SMTPPort = 587
EMAIL_SECRET_KEY = "hr1s3m@1l";

# Other notes
1. The data integrity for updating employee/user record for bank details can be done through trigger but in my opinion, in real world, it's harder to debug triggers and mysql will not be able to inform us if the trigger fails so decided to do the logic in the code.
2. No library created for simplification of the project. Just created mainly helpers for easy access on supportive functions

# Setup instruction
1. Clone project
2. Create .env file and setup
3. Open terminal then cd to the project root then Run composer install
4. Make sure that your mysql connection is running correctly before running number 5.
5. There is a CREATE_DATABASE.sql file in the project root, run it's content to your mysql
6. Then while your terminal points to your project root, run below in order
   a. php spark migrate
   b. php spark db:seed RunAllSeed
   c. php spark serve - To run the app.

# Features
1. Super admin and admin can change the user's group
2. Super admin and admin can approve or reject a bank change request
3. Super admin and admin have access to all bank change requests. User only have access to it's own records
4. Only the owner of the bank change request can modify their own
5. Old bank change requests can't be modified at all if there's a most recent request 
6. All users can create and update bank change request
7. Users can view responses to a bank change request
8. Dashboard displays basic info including bank details to validate current bank details of the current user as well as access group of the current user
9. Dashboard for Bank change requests list
10. Dashboard for Responses to a Bank change request
11. Approving or rejecting request sends an email to the requestor about the status of their request
12. Email message with clean format using html and link to display the details of the response
13. Login filter and page access check
14. Basic navigation
15. Common user authentication and authorization features
    a. Login
    b. Login magic link - Will send an email for a login link. Email should exists in the db.
    c. Remember me
    d. Register
