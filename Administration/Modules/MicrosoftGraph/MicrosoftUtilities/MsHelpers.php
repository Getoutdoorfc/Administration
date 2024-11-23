<?php
namespace Administration\Components\Integrations\MicrosoftGraph;

use Administration\Components\Utilities\Logger;

defined( 'ABSPATH' ) || exit;

/**
 * Class Helpers
 *
 * Hjælpefunktioner til Microsoft Graph-integrationer.
 *
 * @package Administration\Components\Integrations\MicrosoftGraph
 */
class MsHelpers {

    /**
     * API instance.
     *
     * @var API
     */
    private $api;

    /**
     * Constructor.
     */
    public function __construct() {
        $this->api = new API( 'https://graph.microsoft.com/v1.0', new TokenHandler() );
    }

    /**
     * Henter kalenderkategorier fra Microsoft Graph API.
     *
     * @return array|false
     */
    public function getCalendarCategories() {
        $cacheKey   = 'administration_calendar_categories';
        $categories = get_transient( $cacheKey );

        if ( $categories ) {
            return $categories;
        }

        $response = $this->api->get( '/me/outlook/masterCategories' );

        if ( ! $response || ! isset( $response['value'] ) ) {
            Logger::get_instance()->error( 'Failed to retrieve calendar categories.' );
            return false;
        }

        $categories = $response['value'];

        // Cache for 24 hours
        set_transient( $cacheKey, $categories, DAY_IN_SECONDS );

        return $categories;
    }
}
