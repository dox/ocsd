<?php
$ou = "DC=SEH,DC=ox,DC=ac,DC=uk";

$admin_ldapconn = ss_ldap_connect();
ldap_set_option ($admin_ldapconn, LDAP_OPT_REFERRALS, 0);
ldap_set_option($admin_ldapconn, LDAP_OPT_PROTOCOL_VERSION, 3);

if ($admin_ldapconn) {
	$filter = "(|(sAMAccountName=" . $person->sso_username . ")(mail=" . $person->oxford_email . "))";

	// BIND
	$admin_bind = ss_ldap_bind($admin_ldapconn, LDAP_BIND_DN, LDAP_BIND_PASSWORD);

	$admin_search_results = ss_ldap_search($admin_ldapconn, $ou, $filter);

	$admin_entries = ss_ldap_get_entries($admin_ldapconn, $admin_search_results);

	if ($admin_entries['count'] == 1) {
    echo "<div class=\"alert alert-dark\" role=\"alert\">" . $admin_entries[0]['dn'] . "</div>";
    echo "<h1 class=\"float-right\">" . useraccountcontrolbadge($admin_entries[0]['useraccountcontrol'][0]) . "</h1>";
    echo "<p>Username: <kbd>" . $admin_entries[0]['samaccountname'][0] . "</kbd></p>";
    echo "<p>Description: " . $admin_entries[0]['description'][0] . "</p>";
    echo "<p>CN: " . $admin_entries[0]['cn'][0] . " <i>(" . $admin_entries[0]['givenname'][0] . " " . $admin_entries[0]['sn'][0] . ")</i></p>";
    echo "<p>pwdlastset: " . pwdlastsetbadge($admin_entries[0]['pwdlastset'][0]) . "</p>";
    echo "<p>mail: <a href=\"mailto:" . $admin_entries[0]['mail'][0] . "\">" . $admin_entries[0]['mail'][0] . "</a></p>";

    echo "<h2>Member Of:</h2>";
    echo "<ul>";
    foreach ($admin_entries[0]['memberof'] AS $memberOf) {
      echo "<li>" . "<div class=\"alert alert-dark\" role=\"alert\">" . $memberOf . "</div>" . "</li>";
    }
    echo "</ul>";
	} else {
		if (debug == true) {
			echo "<div class=\"alert alert-warning\" role=\"alert\">";
			echo "<kbd>ldap_count_entries</kbd> as user <code>" . $user_dn . "</code> is <code>" . $admin_entries['count'] . "</code>";
			echo "</div>";
		}
    echo "<div class=\"alert alert-warning\" role=\"alert\"><strong>Warning!</strong> More than 1 user found!</div>";
    echo "<pre>";
    print_r($admin_entries);
    echo "</pre>";
	}
}
?>
