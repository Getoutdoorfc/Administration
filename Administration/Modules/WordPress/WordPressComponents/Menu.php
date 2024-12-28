<?php
namespace Administration\Modules\WordPress\WordPressComponents;

defined('ABSPATH') || exit;

use Administration\Modules\Microsoft\MicroSoftSetup;
use Administration\Modules\Woocommerce\WoocommerceComponents\Stock\StockOverview;

/**
 * Class Menu
 *
 * Tilføjer hovedmenu og undermenuer i WordPress admin-dashboard under "Get Indoor".
 * 
 * @package Administration\Modules\AdminInterface\AdminComponents
 */
class Menu {

    private $stock_overview;

    /**
     * Constructor.
     * Initialiserer Menu-klassen og tilføjer WordPress 'admin_menu' hook.
     */
    public function __construct() {
        // Initialiserer StockOverview hvis klassen findes.
        if (class_exists('Administration\Modules\AdminInterface\AdminComponents\StockOverview')) {
            $this->stock_overview = new StockOverview();
        }

        // Tilføj 'register_menus' funktionen til 'admin_menu' hook.
        add_action('admin_menu', array($this, 'register_menus'));
    }

    /**
     * Register menuer og undermenuer i WordPress admin.
     */
    public function register_menus() {
        // Hovedmenu: Get Indoor
        add_menu_page(
            __('Get Indoor', 'administration'),
            __('Get Indoor', 'administration'),
            'manage_options',
            'get-indoor',
            array($this, 'display_dashboard_page'),
            'dashicons-admin-site-alt3',
            6
        );

        // Undermenu: Microsoft Opsætning
        if (class_exists('Administration\Components\AdminInterface\MicrosoftSetup')) {
            add_submenu_page(
                'get-indoor',
                __('Microsoft Opsætning', 'administration'),
                __('Microsoft Opsætning', 'administration'),
                'manage_options',
                'administration-microsoft-setup',
                array(new MicrosoftSetup(), 'render_setup_page')
            );
        }

        // Undermenu: Årshjul
        add_submenu_page(
            'get-indoor',
            __('Årshjul', 'administration'),
            __('Årshjul', 'administration'),
            'manage_options',
            'aarshjul',
            array($this, 'display_aarshjul_page')
        );

        // Undermenu: Lageroversigt, hvis $stock_overview er initialiseret
        if ($this->stock_overview) {
            add_submenu_page(
                'get-indoor',
                __('Lageroversigt', 'administration'),
                __('Lageroversigt', 'administration'),
                'manage_options',
                'lageroversigt',
                array($this->stock_overview, 'display_stock_overview_page')
            );
        }
    }

    /**
     * Viser Dashboard siden.
     */
    public function display_dashboard_page() {
        echo '<h1>' . __('Dashboard', 'administration') . '</h1>';
    }

    /**
     * Viser Årshjul siden.
     */
    public function display_aarshjul_page() {
        echo '<h1>' . __('Årshjul', 'administration') . '</h1>';
    }
}
