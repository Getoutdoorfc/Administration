<?php

namespace Administration\Includes;

defined('ABSPATH') || exit;

/**
 * Class Loader
 * Central hook manager for actions and filters.
 */
class Loader {
    protected $hooks = [
        'actions' => [],
        'filters' => []
    ];

    public function add($type, $hook, $component, $callback, $priority = 10, $accepted_args = 1) {
        $this->hooks[$type][] = compact('hook', 'component', 'callback', 'priority', 'accepted_args');
    }

    public function add_action($hook, $component, $callback, $priority = 10, $accepted_args = 1) {
        $this->add('actions', $hook, $component, $callback, $priority, $accepted_args);
    }

    public function add_filter($hook, $component, $callback, $priority = 10, $accepted_args = 1) {
        $this->add('filters', $hook, $component, $callback, $priority, $accepted_args);
    }

    public function run() {
        foreach ($this->hooks as $type => $hooks) {
            foreach ($hooks as $hook) {
                $function = $type === 'actions' ? 'add_action' : 'add_filter';
                $function($hook['hook'], [$hook['component'], $hook['callback']], $hook['priority'], $hook['accepted_args']);
            }
        }
    }
}
