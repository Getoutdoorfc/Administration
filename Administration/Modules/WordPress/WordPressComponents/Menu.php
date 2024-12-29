<?php
namespace Administration\Modules\WordPress\WordPressComponents;

use Administration\Modules\MicroSoft\MicrosoftSetup;
use Administration\Modules\Woocommerce\WoocommerceComponents\Stock\StockOverview;

defined('ABSPATH') || exit;

/**
 * Class Menu
 *
 * Tilføjer hovedmenu og undermenuer i WordPress admin-dashboard under "Get Indoor".
 */
class Menu {
    private $stock_overview;

    /**
     * Constructor.
     * Initialiserer Menu-klassen og tilføjer WordPress 'admin_menu' hook.
     */
    public function __construct() {
        error_log('Initializing Menu class.');

        // Initialiserer StockOverview, hvis klassen findes
        if (class_exists(StockOverview::class)) {
            error_log('StockOverview class exists. Initializing.');
            $this->stock_overview = new StockOverview();
        } else {
            error_log('StockOverview class does not exist.');
        }

        add_action('admin_menu', [$this, 'register_menus']);
    }

    /**
     * Register menuer og undermenuer i WordPress admin.
     */
    public function register_menus() {
        error_log('Registering menus.');

        // Hovedmenu: Get Indoor
        add_menu_page(
            __('Get Indoor', 'administration'),
            __('Get Indoor', 'administration'),
            'manage_options',
            'get-indoor',
            [$this, 'display_dashboard_page'],
            'dashicons-admin-site-alt3',
            6
        );

        error_log('Added main menu: Get Indoor.');

        // Undermenu: Microsoft Opsætning
        if (class_exists(MicrosoftSetup::class)) {
            error_log('MicrosoftSetup class exists. Adding submenu.');
            add_submenu_page(
                'get-indoor',
                __('Microsoft Opsætning', 'administration'),
                __('Microsoft Opsætning', 'administration'),
                'manage_options',
                'administration-microsoft-setup',
                [new MicrosoftSetup(), 'render_setup_page']
            );
        } else {
            error_log('MicrosoftSetup class does not exist.');
        }

        // Undermenu: Årshjul
        add_submenu_page(
            'get-indoor',
            __('Årshjul', 'administration'),
            __('Årshjul', 'administration'),
            'manage_options',
            'aarshjul',
            [$this, 'display_aarshjul_page']
        );

        // Undermenu: Lageroversigt, hvis $stock_overview er initialiseret
        if ($this->stock_overview) {
            error_log('StockOverview instance exists. Adding Lageroversigt submenu.');
            add_submenu_page(
                'get-indoor',
                __('Lageroversigt', 'administration'),
                __('Lageroversigt', 'administration'),
                'manage_options',
                'lageroversigt',
                [$this->stock_overview, 'display_stock_overview_page']
            );
        } else {
            error_log('StockOverview instance does not exist. Skipping Lageroversigt submenu.');
        }
    }

    /**
     * Viser Dashboard siden.
     */
    public function display_dashboard_page() {
        error_log('Displaying Dashboard page.');
        echo '<h1>' . __('Dashboard', 'administration') . '</h1>';
    }

    /**
     * Viser Årshjul siden.
     */
    public function display_aarshjul_page() {
        error_log('Displaying Årshjul page.');
        echo '<h1>' . __('Årshjul', 'administration') . '</h1>';
    }
}
