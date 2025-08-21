// TEMPORARY: For testing the staff management interface
// Run this in your browser console to set a valid admin token

// Set the test token in localStorage
localStorage.setItem('admin_token', '37|YLDqGQmWaifp1hZNkdkn0mInEILQt2iBNB9jLqap391d1a93');

// Refresh the page to test
window.location.reload();

// NOTE: In production, you should:
// 1. Access /admin/login first
// 2. Login with valid credentials  
// 3. The login process should set the admin_token in localStorage
// 4. Then access /admin/staff
