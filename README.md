# Emergency Info BC Plugin

## Description
Provides custom functionality for the Emergency Info BC site.

## Setup
1. Activate Custom Post Type UI and Advanced Custom Fields plugins.
1. Import the xml file(s) in ./patterns-xml/ through the Wordpress import tool.
1. Import the json file(s) in ./cpt-ui-json/ through CPT UI's import tool.
1. Update fields using ACF's `sync` function in the ACF admin menu.

### NAAD Requests
To allow the plugin to accept requests from the NAAD connector some additional configuration is required:
1. Choose a user (or create a new one) that has the `manage_options` capability (Administrator role and up).
2. Navigate to this user's settings page (Users > {user from step 1} > Edit).
3. Under Application Passwords section, set `New Application Password Name` to "NAAD".
4. Click `Add New Application Password` button.
5. Copy the generated password and provide it to the NAAD Connector instance along with the username from step 1.
6. To test the authentication, run the following curl command replacing the values as needed: `curl https://localhost/{site slug}/wp-json/naad/v1/alert -X POST -k --user "{username}:{application password}"`.
   * If you receive a 200 response, the authentication is working correctly.
   * A 401 response indicates that the username and/or password are incorrect.

## Shortcodes

## Hooks
### Actions
1. `eibc_create_event`: Programmatically creates an Event post with the given parameters. Intended to be used by the Earthquake Early Warning System.
    ```php
    do_action(
        'eibc_create_event',
        'Earthquake Detected in B.C.', # Event title
        'An earthquake of magnitude 7.1 has been detected in Northern B.C.', # Event excerpt
        'earthquake', # Hazard type slug
        1704311951, # Unix time when event occurred or alert was sent
        'Northern B.C.', # Card value 1, for earthquakes this is the location. Optional
        '7.1' # Card value 2, for earthquakes this is the magnitude. Optional
    );
    ```

## Unit Tests
Requirements: MySQL server, PHP 7.4, SVN
1. From project root, run the command `composer run setup-tests` (see: https://make.wordpress.org/cli/handbook/misc/plugin-unit-tests/#3-initialize-the-testing-environment-locally). This only needs to be run the first time to set up the WP test environment.
1. Run `composer run test-wp` to execute tests
	- If a "could not establish connection to database" error occurs, make sure the MySQL server is running (`mysqld` command in terminal)