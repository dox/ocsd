# OCSD (Oxford College Student Database)
A web-based utlity ti plug into the local Oxford CUD database.  Additionally, it will connect to your local LDAP (Active Directory) and provide sync/update for users.

OCSD is a standard PHP/mySQL website.

# Requirements
* [Git](https://git-scm.com/book/en/v2/Getting-Started-Installing-Git)
* PHP 7.2+ (include <code>apt-get install php-ldap</code>
* [MySQL](https://www.mysql.com/)
* [Composer](https://getcomposer.org/)

# Installation
Please ensure you have a valid Apache virtual host/site ready, and mySQL working.  OCSD is not designed to connect to a MSSQL server.

* Download OCSD into your web root from GitHub: <code>git clone https://github.com/dox/ocsd.git</code>
* Import the mySQL schema into mySQL (coming soon)
* Install mPDF: <code>composer require mpdf/mpdf</code>
* Copy config.php.SAMPLE to config.php (in the root of the web directory) and modify
* Log in (using your LDAP username/password)!

# Scheduled Tasks
<code>0 1 * * * curl https://your-web-address-here/cron.php</code> can be added to crontab which will trigger every <code>.php</code> file in the <code>/cron</code> folder

* _test.php
* cud_sync.php
* email_expiring_passwords.php
* ldap_sync.php
