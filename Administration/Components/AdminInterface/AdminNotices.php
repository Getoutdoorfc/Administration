<?php
namespace Administration\Components\AdminInterface;

defined( 'ABSPATH' ) || exit;

/**
 * Class AdminNotices
 *
 * Viser administrative meddelelser i WordPress-dashboardet.
 *
 * @package Administration\AdminInterface
 */
class AdminNotices {

    /**
     * Constructor.
     */
    public function __construct() {
        add_action( 'admin_notices', array( $this, 'display_admin_notices' ) );
    }

    /**
     * Viser admin-notifikationer.
     */
    public function display_admin_notices() {
        if ( ! current_user_can( 'manage_options' ) ) {
            return;
        }

        $errors = get_transient( 'administration_plugin_errors' );

        if ( $errors ) {
            echo '<div class="notice notice-error is-dismissible"><p>' . esc_html__( 'Der er opstået fejl i Administration-pluginet. Se logfilerne for detaljer.', 'administration' ) . '</p></div>';
            delete_transient( 'administration_plugin_errors' );
        }
    }
}

// Initialiserer klassen.
new AdminNotices();