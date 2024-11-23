<?php
namespace Administration\Components\AdminInterface;

defined( 'ABSPATH' ) || exit;

/**
 * Class Roles
 *
 * Håndterer tilføjelse og fjernelse af brugerroller for pluginet.
 *
 * @package Administration\Components\AdminInterface
 */
class Roles {

    /**
     * Tilføjer brugerroller ved plugin-aktivering.
     */
    public static function add_roles() {
        add_role( 'medarbejder', __( 'Medarbejder', 'administration' ), array(
            'read'                    => true,
            'edit_products'           => true,
            'edit_published_products' => true,
            'publish_products'        => true,
            'read_product'            => true,
            'edit_product'            => true,
            'delete_product'          => false,
            'manage_woocommerce'      => false,
            'view_admin_dashboard'    => false,
            'upload_files'            => true,
        ) );
    }

    /**
     * Fjerner brugerroller ved plugin-deaktivering.
     */
    public static function remove_roles() {
        remove_role( 'medarbejder' );
    }
}
