<?php
namespace Administration\Includes;
if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly.
}

class Loader {
    protected $actions;
    protected $filters;

    public function __construct() {
        $this->actions = array();
        $this->filters = array();
    }

    public function add_action( $hook, $component, $callback, $priority = 10, $accepted_args = 1 ) {
        $this->actions = $this->add( $this->actions, $hook, $component, $callback, $priority, $accepted_args );
    }

    public function add_filter( $hook, $component, $callback, $priority = 10, $accepted_args = 1 ) {
        $this->filters = $this->add( $this->filters, $hook, $component, $callback, $priority, $accepted_args );
    }

    private function add( $hooks, $hook, $component, $callback, $priority, $accepted_args ) {
        $hooks[] = array(
            'hook'          => $hook,
            'component'     => $component,
            'callback'      => $callback,
            'priority'      => $priority,
            'accepted_args' => $accepted_args
        );

        return $hooks;
    }

    public function run() {
        error_log('Loader::run called');
        foreach ( $this->actions as $hook ) {
            error_log('Adding action: ' . $hook['hook']);
            add_action( $hook['hook'], array( $hook['component'], $hook['callback'] ), $hook['priority'], $hook['accepted_args'] );
        }
        foreach ( $this->filters as $hook ) {
            error_log('Adding filter: ' . $hook['hook']);
            add_filter( $hook['hook'], array( $hook['component'], $hook['callback'] ), $hook['priority'], $hook['accepted_args'] );
        }
    }

    public function remove_action( $hook, $component, $callback, $priority = 10 ) {
        remove_action( $hook, array( $component, $callback ), $priority );
    }

    public function remove_filter( $hook, $component, $callback, $priority = 10 ) {
        remove_filter( $hook, array( $component, $callback ), $priority );
    }
}
