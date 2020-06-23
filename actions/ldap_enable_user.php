<?php
include_once("../includes/autoload.php");

$ldapUsername = $_POST['samaccountname'];

$ou = "DC=SEH,DC=ox,DC=ac,DC=uk";
$ldapClass = new LDAP();

if ($ldapClass) {
	$filter = "(sAMAccountName=" . $ldapUsername . ")";

	// BIND
	$admin_bind = $ldapClass->ldap_bind();
	$admin_search_results = $ldapClass->ldap_search($ou, $filter);
	$admin_entries = $ldapClass->ldap_get_entries($admin_search_results);

  if ($admin_entries['count'] == 1) {
    $userdata["useraccountcontrol"] = "512";

    $ldapClass->ldap_mod_replace($admin_entries[0]['dn'], $userdata);
    $logInsert = (new Logs)->insert("ldap","warning",null,"Enabled user account <code>" . $ldapUsername . "</code>");
  } else {
    $logInsert = (new Logs)->insert("ldap","error",null,"Failed to enable user account <code>" . $ldapUsername . "</code>.  " . $admin_entries['count'] . " entries found");
  }
} else {
  $logInsert = (new Logs)->insert("ldap","error",null,"Failed to enable user account <code>" . $ldapUsername . "</code>.  LDAP Connection failed");
}
?>
