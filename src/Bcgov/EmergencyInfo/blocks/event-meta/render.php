<?php
    $event = get_post();
    if (!$event) {
        return '';
    }
    $status = get_field('status', $event->ID);
    $urgency_field = get_field_object('urgency', $event->ID, false);
    $urgency = $urgency_field['choices'][$urgency_field['value']];
    $hazard_types = get_the_terms($event, 'hazard_type');
    if (!$hazard_types || count($hazard_types) < 1) {
        return '';
    }
    $hazard_type = $hazard_types[0];
    $hazard_meta = get_term_meta($hazard_type->term_id);
    $hazard_image_id = $hazard_meta['hazard_image'][0];
    if ($hazard_image_id) {
        $hazard_image_src = wp_get_attachment_image_src($hazard_image_id);
        $hazard_image_srcset = wp_get_attachment_image_srcset($hazard_image_id);
    }
?>

<div class="is-layout-flow entry-content alignwide wp-block-post-content">
    <div class="is-layout-constrained wp-block-group has-background" style="background-color:#d8efff">
        <div class="is-layout-flex wp-container-16 wp-block-columns is-style-constrained">
            <div class="is-layout-flow wp-block-column" style="flex-basis:66.66%">
                <div class="is-layout-constrained wp-block-group">
                    <h2><span><?php echo $hazard_type->name; ?></span><span><?php echo ' | ' . $urgency; ?></span></h2>
                    <p><?php echo $hazard_type->description; ?></p>
                    <div class="is-layout-flex wp-block-buttons">
                        <div class="wp-block-button has-size-regular is-style-fill"><a tabindex="0" class="wp-block-button__link has-foreground-color has-pale-cyan-blue-background-color has-text-color has-background wp-element-button" style="border-radius:100px"><?php echo $status['label']; ?></a></div>
                    </div>
                </div>
            </div>
            <div class="is-layout-flow wp-block-column" style="flex-basis:33.33%">
                <?php if ( isset( $hazard_image_src ) ): ?>
                    <figure class="wp-block-image size-medium has-custom-border is-style-variable-ratio"><img loading="lazy" decoding="async" width="300" height="192" data-print-width="25" alt="" class="wp-image-81" style="border-radius:0px" title="" srcset="<?php echo $hazard_image_srcset; ?>" sizes="(max-width: 300px) 100vw, 300px"></figure>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>