<?php
namespace Administration\Components\AdminInterface;

defined( 'ABSPATH' ) || exit;

/**
 * Class Settings
 *
 * Håndterer indstillingssiden for pluginet.
 *
 * @package Administration\Components\AdminInterface
 */
class Settings {

    /**
     * Constructor.
     */
    public function __construct() {
        add_action( 'admin_init', array( $this, 'register_settings' ) );
    }

    /**
     * Registrerer indstillinger for pluginet.
     */
    public function register_settings() {
        // Registrer en ny indstilling under en bestemt gruppe
        register_setting( 'administration_settings_group', 'administration_option_name' );

        // Tilføj en sektion til indstillingerne
        add_settings_section(
            'administration_settings_section',
            __( 'Generelle Indstillinger', 'administration' ),
            array( $this, 'settings_section_callback' ),
            'administration-settings'
        );

        // Tilføj et indstillingsfelt
        add_settings_field(
            'administration_option_name',
            __( 'Indstillingsnavn', 'administration' ),
            array( $this, 'option_name_callback' ),
            'administration-settings',
            'administration_settings_section'
        );
    }

    /**
     * Callback for indstillingssektionen.
     */
    public function settings_section_callback() {
        echo '<p>' . esc_html__( 'Generelle indstillinger for Administration-pluginet.', 'administration' ) . '</p>';
    }

    /**
     * Callback for indstillingsfeltet.
     */
    public function option_name_callback() {
        $option = get_option( 'administration_option_name' );
        ?>
        <input type="text" name="administration_option_name" value="<?php echo esc_attr( $option ); ?>" />
        <?php
    }
}

// Initialiserer klassen.
new Settings();
