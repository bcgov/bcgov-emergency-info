# Forking this repo
After forking this repo to create a new plugin:
1. Replace all occurrences of "Plugin_Name" and "plugin-name" in files with the name of the new plugin.
1. Update `package.json` and `composer.json` with new plugin name, author, etc.
1. Update `plugin-name.php` (should be renamed to correct plugin name) with correct WP plugin information.
1. Functions in `src/scripts/admin.js` and `public.js` can be removed as they only exist to demonstrate unit tests are working correctly.
1. Remove these instructions.

# [Add plugin name here]

## Description
[Add plugin description here]

## Shortcodes

## Hooks

