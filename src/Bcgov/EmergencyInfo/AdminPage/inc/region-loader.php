<?php
/**
 * Region Loader page.
 *
 * @package Bcgov
 */

// Get local region JSON data to auto-populate the textarea.
// phpcs:ignore WordPress.WP.AlternativeFunctions.file_get_contents_file_get_contents
$region_json_data = file_get_contents( __DIR__ . '/../../../../../cpt-ui-json/regions.json' );

$regions          = get_terms(
    [
		'taxonomy'   => 'region',
		'hide_empty' => false,
		'number'     => 1,
	]
);
$do_regions_exist = count( $regions ) > 0;
?>

<?php if ( $do_regions_exist ) : ?>
        <div class="notice-warning notice">
            <p>Regions already exist. This tool can only be used with an empty set of Region terms.</p>
        </div>
    <?php endif ?>
<h2>Region Loader</h2>
<div class="explanation">
    <p>The input below should be in JSON form with the following structure:</p>
    <code>
        [<br>
            {<br>
                "label": "Victoria",<br>
                "regionGroups": ["Capital Regional District", "Tsunami Zone A", ...]<br>
            },<br>
            {<br>
                "label": "Vancouver",<br>
                "regionGroups": ["Metro Vancouver Regional District", "Tsunami Zone B", ...]<br>
            },<br>
            ...<br>
        ]
    </code>
    <p>The input will be pre-populated with data from <code>cpt-ui-json/regions.json</code>.</p>
</div>
<form action="<?php echo esc_url( admin_url( 'admin-ajax.php' ) ); ?>" method="post" id="region_loader_form">
    <input type="hidden" name="action" value="emergency_info_load_region_terms">
    <?php wp_nonce_field( 'region_loader_form_nonce', 'region_loader_form_nonce' ); ?>
    <fieldset <?php echo $do_regions_exist ? 'disabled' : ''; ?>">
        <label for="region_json">Region JSON</label>
        <div>
            <textarea id="region_json" name="region_json" cols="120" rows="20" required><?php echo esc_html( $region_json_data ); ?></textarea>
        </div>
        <p class="submit">
            <button type="submit" name="submit" id="submit" class="button button-primary"><?php esc_html_e( 'Submit' ); ?></button>
        </p>
    </fieldset>
</form>
