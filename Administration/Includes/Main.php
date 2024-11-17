<?php

namespace Administration\Includes;

use Administration\Components\AdminInterface\Menu;
use Administration\Components\Utilities\Logger;

defined('ABSPATH') || exit;

/**
 * Main class for plugin control.
 */
class Main {
    protected $loader;
    private $logger;

    public function __construct() {
        $this->logger = Logger::getInstance();
        $this->loader = new Loader();
    }

    private function defineAdminHooks() {
        $menu = new Menu();
        $this->loader->add_action('admin_menu', $menu, 'register_menus');
    }

    private function definePublicHooks() {
        // Placeholder for future public hooks
    }

    public function run() {
        $this->defineAdminHooks();
        $this->definePublicHooks();
        $this->loader->run();
        $this->logger->info('Plugin initialized successfully.');
    }
}
