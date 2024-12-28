<?php
namespace Administration\Core\GeneralHandlers;

defined('ABSPATH') || exit;

/**
 * Class WpRestApiHandler
 *
 * Håndterer REST API-anmodninger og endpoints.
 *
 * @package Administration\Core\GeneralHandlers
 */
class WpRestApiHandler {

    /**
     * Registrerer REST API-endpoints.
     */
    public static function register_endpoints() {
        add_action('rest_api_init', function () {
            register_rest_route('administration/v1', '/save-credentials', [
                'methods' => 'POST',
                'callback' => [self::class, 'handle_save_credentials'],
                'permission_callback' => [self::class, 'permissions_check'],
            ]);
        });
    }

    /**
     * Callback til at håndtere gemning af credentials via REST API.
     *
     * @param WP_REST_Request $request REST request object.
     * @return WP_REST_Response REST response object.
     */
    public static function handle_save_credentials($request) {
        $client_id = sanitize_text_field($request->get_param('client_id'));
        $client_secret = sanitize_text_field($request->get_param('client_secret'));
        $tenant_id = sanitize_text_field($request->get_param('tenant_id'));

        if (empty($client_id) || empty($client_secret) || empty($tenant_id)) {
            return new \WP_REST_Response([
                'success' => false,
                'message' => __('All fields are required.', 'administration'),
            ], 400);
        }

        // Gemmer credentials i wp-config.php
        try {
            WpConfigHandler::save_credentials($client_id, $client_secret, $tenant_id);

            return new \WP_REST_Response([
                'success' => true,
                'message' => __('Credentials saved successfully.', 'administration'),
            ], 200);
        } catch (\Exception $e) {
            return new \WP_REST_Response([
                'success' => false,
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Kontrollerer tilladelser for REST API-anmodninger.
     *
     * @return bool
     */
    public static function permissions_check() {
        return current_user_can('manage_options');
    }
}
