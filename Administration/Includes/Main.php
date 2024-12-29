<?php

namespace Administration\Includes;

use Administration\Core\Managers\LoggerManager;
use Administration\Core\GeneralHandlers\WpRestApiHandler;
use Administration\Modules\WordPress\WordPressComponents\Menu;

defined('ABSPATH') || exit;

/**
 * Main class for plugin control.
 */
class Main {
    protected $loader;
    private $logger;

    public function __construct() {
        $this->logger = LoggerManager::getInstance();
        $this->loader = new Loader();

        // Hook-registreringer
        add_action('init', [$this, 'defineAdminHooks']);
        add_action('init', [$this, 'definePublicHooks']);
        add_action('rest_api_init', [$this, 'defineRestApiHooks']);
        add_action('init', [$this, 'loadTextdomain']);
    }

    /**
     * Indlæser tekstdomænet for oversættelser.
     */
    public function loadTextdomain() {
        load_plugin_textdomain('administration', false, dirname(plugin_basename(__FILE__)) . '/languages/');
    }

    /**
     * Registrerer hooks til admin-miljøet.
     */
    public function defineAdminHooks() {
        $menu = new Menu();
        $this->loader->add_action('admin_menu', [$menu, 'register_menus'], 10);
    }

    /**
     * Registrerer hooks til public-miljøet.
     */
    public function definePublicHooks() {
        // Placeholder for public hooks
    }

    /**
     * Registrerer REST API hooks.
     */
    public function defineRestApiHooks() {
        $this->loader->add_action('rest_api_init', [WpRestApiHandler::class, 'register_endpoints'], 10);
    }

    /**
     * Initialiserer og kører pluginet.
     */
    public function run() {
        $this->loader->run();
        $this->logger->info('Plugin initialized successfully.');
    }
}
