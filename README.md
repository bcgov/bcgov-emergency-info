# Emergency Info BC Plugin

## Description

Provides custom functionality for the Emergency Info BC site.

## Setup

1. Activate Custom Post Type UI and Advanced Custom Fields plugins.
2. Import the json file(s) in ./cpt-ui-json/ through CPT UI's import tool.
3. Update fields using ACF's `sync` function in the ACF admin menu.

## Shortcodes

## Hooks

## Event metadata usage

### Adding a field
1. Create the field in the ACF plugin.
2. Ensure the following setting is turned on to allow block binding on the field: ACF Field > Presentation > Allow Access to Value in Editor UI.
3. Open the code editor view in the WP editor and set up block binding on the block where you want the field to display its value, eg.

```html
<!-- wp:paragraph {
    "metadata":{
        "bindings":{
            "content":{
                "source":"acf/field",
                "args":{
                    "key":"recommended_actions"
                }
            }
        }
    }
} -->
<p></p>
<!-- /wp:paragraph -->
```
Alternatively, register a block variation in JS:
```js
registerBlockVariation(
    'core/paragraph',
    {
        name: 'recommended_actions',
        title: 'Recommended Actions',
        attributes: {
            metadata: {
                bindings: {
                    content: {
                        source: 'acf/field',
                        args: {
                            key: 'recommended_actions'
                        }
                    }
                }
            }
        }
    });
```
- This makes it easier to reuse common block bindings as it will create a Recommended Actions block that can be inserted into a page with the block bindings already set up.

4. Optional: Register the post meta in PHP:
```php
register_post_meta(
    'event',
    'recommended_actions',
    [
        'show_in_rest' => true,
        'single' => true,
        'type' => 'string'
    ]
);
```
- This will allow us to use `"source":"core/post-meta"` instead of `acf/field` in step 3. This makes the meta field value actually appear in the post content in the editor, whereas leaving it as `acf/field` just shows "ACF Field".
  - Note: ACF Image fields don't seem to be able to properly bind using `core/post-meta`, only seems to work with `acf/field`.