/*!
 * Color mode toggler for Bootstrap's docs (https://getbootstrap.com/)
 * Copyright 2011-2023 The Bootstrap Authors
 * Licensed under the Creative Commons Attribution 3.0 Unported License.
 */

(() => {
  'use strict'

  const getStoredTheme = () => localStorage.getItem('theme')
  const setStoredTheme = theme => localStorage.setItem('theme', theme)

  const getPreferredTheme = () => {
	const storedTheme = getStoredTheme()
	if (storedTheme) {
	  return storedTheme
	}

	return window.matchMedia('(prefers-color-scheme: dark)').matches ? 'dark' : 'light'
  }

  const setTheme = theme => {
	if (theme === 'auto' && window.matchMedia('(prefers-color-scheme: dark)').matches) {
	  document.documentElement.setAttribute('data-bs-theme', 'dark')
	} else {
	  document.documentElement.setAttribute('data-bs-theme', theme)
	}
  }

  setTheme(getPreferredTheme())

  const showActiveTheme = (theme, focus = false) => {
	const themeSwitcher = document.querySelector('#bd-theme')

	if (!themeSwitcher) {
	  return
	}

	const themeSwitcherText = document.querySelector('#bd-theme-text')
	const activeThemeIcon = document.querySelector('.theme-icon-active use')
	const btnToActive = document.querySelector(`[data-bs-theme-value="${theme}"]`)
	const svgOfActiveBtn = btnToActive.querySelector('svg use').getAttribute('href')

	document.querySelectorAll('[data-bs-theme-value]').forEach(element => {
	  element.classList.remove('active')
	  element.setAttribute('aria-pressed', 'false')
	})

	btnToActive.classList.add('active')
	btnToActive.setAttribute('aria-pressed', 'true')
	activeThemeIcon.setAttribute('href', svgOfActiveBtn)
	const themeSwitcherLabel = `${themeSwitcherText.textContent} (${btnToActive.dataset.bsThemeValue})`
	themeSwitcher.setAttribute('aria-label', themeSwitcherLabel)

	if (focus) {
	  themeSwitcher.focus()
	}
  }

  window.matchMedia('(prefers-color-scheme: dark)').addEventListener('change', () => {
	const storedTheme = getStoredTheme()
	if (storedTheme !== 'light' && storedTheme !== 'dark') {
	  setTheme(getPreferredTheme())
	}
  })

  window.addEventListener('DOMContentLoaded', () => {
	showActiveTheme(getPreferredTheme())

	document.querySelectorAll('[data-bs-theme-value]')
	  .forEach(toggle => {
		toggle.addEventListener('click', () => {
		  const theme = toggle.getAttribute('data-bs-theme-value')
		  setStoredTheme(theme)
		  setTheme(theme)
		  showActiveTheme(theme, true)
		})
	  })
  })
})()

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
	
	return false;
}

function ldap_toggle_user(el, samaccountname, toggle) {
	if (toggle == "enable") {
		var isGood=confirm('Are you sure you want to enable this user in the LDAP?');
	} else if (toggle == "disable") {
		var isGood=confirm('Are you sure you want to disable this user in the LDAP?');
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
				alert(`Error ${xhr.status}: ${xhr.statusText}`); // e.g. 404: Not Found
			} else {
				// check if the word 'Error' appeared
				var error_check = xhr.responseText.includes("Error");
				
				// if the response had an error, alert
				if (error_check == true) {
					alert(this.responseText);
				} else {
					// success
					if (toggle == "disable") {
						el.parentNode.parentNode.parentNode.parentNode.style.display='none';	
					}
				}
			}
		}
		
		xhr.onerror = function() {
			alert("Request failed");
		};
	}
	
	return false;
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
	
	return false;
}