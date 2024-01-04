# Emergency Info BC Plugin

## Description
Provides custom functionality for the Emergency Info BC site.

## Setup
1. Activate Custom Post Type UI and Advanced Custom Fields plugins.
1. Import the xml file(s) in ./patterns-xml/ through the Wordpress import tool.
1. Import the json file(s) in ./cpt-ui-json/ through CPT UI's import tool.
1. Update fields using ACF's `sync` function in the ACF admin menu.

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