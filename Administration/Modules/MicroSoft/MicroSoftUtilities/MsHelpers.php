<?php
namespace Administration\Modules\MicroSoft\MicroSoftUtilities\MsHelpers;

use Administration\Core\Managers\LoggerManager;
use Administration\Modules\MicroSoft\MicroSoftCore\MsTokenHandler;
use Administration\Modules\MicroSoft\MicroSoftCore\MsRestApiHandler;

defined('ABSPATH') || exit;

/**
 * Class MsHelpers
 *
 * Hjælpefunktioner til Microsoft Graph-integrationer.
 *
 * @package Administration\Modules\MicroSoft\MicroSoftUtilities\MsHelpers
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
        $this->api = new MsRestApiHandler( 'https://graph.microsoft.com/v1.0', new MsTokenHandler() );
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
            LoggerManager::getInstance()->error( 'Failed to retrieve calendar categories.' );
            return false;
        }

        $categories = $response['value'];

        // Cache for 24 hours
        set_transient( $cacheKey, $categories, DAY_IN_SECONDS );

        return $categories;
    }
}
