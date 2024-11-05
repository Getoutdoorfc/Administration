<?php
/* class-administration-loader */

if (!defined('ABSPATH')) {
    exit; // Stop direkte adgang
}

class Administration_Loader {
    protected $actions;
    protected $filters;

    public function __construct() {
        $this->actions = array();
        $this->filters = array();
    }

    public function add_action($hook, $component, $callback, $priority = 10, $accepted_args = 1) {
        $this->actions = $this->add($this->actions, $hook, $component, $callback, $priority, $accepted_args);
    }

    public function add_filter($hook, $component, $callback, $priority = 10, $accepted_args = 1) {
        $this->filters = $this->add($this->filters, $hook, $component, $callback, $priority, $accepted_args);
    }

    private function add($hooks, $hook, $component, $callback, $priority, $accepted_args) {
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
        foreach ($this->actions as $hook) {
            add_action($hook['hook'], array($hook['component'], $hook['callback']), $hook['priority'], $hook['accepted_args']);
            error_log("Action added: {$hook['hook']} -> " . get_class($hook['component']) . "::{$hook['callback']}");
        }

        foreach ($this->filters as $hook) {
            add_filter($hook['hook'], array($hook['component'], $hook['callback']), $hook['priority'], $hook['accepted_args']);
            error_log("Filter added: {$hook['hook']} -> " . get_class($hook['component']) . "::{$hook['callback']}");
        }
    }

    public function remove_action($hook, $component, $callback, $priority = 10) {
        remove_action($hook, array($component, $callback), $priority);
        error_log("Action removed: {$hook} -> " . get_class($component) . "::{$callback}");
    }

    public function remove_filter($hook, $component, $callback, $priority = 10) {
        remove_filter($hook, array($component, $callback), $priority);
        error_log("Filter removed: {$hook} -> " . get_class($component) . "::{$callback}");
    }
}
