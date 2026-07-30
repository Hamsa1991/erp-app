###################
What is CodeIgniter
###################

CodeIgniter is an Application Development Framework - a toolkit - for people
who build web sites using PHP. Its goal is to enable you to develop projects
much faster than you could if you were writing code from scratch, by providing
a rich set of libraries for commonly needed tasks, as well as a simple
interface and logical structure to access these libraries. CodeIgniter lets
you creatively focus on your project by minimizing the amount of code needed
for a given task.

*******************
Release Information
*******************

This repo contains in-development code for future releases. To download the
latest stable release please visit the `CodeIgniter Downloads
<https://codeigniter.com/download>`_ page.

**************************
Changelog and New Features
**************************

You can find a list of all changes for each release in the `user
guide change log <https://github.com/bcit-ci/CodeIgniter/blob/develop/user_guide_src/source/changelog.rst>`_.

*******************
Server Requirements
*******************

PHP version 5.6 or newer is recommended.

It should work on 5.3.7 as well, but we strongly advise you NOT to run
such old versions of PHP, because of potential security and performance
issues, as well as missing features.

************
Installation
************

Please see the `installation section <https://codeigniter.com/userguide3/installation/index.html>`_
of the CodeIgniter User Guide.

*************************
How to Run This Project
*************************

Follow these steps to download and run this CodeIgniter 3 project from GitHub:

1. **Clone or Download the Repository**
https://github.com/Hamsa1991/erp-app.git

Or download the ZIP file from GitHub and extract it to your local server directory.

2. **Navigate to the Project Directory**

3. **Configure the Database**
- Open the `application/config/database.php` file
- Update the database connection settings with your local credentials:

```php
'hostname' => 'localhost',
'username' => 'your_database_username',
'password' => 'your_database_password',
'database' => 'your_database_name',

```

4. **Import the SQL Database**

- Create a new database in your MySQL server (e.g., using phpMyAdmin or MySQL command line)

- The SQL file is located in the repository (check the root directory for the erp_db.sql file)

5. **Configure Base URL**

- Open application/config/config.php

- Update the $config['base_url'] parameter:

```php
	$config['base_url'] = 'http://localhost/your-repository-name/';
```

6. **Run the Application**
- By default the working running commnad is:
	php -S localhost:3000

- Access your application via web browser:

	http://localhost/your-repository-name/

7. **Default users for login:**

- Default admin: admin@erp.local / admin123

- Default warehouse_user (related to warehouse with id 1): warehouse_user@erp.local / admin123
