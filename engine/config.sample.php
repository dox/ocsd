<?php
// Site Constants
//Replace all variables with your own details.  For example "{{DB_NAME}} would be "your_data_base_name"
define ("SITE_VERSION", "0.1");
define ("SITE_UNIQUE_KEY", "sitea"); // if you're running more than 1 instance, this needs to be unique

// Database Constants
define ("DB_SERVER", "localhost"); // generally 'localhost'
define ("DB_NAME", "");
define ("DB_USER", "");
define ("DB_PASS", "");

// Location Constants
define ("SITE_PATH", "{{SITE_PATH}}"); // if not the root, i.e. '/helpdesk'
define ("SITE_LOCATION", $_SERVER['DOCUMENT_ROOT'] . SITE_PATH);

// Site Detail Constants
define ("SITE_NAME", "Oxford Central Student Database");
define ("SITE_SHORT_NAME", "OCSD");
define ("SITE_SLOGAN", "A central place for all college-level student data");
define ("SITE_ADMIN_NAME", "");
define ("SITE_ADMIN_EMAIL", "");

// LDAP Constants
define ("LDAP_LOCATION", "{{LDAP_SERVER}}"); // Active Directory location (e.g. '192.168.0.1')
define ("LDAP_DOMAIN", "{{@domain.local}}"); // Domain name suffix (e.g. '@domain.local')
define ("LDAP_USERNAME", "{{LDAP_USERNAME}}"); // User able to search the AD (e.g. 'username')
define ("LDAP_PASSWORD", "{{LDAP_PASSWORD}}"); // Password for ldapSearchUser
define ("LDAP_BIND_ROOT", "{{LDAP_BIND_ROOT}}"); // The root binding location for users (e.g. 'DC=domain,DC=local')

?>
