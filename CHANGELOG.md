# Changelog

## 1.2.0: June 27, 2024

* Override Homepage sorting so that State of Emergency events are prioritized ([DESCW-2433](https://apps.itsm.gov.bc.ca/jira/browse/DESCW-2435))
* Changed priority of action 'enqueue_styles' from 20 to 5.
* Added Government of BC logo in State of Emergency event header and cards ([DESCW-2435](https://apps.itsm.gov.bc.ca/jira/browse/DESCW-2435))
* Added "has_striped_border" meta field and relevant css for hazard types ([DESCW-2432](https://apps.itsm.gov.bc.ca/jira/browse/DESCW-2432))
* Updated `query_loop_block_query_vars` filter to allow query loop to be used on Hazard Type archive pages ([DESCW-2450](https://apps.itsm.gov.bc.ca/jira/browse/DESCW-2450))
* Changed hazard_type permalinks to /hazard/hazard-name from /hazard_types/hazard-name ([DESCW-2355](https://apps.itsm.gov.bc.ca/jira/browse/DESCW-2355))
* Added wp-scripts markdown linting command to package.json
* Affected Area is no longer a required field ([DESCW-2372](https://apps.itsm.gov.bc.ca/jira/browse/DESCW-2372))
* Updated to use node 20
* Updated to use new eslint and stylelint dependencies instead of webpack-wordpress
* Replaced usage of browser incompatible Set functions: difference and union
* Override subscription list filter to remove taxonomy labels ([DESCW-2354](https://apps.itsm.gov.bc.ca/jira/browse/DESCW-2354))
* Created filter to control which posts are able trigger notifications ([DESCW-2249](https://apps.itsm.gov.bc.ca/jira/browse/DESCW-2249))
* Fixed bug with hazard type color styles being added to wrong theme.json property
* Updated Event CPT to allow export and allow rewrite without front for permanlinks (/post-slug works in addition to /events/post-slug)

## 1.1.0: April 12, 2024

* Updated Subscribe Form: ([DESCW-2246](https://apps.itsm.gov.bc.ca/jira/browse/DESCW-2246))
    * Now shows associated region groups below selected regions
    * Autocomplete search sorts by relevancy
    * Autocomplete no longer shows already selected regions as options
    * Fixed in content, style that differ from mockups
* Updated Subscribe Form to be able to get email address when updating an existing subscription ([DESCW-2175](https://apps.itsm.gov.bc.ca/jira/browse/DESCW-2175))
* Improved Subscribe Form validation ([DESCW-2176](https://apps.itsm.gov.bc.ca/jira/browse/DESCW-2176))
* Added Region Loader feature to allow Region and Region Group taxonomy terms to be inserted via JSON in an admin page ([DESCW-2248](https://apps.itsm.gov.bc.ca/jira/browse/DESCW-2248))
* Added Region Group control in editor to allow admins to select regions via the Region Group controls ([DESCW-2130](https://apps.itsm.gov.bc.ca/jira/browse/DESCW-2130))
* Flattened and reorganized municipalities and first nations json ([DESCW-2127](https://apps.itsm.gov.bc.ca/jira/browse/DESCW-2127))
* Created Event Status block to display event status messages at the top of Event pages ([DESCW-2128](https://apps.itsm.gov.bc.ca/jira/browse/DESCW-2128))
* Created Region Group Options taxonomy and Region Group ACF field to allow Region terms to be grouped ([DESCW-2129](https://apps.itsm.gov.bc.ca/jira/browse/DESCW-2129))
* Added "Select all regions" feature to Subscribe Form block ([DESCW-2124](https://apps.itsm.gov.bc.ca/jira/browse/DESCW-2124))
* Improved Subscribe Form block frontend styling and content to better reflect wireframes ([DESCW-2000](https://apps.itsm.gov.bc.ca/jira/browse/DESCW-2000))
* Improved Subscribe Form block editor styling and content to match frontend ([DESCW-1997](https://apps.itsm.gov.bc.ca/jira/browse/DESCW-1997))
* Fixed bug with ACF/CPTUI local json saving/loading hooks sometimes saving json files to wrong repo when the emergencyinfo and des-notify-client plugin are both activated ([DESCW-2071](https://apps.itsm.gov.bc.ca/jira/browse/DESCW-2071))
* Added required "I agree..." checkbox to Subscribe Form block ([DESCW-2024](https://apps.itsm.gov.bc.ca/jira/browse/DESCW-2024))
* Updated Subscribe Form block to make use of Notify plugin's maintenance mode option ([DESCW-2022](https://apps.itsm.gov.bc.ca/jira/browse/DESCW-2022))
* Replaced requests to admin-post to admin-ajax in order to fix bugs on test/production servers caused by access restrictions to wp-admin/ routes
* Updated Subscribe Form accessibility, now navigable via keyboard and screen reader ([DESCW-1996](https://apps.itsm.gov.bc.ca/jira/browse/DESCW-1996))
* Updated Subscribe Form to set region selections based on query params to allow users to update their subscriptions ([DESCW-1951](https://apps.itsm.gov.bc.ca/jira/browse/DESCW-1951))
* Updated region data to be hierarchical
* Added region data json, minor changes to Subscribe Form block to accommodate large number of autocomplete options ([DESCW-1952](https://apps.itsm.gov.bc.ca/jira/browse/DESCW-1952))
* Added autocomplete functionality to Subscribe Form block for selecting regions ([DESCW-1975](https://apps.itsm.gov.bc.ca/jira/browse/DESCW-1975))
* Created EIBC Subscribe Form block ([DESCW-1974](https://apps.itsm.gov.bc.ca/jira/browse/DESCW-1974))
* Added Region taxonomy to Events

## 1.0.3: January 11, 2024

* Updated ACF fields for hazard type card metadata ([DESCW-1893](https://apps.itsm.gov.bc.ca/jira/browse/DESCW-1893))
* Fixed error caused by calling remove_menu_page() in wrong hook (admin_init -> admin_menu)

## 1.0.2: December 21, 2023

* Added requirements to the Hazard Type Image ACF field, added image preview and image column to table ([DESCW-1797](https://apps.itsm.gov.bc.ca/jira/browse/DESCW-1797))
* Removed AIOSEO "Don't update the modified date" checkbox from editor for Events ([DESCW-1796](https://apps.itsm.gov.bc.ca/jira/browse/DESCW-1796))
* Removed Comments item from admin menu ([DESCW-1795](https://apps.itsm.gov.bc.ca/jira/browse/DESCW-1795))

## 1.0.1: November 29, 2023

* Updated hazard type styles so that adding a new hazard type no longer requires changes to code ([DESCW-1632](https://apps.itsm.gov.bc.ca/jira/browse/DESCW-1632))
* Fixed minor accessibility issues in blocks ([DESCW-1756](https://apps.itsm.gov.bc.ca/jira/browse/DESCW-1756))

## 1.0.0: October 25, 2023

* Updated PHP coding standards to latest WP ruleset ([DESCW-1583](https://apps.itsm.gov.bc.ca/jira/browse/DESCW-1583))
* Added copy link feature to Social Share block ([DESCW-1425](https://apps.itsm.gov.bc.ca/jira/browse/DESCW-1425))
* Updated emergency-map reusable block to include new Twitter block design ([DESCW-1362](https://apps.itsm.gov.bc.ca/jira/browse/DESCW-1362))
* Updated event status alert css to only appear when event is inactive ([DESCW-1369](https://apps.itsm.gov.bc.ca/jira/browse/DESCW-1369))
* Updated templates so only homepage requires special margins/padding, added Homepage General Block pattern ([DESCW-1357](https://apps.itsm.gov.bc.ca/jira/browse/DESCW-1357))
* Added styling for Bootstrap Accordions to match site theming ([DESCW-1377](https://apps.itsm.gov.bc.ca/jira/browse/DESCW-1377))
* Updated Emergency Alert pattern to remove subheading ([DESCW-1363](https://apps.itsm.gov.bc.ca/jira/browse/DESCW-1363))
* Updated Emergency Alert and Emergency Alert pill block CSS to use inactive styles when event is set inactive ([DESCW-1360](https://apps.itsm.gov.bc.ca/jira/browse/DESCW-1360))
* Updated Event Query Loop pattern to include Hazard title ([DESCW-1356](https://apps.itsm.gov.bc.ca/jira/browse/DESCW-1356))
* Improved accessibility and general style issues across patterns ([DESCW-1358](https://apps.itsm.gov.bc.ca/jira/browse/DESCW-1358))
* Added Hidden ACF field to allow posts to be hidden from the homepage while still being active ([DESCW-1359](https://apps.itsm.gov.bc.ca/jira/browse/DESCW-1359))
* Updated Hazard Type select input on Event pages to default properly to the first taxonomy term ([DESCW-1361](https://apps.itsm.gov.bc.ca/jira/browse/DESCW-1361))
* Created Catastrophic Earthquake and Tsunami patterns ([DESCW-1209](https://apps.itsm.gov.bc.ca/jira/browse/DESCW-1209))
* Reverted removal of Emergency Alert color, still needed for post-emergency-alert block ([DESCW-1254](https://apps.itsm.gov.bc.ca/jira/browse/DESCW-1254))
* Updated css to use BC Sans font
* Fixed bug with Hazard Type select input on Event pages only showing the first 10 results ([DESCW-1254](https://apps.itsm.gov.bc.ca/jira/browse/DESCW-1254))
* Added support for all current hazard types ([DESCW-1254](https://apps.itsm.gov.bc.ca/jira/browse/DESCW-1254))
* Fixed various accessibility issues ([DESCW-1211](https://apps.itsm.gov.bc.ca/jira/browse/DESCW-1211))
* Updated patterns for accessibility ([DESCW-1211](https://apps.itsm.gov.bc.ca/jira/browse/DESCW-1211))
* Updated patterns with minor fixes ([DESCW-1229](https://apps.itsm.gov.bc.ca/jira/browse/DESCW-1229))
* Created Tsunami event pattern ([DESCW-1176](https://apps.itsm.gov.bc.ca/jira/browse/DESCW-1176))
* Created Aggregated Flooding event pattern ([DESCW-1221](https://apps.itsm.gov.bc.ca/jira/browse/DESCW-1221))
* Created Heat event pattern, updated Emergency Alert pattern ([DESCW-1175](https://apps.itsm.gov.bc.ca/jira/browse/DESCW-1175))
* Extended ability to override the hazard name in event headers to all hazard types instead of only Generic ([DESCW-1216](https://apps.itsm.gov.bc.ca/jira/browse/DESCW-1216))
* Updated all event patterns with minor fixes ([DESCW-1215](https://apps.itsm.gov.bc.ca/jira/browse/DESCW-1215))
* Added Emergency Alert pattern, created Post Emergency Alert block for displaying badge on Event cards ([DESCW-1202](https://apps.itsm.gov.bc.ca/jira/browse/DESCW-1202))
* Updated Homepage pattern ([DESCW-1177](https://apps.itsm.gov.bc.ca/jira/browse/DESCW-1177))
* Improved Aggregate Event cards metadata display, created Post Meta Display block, updated ACF fields to not require full icon HTML  ([DESCW-1138](https://apps.itsm.gov.bc.ca/jira/browse/DESCW-1138))
* Added Aggregated Event cards pattern ([DESCW-1191](https://apps.itsm.gov.bc.ca/jira/browse/DESCW-1191))
* Updated Event cards meta field display and ACF meta fields for various hazard types ([DESCW-1137](https://apps.itsm.gov.bc.ca/jira/browse/DESCW-1137))
* Added override for Post Hazard Title block for Generic hazard type ([DESCW-1191](https://apps.itsm.gov.bc.ca/jira/browse/DESCW-1191))
* Cleanup of unused code: med-fi patterns, unused blocks, unused Resource post type, etc. ([DESCW-1178](https://apps.itsm.gov.bc.ca/jira/browse/DESCW-1178))
* Added is_aggregated_event meta field to Evacuation field group, changed Event query loop to order by metadata date/time ([DESCW-1136](https://apps.itsm.gov.bc.ca/jira/browse/DESCW-1136))
* Updated Event CSS and Event Info reusable block for inactive styles ([DESCW-1139](https://apps.itsm.gov.bc.ca/jira/browse/DESCW-1139))
* Created Amber Alert event pattern ([DESCW-1094](https://apps.itsm.gov.bc.ca/jira/browse/DESCW-1094))
* Updated Event Query Loop built-in pattern, updated ACF fields ([DESCW-1095](https://apps.itsm.gov.bc.ca/jira/browse/DESCW-1095))
* Created Earthquake event pattern, created Post Hazard Title block, updated reusable Event Info block ([DESCW-1093](https://apps.itsm.gov.bc.ca/jira/browse/DESCW-1093))
* Added Flood event patterns ([DESCW-1092](https://apps.itsm.gov.bc.ca/jira/browse/DESCW-1092))
* Updated Homepage pattern ([DESCW-1057](https://apps.itsm.gov.bc.ca/jira/browse/DESCW-1057))
* Changed Event meta evacuation field to have 3 options ([DESCW-1055](https://apps.itsm.gov.bc.ca/jira/browse/DESCW-1055))
* Added Wildfire event pattern, changed event header to reusable block, fixed margin/padding issue on small screen sizes ([DESCW-1091](https://apps.itsm.gov.bc.ca/jira/browse/DESCW-1091))
* Improved Post Social Share block display, added Bootstrap dependency and Bootstrap theming overrides, added event meta fields per UX changes ([DESCW-1018](https://apps.itsm.gov.bc.ca/jira/browse/DESCW-1018))
* Improved hidden block display in editor, improved Post Hazard Image block display, added theme colours ([DESCW-1056](https://apps.itsm.gov.bc.ca/jira/browse/DESCW-1056))
* Added homepage patterns, Visibility toggle, Post Hazard Image block, improved hazard color handling ([DESCW-1026](https://apps.itsm.gov.bc.ca/jira/browse/DESCW-1026))
* Improved Event Meta block styling ([DESCW-1015](https://apps.itsm.gov.bc.ca/jira/browse/DESCW-1015))
* Added hide block attribute to all blocks to allow user to keep a block in the editor but not render it on frontend
* Updated Event Meta block to use hazard colours, improved styling ([DESCW-1017](https://apps.itsm.gov.bc.ca/jira/browse/DESCW-1017))
* Query Loops targeting Event post type now filter on active Events ([DESCW-1016](https://apps.itsm.gov.bc.ca/jira/browse/DESCW-1016))
* Added amber alert event patterns ([DESCW-1010](https://apps.itsm.gov.bc.ca/jira/browse/DESCW-1010))
* Added earthquake event patterns ([DESCW-1009](https://apps.itsm.gov.bc.ca/jira/browse/DESCW-1009))
* Added flood event patterns ([DESCW-960](https://apps.itsm.gov.bc.ca/jira/browse/DESCW-960))
* Added select input for Hazard Type taxonomy ([DESCW-1005](https://apps.itsm.gov.bc.ca/jira/browse/DESCW-1005))
* Unregistered default Block Theme patterns ([DESCW-1003](https://apps.itsm.gov.bc.ca/jira/browse/DESCW-1003))
* Added Event Query Loop block pattern ([DESCW-961](https://apps.itsm.gov.bc.ca/jira/browse/DESCW-961))
* Social Share block ([DESCW-979](https://apps.itsm.gov.bc.ca/jira/browse/DESCW-979))
* Save/load ACF and CPT UI settings from JSON ([DESCW-980](https://apps.itsm.gov.bc.ca/jira/browse/DESCW-980))
* Event Metadata block ([DESCW-958](https://apps.itsm.gov.bc.ca/jira/browse/DESCW-958))
* Changed block category to "theme" to avoid a bug with emergency-info category only appearing in Editor for CPTs ([DESCW-924](https://apps.itsm.gov.bc.ca/jira/browse/DESCW-924))
* Resource List block ([DESCW-924](https://apps.itsm.gov.bc.ca/jira/browse/DESCW-924))
* Resources CPT ([DESCW-923](https://apps.itsm.gov.bc.ca/jira/browse/DESCW-923))
* Events CPT ([DESCW-881](https://apps.itsm.gov.bc.ca/jira/browse/DESCW-881))
* Active Events block ([DESCW-922](https://apps.itsm.gov.bc.ca/jira/browse/DESCW-922))
* Fixed incorrect name in composer.json ([DESCW-877](https://apps.itsm.gov.bc.ca/jira/browse/DESCW-877))
* Support for block patterns ([DESCW-877](https://apps.itsm.gov.bc.ca/jira/browse/DESCW-877))
* Support for Gutenberg metadata blocks ([DESCW-877](https://apps.itsm.gov.bc.ca/jira/browse/DESCW-877))
* Initial plugin build ([DESCW-877](https://apps.itsm.gov.bc.ca/jira/browse/DESCW-877))
* Replaced plugin-template references with EIBC
