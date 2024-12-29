<?php namespace Administration\Modules\WordPress\WordPressPages;

defined( 'ABSPATH' ) || exit;

/**
 * Plugin Indstillinger Template
 *
 * Viser indstillingssiden for pluginet i admin-grænsefladen.
 *
 * @package Administration\Modules\WordPress\WordPressPages
 * @since WordPress 1.0.0
 * @version 1.0.0
 * 
 */

?>

<div class="wrap">
    <h1><?php esc_html_e( 'Plugin Indstillinger', 'administration' ); ?></h1>
    <form method="post" action="options.php">
        <?php
        settings_fields( 'administration_settings_group' );
        do_settings_sections( 'administration-settings' );
        submit_button();
        ?>
    </form>
</div>
