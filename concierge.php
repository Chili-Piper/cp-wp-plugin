<?php
/**
* Plugin Name: Concierge
* Plugin URI: https://www.chilipiper.com
* Description: Chili Piper Plugin to deploy Concierge on your app
* Version: 0.1
* Author: chilipiper
* Author URI: https://www.chilipiper.com
**/

function generate_concierge_snippet($tenant, $router) {
  return "<script>
  function q(a){return function(){ChiliPiper[a].q=(ChiliPiper[a].q||[]).concat([arguments])}}window.ChiliPiper=window.ChiliPiper||'submit scheduling showCalendar submit widget bookMeeting'.split(' ').reduce(function(a,b){a[b]=q(b);return a},{});
  ChiliPiper.scheduling('" . $tenant . "', '" . $router . "', {})
  </script>
  <script src='https://" . $tenant . ".chilipiper.io/concierge-js/cjs/concierge.js' type='text/javascript' async></script>";
}

function cp_concierge_box_html( $post ) {
  $tenant = get_post_meta( $post->ID, '_cp_concierge_tenant', true );
  $router = get_post_meta( $post->ID, '_cp_concierge_router', true );
	?>
    <div class="cp_concierge__container">
      <div class="cp_concierge_field__container">
        <label for="cp_tenant_id" class="cp_concierge_field__label">Tenant</label>
        <div class="cp_concierge_field__input-container" tabindex="-1">
            <div data-wp-c16t="true" data-wp-component="Flex" class="components-flex">
              <input type="text" name="cp_tenant_id" id="cp_tenant_id" value="<?php echo $tenant; ?>" utocomplete="off" class="components-form-token-field__input" />
        </div>
      </div>
      <div class="cp_concierge_field__container">
        <label for="cp_router_slug"  class="cp_concierge_field__label">Router</label>
        <div class="cp_concierge_field__input-container" tabindex="-1">
            <div data-wp-c16t="true" data-wp-component="Flex" class="components-flex">
              <input type="text" name="cp_router_slug"  value="<?php echo $router; ?>" id="cp_router_slug" autocomplete="off" class="components-form-token-field__input" />
        </div>
      </div>
    </div>
	<?php
}

function cp_add_concierge_box() {
	$screens = [ 'post', 'page' ];
	foreach ( $screens as $screen ) {
		add_meta_box(
			'cp_box_id',
			'Concierge Info',      // Box title
			'cp_concierge_box_html',  // Content callback, must be of type callable
			$screen,                            // Post type
      'side',
      'high'
		);
	}
}
add_action( 'add_meta_boxes', 'cp_add_concierge_box' );

function cp_save_postdata( $post_id ) {
	if ( array_key_exists( 'cp_tenant_id', $_POST ) ) {
		update_post_meta(
			$post_id,
			'_cp_concierge_tenant',
			$_POST['cp_tenant_id']
		);

    update_post_meta(
			$post_id,
			'_cp_concierge_router',
			$_POST['cp_router_slug']
		);
	}
}
add_action( 'save_post', 'cp_save_postdata' );

function cp_concierge_add_snippet( $content ) {
  if ( is_singular() && in_the_loop() && is_main_query() ) {
    global $post;
    $tenant = get_post_meta( $post->ID, '_cp_concierge_tenant', true );
    $router = get_post_meta( $post->ID, '_cp_concierge_router', true );
    if ($tenant && $router) {
      return generate_concierge_snippet($tenant, $router) . $content;
    }
  }
  return $content;
}

add_filter('the_content', 'cp_concierge_add_snippet');

wp_enqueue_style( 'cp-concierge-admin-css', plugin_dir_url( __FILE__ ) . 'admin.css', null, $asset['version'] );