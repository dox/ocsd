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
	if (theme === 'auto') {
	  document.documentElement.setAttribute('data-bs-theme',
		window.matchMedia('(prefers-color-scheme: dark)').matches ? 'dark' : 'light')
	} else {
	  document.documentElement.setAttribute('data-bs-theme', theme)
	}
  }

  const showActiveTheme = (theme, focus = false) => {
	const themeSwitcher = document.querySelector('#bd-theme')
	const activeThemeIcon = document.querySelector('.theme-icon-active')
	const btnToActivate = document.querySelector(`[data-bs-theme-value="${theme}"]`)
  
	// Reset button states
	document.querySelectorAll('[data-bs-theme-value]').forEach(btn => {
	  btn.classList.remove('active')
	  btn.setAttribute('aria-pressed', 'false')
	})
  
	// Mark current button as active
	btnToActivate.classList.add('active')
	btnToActivate.setAttribute('aria-pressed', 'true')
  
	// Replace the SVG icon
	const newIcon = btnToActivate.querySelector('svg').cloneNode(true)
	activeThemeIcon.replaceWith(newIcon)
	newIcon.classList.add('theme-icon-active')
  
	// Update label
	themeSwitcher.setAttribute('aria-label', `Toggle theme (${theme})`)
	if (focus) themeSwitcher.focus()
  }

  setTheme(getPreferredTheme())

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

  window.matchMedia('(prefers-color-scheme: dark)').addEventListener('change', () => {
	const storedTheme = getStoredTheme()
	if (storedTheme !== 'light' && storedTheme !== 'dark') {
	  setTheme(getPreferredTheme())
	}
  })
})()

document.querySelectorAll('.ldap-toggle-link').forEach(link => {
	link.addEventListener('click', function(e) {
		e.preventDefault();

		const username = this.dataset.username;
		const action = this.dataset.action;
		const statusSpan = document.querySelector(`#status-${username}`);

		fetch('actions/ldap_toggle.php', {
			method: 'POST',
			headers: {
				'Content-Type': 'application/x-www-form-urlencoded'
			},
			body: `username=${encodeURIComponent(username)}&action=${encodeURIComponent(action)}`
		})
		.then(response => response.text())
		.then(result => {
			statusSpan.innerHTML = result;
			
			// Initialide the popover
			const popoverTriggerList = [].slice.call(statusSpan.querySelectorAll('[data-bs-toggle="popover"]'));
			popoverTriggerList.forEach(function (popoverTriggerEl) {
				new bootstrap.Popover(popoverTriggerEl);
			});
		})
		.catch(error => {
			statusSpan.textContent = 'Error';
			console.error('Error:', error);
		});
	});
});
// Delete listener
document.querySelectorAll('.ldap-delete-link').forEach(link => {
	link.addEventListener('click', function(e) {
		e.preventDefault();

		if (!confirm('Are you sure you want to delete this user?')) {
			return;
		}

		const username = this.dataset.username;
		const statusSpan = document.querySelector(`#status-${username}`);

		fetch('actions/ldap_delete.php', {
			method: 'POST',
			headers: {
				'Content-Type': 'application/x-www-form-urlencoded'
			},
			body: `username=${encodeURIComponent(username)}`
		})
		.then(response => response.text())
		.then(result => {
			statusSpan.innerHTML = result;
			
			// Initialide the popover
			const popoverTriggerList = [].slice.call(statusSpan.querySelectorAll('[data-bs-toggle="popover"]'));
			popoverTriggerList.forEach(function (popoverTriggerEl) {
				new bootstrap.Popover(popoverTriggerEl);
			});
		})
		.catch(error => {
			statusSpan.textContent = 'Error';
			console.error('Error:', error);
		});
	});
});

const popoverTriggerList = document.querySelectorAll('[data-bs-toggle="popover"]')
const popoverList = [...popoverTriggerList].map(popoverTriggerEl => new bootstrap.Popover(popoverTriggerEl))