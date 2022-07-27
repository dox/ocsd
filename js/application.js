function ldap_delete_user(el, samaccountname) {
	var isGood=confirm('Are you sure you want to delete this user from the LDAP?  This action cannot be undone!');
	
	if (isGood) {
		var url = "/actions/ldap_delete_user.php";
		
		var xhr = new XMLHttpRequest();
		var formData = new FormData();
		xhr.open("POST", url, true);
		
		formData.append("samaccountname", samaccountname);
		
		xhr.send(formData);
		
		xhr.onload = function() {
			if (xhr.status != 200) { // analyze HTTP status of the response
				alert("Something went wrong.  Please refresh this page and try again.");
				alert(`Error ${xhr.status}: ${xhr.statusText}`); // e.g. 404: Not Found
			} else {
				// check if the word 'Error' appeared
				var error_check = xhr.responseText.includes("Error");
				
				// if the response had an error, alert
				if (error_check == true) {
					alert(this.responseText);
				} else {
					// success
					el.parentNode.parentNode.parentNode.parentNode.style.display='none';
				}
			}
		}
		
		xhr.onerror = function() {
			alert("Request failed");
		};
	}
}

function ldap_toggle_user(el, samaccountname, toggle) {
	if (toggle == "enable") {
		var isGood=confirm('Are you sure you want to enable this user in the LDAP?');
	} else if (toggle == "disable") {
		var isGood=confirm('Are you sure you want to disable this user in the LDAP?');
	} else {
		alert("ERROR!");
		quit();
	}
	
	if (isGood) {
		var url = "/actions/ldap_toggle_user.php";
		
		var xhr = new XMLHttpRequest();
		var formData = new FormData();
		xhr.open("POST", url, true);
		
		formData.append("samaccountname", samaccountname);
		formData.append("toggle", toggle);
		
		xhr.send(formData);
		
		xhr.onload = function() {
			if (xhr.status != 200) { // analyze HTTP status of the response
				alert("Something went wrong.  Please refresh this page and try again.");
				alert(`Error ${xhr.status}: ${xhr.statusText}`); // e.g. 404: Not Found
			} else {
				// check if the word 'Error' appeared
				var error_check = xhr.responseText.includes("Error");
				
				// if the response had an error, alert
				if (error_check == true) {
					alert(this.responseText);
				} else {
					// success
				}
			}
		}
		
		xhr.onerror = function() {
			alert("Request failed");
		};
	}
}

function ldap_provision_user(el, cudid, email) {
	if (email == "enable") {
		var isGood=confirm('Are you sure you want to provision this user in the LDAP, and email them their details?');
	} else if (email == "disable") {
		var isGood=confirm('Are you sure you want to provision this user in the LDAP (silently)?');
	} else {
		alert("ERROR!");
		quit();
	}
	
	if (isGood) {
		var url = "/actions/ldap_provision_user.php";
		
		var xhr = new XMLHttpRequest();
		var formData = new FormData();
		xhr.open("POST", url, true);
		
		formData.append("cudid", cudid);
		formData.append("email", email);
		
		xhr.send(formData);
		
		xhr.onload = function() {
			if (xhr.status != 200) { // analyze HTTP status of the response
				alert("Something went wrong.  Please refresh this page and try again.");
				alert(`Error ${xhr.status}: ${xhr.statusText}`); // e.g. 404: Not Found
			} else {
				// check if the word 'Error' appeared
				var error_check = xhr.responseText.includes("Error");
				
				// if the response had an error, alert
				if (error_check == true) {
					alert(this.responseText);
				} else {
					// success
					el.parentNode.parentNode.parentNode.parentNode.style.display='none';
				}
			}
		}
		
		xhr.onerror = function() {
			alert("Request failed");
		};
	}
}