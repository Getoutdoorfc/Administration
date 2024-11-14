<?php
namespace Administration\Includes;

use Administration\Includes\Loader;
use Administration\Components\AdminInterface\Menu;

defined( 'ABSPATH' ) || exit;

/**
 * Class Administration
 *
 * Hovedklasse for pluginet, initialiserer nødvendige funktioner og hooks.
 *
 * @package Administration\Includes
 */
class Main {

    /**
     * Loader instans.
     *
     * @var Loader
     */
    protected $loader;

    /**
     * Constructor.
     */
    public function __construct() {
        error_log('Main::__construct called');
        $this->loadDependencies();
        $this->defineAdminHooks();
        $this->definePublicHooks();
    }

    /**
     * Indlæser afhængighederne for pluginet.
     */
    private function loadDependencies() {
        $this->loader = new Loader();
    }

    /**
     * Definerer hooks og filtre til admin-området.
     */
    private function defineAdminHooks() {
        error_log('Main::defineAdminHooks called');
        // Registrer handlinger og filtre for admin-området
        $menu = new Menu();
    }

    /**
     * Definerer hooks og filtre til frontend.
     */
    private function definePublicHooks() {
        error_log('Main::definePublicHooks called');
        // Registrer handlinger og filtre for frontend
    }

    /**
     * Kører loaderen for at eksekvere alle hooks med WordPress.
     */
    public function run() {
        error_log('Main::run called');
        $this->loader->run();
    }
}
