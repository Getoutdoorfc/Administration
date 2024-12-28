<?php

namespace Administration\Includes;

use Administration\Components\AdminInterface\Menu;
use Administration\Core\Managers\LoggerManager;
use Administration\core\GeneralHandlers\WpRestApiHandler;

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
    }

    private function defineAdminHooks() {
        $menu = new Menu();
        $this->loader->add_action('admin_menu', $menu, 'register_menus');
    }

    private function definePublicHooks() {
        // Placeholder for future public hooks
    }

    /**
     * Define REST API Hooks
     */
    private function defineRestApiHooks() {
        // Registrer REST API endpoints
        $this->loader->add_action('rest_api_init', WpRestApiHandler::class, 'register_endpoints');
    }

    /**
     * Run the plugin initialization.
     */
    public function run() {
        $this->defineAdminHooks();
        $this->definePublicHooks();
        $this->defineRestApiHooks(); // Tilføj REST API hooks
        $this->loader->run();
        $this->logger->info('Plugin initialized successfully.');
    }
}
