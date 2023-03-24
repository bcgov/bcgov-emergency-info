<?php

namespace Bcgov\Theme\FoodDirectory;

/**
 * A comfortably padded section in which to place elements.
 */
return [
	'title'      => __( 'Event (Query Loop)', 'emergency-info' ),
	'categories' => [ 'emergency-info-bc-general' ],
	'blockTypes' => [ 'core/query' ],
	'content'    => '
        <!-- wp:query {"queryId":34,"query":{"perPage":6,"pages":0,"offset":0,"postType":"event","order":"desc","orderBy":"date","author":"","search":"","exclude":[],"sticky":"","inherit":false,"parents":[]},"displayLayout":{"type":"flex","columns":3},"layout":{"type":"default"}} -->
        <div class="wp-block-query"><!-- wp:post-template {"lock":{"move":true,"remove":true}} -->
        <!-- wp:columns {"verticalAlignment":"top","lock":{"move":true,"remove":true},"style":{"spacing":{"padding":{"top":"0","right":"0","bottom":"0","left":"0"},"margin":{"top":"0"}},"border":{"radius":"0px","width":"1px"}},"borderColor":"gray-80","backgroundColor":"white"} -->
        <div class="wp-block-columns are-vertically-aligned-top has-border-color has-gray-80-border-color has-white-background-color has-background" style="border-width:1px;border-radius:0px;margin-top:0;padding-top:0;padding-right:0;padding-bottom:0;padding-left:0"><!-- wp:column {"verticalAlignment":"top","templateLock":"all","lock":{"move":true,"remove":true},"style":{"spacing":{"blockGap":"0","padding":{"right":"0","left":"0"}}}} -->
        <div class="wp-block-column is-vertically-aligned-top" style="padding-right:0;padding-left:0"><!-- wp:group {"style":{"border":{"width":"0px","style":"none"},"spacing":{"padding":{"top":"var:preset|spacing|20","right":"var:preset|spacing|20","bottom":"0","left":"var:preset|spacing|20"}}},"backgroundColor":"gray-40","layout":{"type":"constrained"}} -->
        <div class="wp-block-group has-gray-40-background-color has-background" style="border-style:none;border-width:0px;padding-top:var(--wp--preset--spacing--20);padding-right:var(--wp--preset--spacing--20);padding-bottom:0;padding-left:var(--wp--preset--spacing--20)"><!-- wp:emergency-info/post-social-share /-->
        
        <!-- wp:group {"style":{"spacing":{"padding":{"bottom":"var:preset|spacing|20"}}},"layout":{"type":"flex","flexWrap":"nowrap"}} -->
        <div class="wp-block-group" style="padding-bottom:var(--wp--preset--spacing--20)"><!-- wp:image {"id":281,"width":75,"height":75,"sizeSlug":"medium","linkDestination":"none","style":{"color":{"duotone":["#000000","#ffffff"]}},"className":"is-style-rounded w-25"} -->
        <figure class="wp-block-image size-medium is-resized is-style-rounded w-25"><img src="https://localhost/eibc/wp-content/uploads/sites/7/2023/02/1146895-300x300.png" alt="" class="wp-image-281" width="75" height="75" title=""/></figure>
        <!-- /wp:image -->
        
        <!-- wp:post-title {"isLink":true,"style":{"color":{"text":"#202020"}},"className":"w-75","fontSize":"large"} /--></div>
        <!-- /wp:group --></div>
        <!-- /wp:group -->
        
        <!-- wp:group {"style":{"spacing":{"padding":{"top":"var:preset|spacing|20","right":"var:preset|spacing|20","bottom":"var:preset|spacing|20","left":"var:preset|spacing|20"}}},"layout":{"type":"constrained"}} -->
        <div class="wp-block-group" style="padding-top:var(--wp--preset--spacing--20);padding-right:var(--wp--preset--spacing--20);padding-bottom:var(--wp--preset--spacing--20);padding-left:var(--wp--preset--spacing--20)"><!-- wp:mfb/meta-field-block {"fieldType":"acf","fieldName":"affected_area","hideEmpty":true,"prefix":"\u003cspan class=\u0022material-symbols-outlined\u0022\u003elocation_on\u003c/span\u003e\u003cb\u003eAffected area:\u003c/b\u003e"} /-->
        
        <!-- wp:mfb/meta-field-block {"fieldType":"acf","fieldName":"issued_by","hideEmpty":true,"prefix":"\u003cspan class=\u0022material-symbols-outlined\u0022\u003eaccount_balance\u003c/span\u003e\u003cb\u003eIssued by:\u003c/b\u003e"} /-->
        
        <!-- wp:post-excerpt {"showMoreOnNewLine":false} /--></div>
        <!-- /wp:group --></div>
        <!-- /wp:column --></div>
        <!-- /wp:columns -->
        <!-- /wp:post-template --></div>
        <!-- /wp:query -->
    ',
];
