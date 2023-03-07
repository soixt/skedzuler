## Scheduler Application 

This application is made in Laravel and VueJS.


## To run it on Windows:

1. Install composer

2. Setup the application

- git clone https://github.com/sasaorasanin/scheduler.git
- rename .env.example to .env
- composer install
- npm install
- npm run dev
- php artisan serve

## To run it one fresh CentOS:

1. Install apache server

- yum install httpd -y
- service httpd enable or systemctl enable httpd.service
- service httpd restart or systemctl restart httpd.service

2. Configure firewall

- firewall-cmd --zone=public --permanent --add-service=http
- firewall-cmd --zone=public --permanent --add-service=https
- firewall-cmd --reload

3. Apache and Laravel configuration

- Change DirectoryIndex from index.html to index.php
- Change DocumentRoot to "/var/www/html/public"

4. Install Composer

5. Setup the application

- git clone https://github.com/sasaorasanin/scheduler.git public
- rename .env.example to .env
- composer install
- npm install
- npm run dev