<?php
namespace Administration\Core\GeneralHandlers;

defined('ABSPATH') || exit;

/**
 * Class HtaccessHandler
 *
 * Håndterer opdateringer til .htaccess for at beskytte følsomme filer som wp-config.php.
 *
 * @package Administration\Core\GeneralHandlers
 */
class HtaccessHandler {

    /**
     * Tilføjer sikkerhedsregler til .htaccess for at beskytte følsomme filer.
     */
    public static function updateHtaccess() {
        $htaccess_file = ABSPATH . '.htaccess';

        if (is_writable($htaccess_file)) {
            $rules = "\n# BEGIN Administration Plugin\n";
            $rules .= "<FilesMatch \"(wp-config\\.php|class-token-handler\\.php)$\">\n";
            $rules .= "Require all denied\n";
            $rules .= "</FilesMatch>\n";
            $rules .= "# END Administration Plugin\n";

            $current_rules = file_get_contents($htaccess_file);
            if (strpos($current_rules, '# BEGIN Administration Plugin') === false) {
                file_put_contents($htaccess_file, $rules, FILE_APPEND);
            }
        } else {
            wp_die(__('Cannot write to .htaccess. Make sure the file is writable.', 'administration'));
        }
    }

    /**
     * Fjerner pluginets regler fra .htaccess.
     */
    public static function removeHtaccessRules() {
        $htaccess_file = ABSPATH . '.htaccess';

        if (is_writable($htaccess_file)) {
            $current_rules = file_get_contents($htaccess_file);
            $new_rules = preg_replace('/\n# BEGIN Administration Plugin.*?# END Administration Plugin\n/s', '', $current_rules);
            file_put_contents($htaccess_file, $new_rules);
        }
    }
}

// Tilføj handlinger for aktivering og deaktivering
register_activation_hook(__FILE__, array('Administration\Components\Utilities\HtaccessHandler', 'updateHtaccess'));
register_deactivation_hook(__FILE__, array('Administration\Components\Utilities\HtaccessHandler', 'removeHtaccessRules'));
