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
	if (!themeSwitcher || !activeThemeIcon || !btnToActivate) return
  
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
		const statusKey = this.dataset.cudid || username;
		const statusSpan = document.querySelector(`#status-${CSS.escape(statusKey)}`);
		if (!statusSpan) return;

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
		const statusKey = this.dataset.cudid || username;
		const statusSpan = document.querySelector(`#status-${CSS.escape(statusKey)}`);
		if (!statusSpan) return;

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

// Provision listener
document.querySelectorAll('.ldap-provision-link').forEach(link => {
	link.addEventListener('click', function(e) {
		e.preventDefault();

		if (!confirm('Are you sure you want to provision this user?  This action will also email the user a copy of their details')) {
			return;
		}

		const cudid = this.dataset.cudid;
		const statusSpan = document.querySelector(`#status-${cudid}`);

		fetch('actions/ldap_provision.php', {
			method: 'POST',
			headers: {
				'Content-Type': 'application/x-www-form-urlencoded'
			},
			body: `cudid=${encodeURIComponent(cudid)}`
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

// Bulk LDAP actions on the CUD persons table.
const bulkSelectAll = document.querySelector('#select-all-persons');
const bulkAction = document.querySelector('#bulk-ldap-action');
const bulkSubmit = document.querySelector('#bulk-ldap-submit');
const bulkStatus = document.querySelector('#bulk-ldap-status');
if (bulkSelectAll && bulkAction && bulkSubmit) {
	const selections = () => [...document.querySelectorAll('.person-select:checked')];
	const updateBulkState = () => {
		bulkSubmit.disabled = !bulkAction.value || selections().length === 0;
	};
	bulkSelectAll.addEventListener('change', () => {
		document.querySelectorAll('.person-select').forEach(box => { box.checked = bulkSelectAll.checked; });
		updateBulkState();
	});
	document.querySelectorAll('.person-select').forEach(box => box.addEventListener('change', updateBulkState));
	bulkAction.addEventListener('change', updateBulkState);
	bulkSubmit.addEventListener('click', async () => {
		const selected = selections();
		const action = bulkAction.value;
		if ((action === 'delete' || action === 'provision') && !confirm(`Are you sure you want to ${action} ${selected.length} user(s)?${action === 'provision' ? ' Provisioning will email each user their details.' : ''}`)) return;
		bulkSubmit.disabled = true;
		bulkStatus.textContent = 'Processing…';
		let succeeded = 0;
		for (const box of selected) {
			if (action === 'provision' && box.dataset.hasLdap === '1') continue;
			const body = action === 'provision' ? `cudid=${encodeURIComponent(box.value)}` : `username=${encodeURIComponent(box.dataset.username)}${action === 'delete' ? '' : `&action=${action}`}`;
			if (action !== 'provision' && !box.dataset.username) continue;
			const endpoint = action === 'provision' ? 'actions/ldap_provision.php' : action === 'delete' ? 'actions/ldap_delete.php' : 'actions/ldap_toggle.php';
			try { const response = await fetch(endpoint, { method: 'POST', headers: {'Content-Type': 'application/x-www-form-urlencoded'}, body }); if (response.ok) succeeded++; } catch (error) { console.error(error); }
		}
		bulkStatus.textContent = `${succeeded}/${selected.length} completed. Reloading…`;
		setTimeout(() => window.location.reload(), 800);
	});
}
