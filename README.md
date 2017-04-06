This is a WordPress plugin to check if a user exists on the site by email.

GET `wp-json/calderawp/user-exists?email=roy%40roysivan.com` returns true if Roy is a user on your site, false if not. Also, Hi Roy.

# Read This RE: Security/ Privacy
This plugin, when active could make brute forcing your login easier, since it could be used to check for user before trying a stolen password for that user. It could also leak your user list. I could say the same thing about the WordPress registration form, but you would probably tell me you have a brute force protection plugin and/ or a WAF to stop that.

This plugin integrates with [Login Lockdown](https://wordpress.org/plugins/login-lockdown/). If Login Lockdown is active, failed user name exists checks will be registered as a failed login *if you have the "Lockout Invalid Usernames?" setting checked.* If a username check fails more than the amount of times you allowed in Login LockDown settings, then a lock out is triggered and all user exists checks are blocked until the lockout is lifted according to Login Lockdown settings.

If you wish to create your own system, for throttling or brute force protection The "cwp_user_exits_failed" action and "cwp_user_exists_pre_check" filter are provided. If you return a WP_Error at "cwp_user_exists_pre_check" filter, then the check will not run and the REST API will return an error. Use this to count past fails or check a single use token or a nonce. When a user exists check fails the "cwp_user_exits_failed"  action is fired. Use this to count fails.