# WP Lock Users

A WordPress plugin that will lock out users after 90 days of inactivity.

## Description

This is a plugin that disable's a site user's account after 90 days. Once deactivated, a manual activation by the site admin is necessary.

## Features:
*	Upon activation, each time a customer logs in, a timestamp option will be added to their account. 
*	Once the 90 days has past and no login has happened, they will be locked out on next login attempt.
*   All of their content will remain in tact.
*   The plugin will disable the users when they try to log in again.
*   Bulk edit to quickly enable or disable multiple accounts.
*   A new `Has Access?` sortable column has been added to the Users page to show the status of each user.
*   A link to reactivate each disabled user individually from the all Users page.
*	Admins have the ability to be able change the number of days before lockout.

## Design Considerations
*	The logic runs when the user tries to log in, not in the background with a cron job.

## Future Enhancements
*	Add an option to Lock/Unlock account from the individual profile page
*	Add a column to show the last login 
*	Create a cron job to automatically lock out users on the 90th day
*	Send out email messages to site owner and user to let them know their account has been locked
*	When user is unlocked, it'd be nice to delete the option from the WordPress Database

## Screenshots

### Settings (`Users -> Lock Inactive Settings`)
![https://cloudup.com/cabo20eNJxU](https://cloudup.com/cabo20eNJxU+) 
Full Size: https://cloudup.com/cabo20eNJxU

### Users columns (`Users`)
![https://cloudup.com/cDiMyypQeKC](https://cloudup.com/cDiMyypQeKC+)
Link to Image: https://cloudup.com/cDiMyypQeKC

### Bulk update Users
![https://cloudup.com/cGh2z2_HV7j](https://cloudup.com/cGh2z2_HV7j+)
Link to Image: https://cloudup.com/cGh2z2_HV7j

## Installation
1. Download the file and upload to plugins page
2. Activate plugin

When the plugin is activated, it will begin working with the default settings. Please see `Users -> Lock Inactive Settings`
