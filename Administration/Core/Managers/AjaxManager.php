<?php
namespace Administration\Core\Managers;

use Administration\Components\Utilities\Crypto;
use Administration\Core\Managers\LoggerManager;

defined('ABSPATH') || exit;

/**
 * Class AjaxManager
 *
 * Håndterer AJAX-anmodninger for forskellige dele af pluginet.
 *
 * Inkluderer:
 * - Håndtering af AJAX for "Experience"-produkttypen.
 * - Håndtering af AJAX for "Rental"-produkttypen.
 * - Håndtering af gemte Microsoft-indstillinger.
 * - Generering af en generisk knap uden logik.
 *
 * @package Administration\Core\Managers
 */
class AjaxManager {

    /**
     * Constructor
     *
     * Registrerer alle AJAX-funktioner.
     */
    public function __construct() {
        // Experience AJAX-funktioner
        add_action('wp_ajax_add_experience_date_row', [$this, 'add_experience_date_row']);
        add_action('wp_ajax_add_experience_tilvalg_row', [$this, 'add_experience_tilvalg_row']);

        // Rental AJAX-funktioner
        add_action('wp_ajax_add_rental_availability_row', [$this, 'add_rental_availability_row']);

        // Microsoft AJAX-funktioner
        add_action('wp_ajax_save_microsoft_settings', [$this, 'save_microsoft_settings']);

        // Generisk knap
        add_action('wp_ajax_generate_generic_button', [$this, 'generate_generic_button']);
    }

    /* ------------------------ EXPERIENCE FUNKTIONER ------------------------ */

    /**
     * AJAX callback for at tilføje en dato række (Experience).
     */
    public function add_experience_date_row() {
        check_ajax_referer('add_experience_date_row_nonce', 'security');

        $index = intval($_POST['index']);
        $date = [];

        ob_start();
        include 'partials/date-row.php';
        $output = ob_get_clean();

        wp_send_json_success($output);
    }

    /**
     * AJAX callback for at tilføje en tilvalg række (Experience).
     */
    public function add_experience_tilvalg_row() {
        check_ajax_referer('add_experience_tilvalg_row_nonce', 'security');

        $index = intval($_POST['index']);
        $item = [];

        ob_start();
        include 'partials/tilvalg-row.php';
        $output = ob_get_clean();

        wp_send_json_success($output);
    }

    /* ------------------------- RENTAL FUNKTIONER -------------------------- */

    /**
     * AJAX callback for at tilføje en tilgængeligheds række (Rental).
     */
    public function add_rental_availability_row() {
        check_ajax_referer('add_rental_availability_row_nonce', 'security');

        $index = intval($_POST['index']);
        $date = [];

        ob_start();
        include 'partials/availability-row.php';
        $output = ob_get_clean();

        wp_send_json_success($output);
    }

    /* ---------------------- MICROSOFT FUNKTIONER ------------------------- */

   
    /* ---------------------- GENERISK KNAP FUNKTION ------------------------ */

    /**
     * Genererer en generisk knap uden logik.
     */
    public function generate_generic_button() {
        check_ajax_referer('generate_generic_button_nonce', 'security');

        $button_id = sanitize_text_field($_POST['button_id'] ?? 'generic-button');
        $button_text = sanitize_text_field($_POST['button_text'] ?? __('Click me', 'administration'));

        ob_start();
        ?>
        <button id="<?php echo esc_attr($button_id); ?>" class="button button-primary">
            <?php echo esc_html($button_text); ?>
        </button>
        <?php
        $output = ob_get_clean();

        wp_send_json_success($output);
    }
}

// Initialiser klassen
new AjaxManager();
