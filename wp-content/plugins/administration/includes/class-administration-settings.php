<?php
class Administration_Settings {
    private $page_hook;

    public function __construct() {
        add_action('admin_menu', array($this, 'add_admin_menu'));
        add_action('admin_init', array($this, 'register_settings'));
        add_action('admin_init', array($this, 'handle_authorize_request'));
    }

    // Tilføjer hovedmenu og undermenu
    public function add_admin_menu() {
        $parent_slug = 'administration_main';
        
        // Hovedmenuen
        add_menu_page(
            __('Administration', 'administration'),
            __('Administration', 'administration'),
            'manage_options',
            $parent_slug,
            array($this, 'display_admin_dashboard'),
            'dashicons-admin-generic',
            6
        );

        // Undermenuen til Microsoft API Settings
        $this->page_hook = add_submenu_page(
            $parent_slug,
            __('Microsoft API Settings', 'administration'),
            __('Microsoft API Settings', 'administration'),
            'manage_options',
            'administration_settings',
            array($this, 'settings_page_content')
        );
    }

    // Registrerer Microsoft API-indstillinger
    public function register_settings() {
        register_setting('administration_microsoft_settings', 'administration_client_id', array(
            'sanitize_callback' => 'sanitize_text_field'
        ));
        register_setting('administration_microsoft_settings', 'administration_client_secret', array(
            'sanitize_callback' => 'sanitize_text_field'
        ));
        register_setting('administration_microsoft_settings', 'administration_tenant_id', array(
            'sanitize_callback' => 'sanitize_text_field'
        ));
    }

    // Indholdet på indstillingssiden
    public function settings_page_content() {
        ?>
        <div class="wrap">
            <h1><?php _e('Microsoft API Settings', 'administration'); ?></h1>
            <form method="post" action="options.php">
                <?php
                settings_fields('administration_microsoft_settings');
                do_settings_sections('administration_microsoft_settings');
                ?>
                <table class="form-table">
                    <tr valign="top">
                        <th scope="row"><?php _e('Microsoft Client ID', 'administration'); ?></th>
                        <td><input type="text" name="administration_client_id" value="<?php echo esc_attr(get_option('administration_client_id')); ?>" style="width: 100%;"/></td>
                    </tr>
                    <tr valign="top">
                        <th scope="row"><?php _e('Microsoft Client Secret', 'administration'); ?></th>
                        <td><input type="text" name="administration_client_secret" value="<?php echo esc_attr(get_option('administration_client_secret')); ?>" style="width: 100%;"/></td>
                    </tr>
                    <tr valign="top">
                        <th scope="row"><?php _e('Microsoft Tenant ID', 'administration'); ?></th>
                        <td><input type="text" name="administration_tenant_id" value="<?php echo esc_attr(get_option('administration_tenant_id')); ?>" style="width: 100%;"/></td>
                    </tr>
                </table>
                <?php submit_button(); ?>
            </form>
            <form method="post" action="<?php echo admin_url('admin.php?page=administration_settings&action=authorize'); ?>">
                <?php wp_nonce_field('authorize_nonce', 'authorize_nonce'); ?>
                <button type="submit" class="button button-primary"><?php _e('Authorize with Microsoft', 'administration'); ?></button>
            </form>
        </div>
        <?php
    }

    // Håndterer autorisationsanmodningen og udveksler koden for tokens
    public function handle_authorize_request() {
        if (isset($_GET['action']) && $_GET['action'] === 'authorize') {
            check_admin_referer('authorize_nonce', 'authorize_nonce');

            $client_id = get_option('administration_client_id');
            $tenant_id = get_option('administration_tenant_id');
            $redirect_uri = admin_url('admin.php?page=administration_settings');
            $scope = 'https://graph.microsoft.com/.default';

            $authorization_url = 'https://login.microsoftonline.com/' . $tenant_id . '/oauth2/v2.0/authorize?' . http_build_query([
                'client_id' => $client_id,
                'response_type' => 'code',
                'redirect_uri' => $redirect_uri,
                'response_mode' => 'query',
                'scope' => $scope
            ]);

            error_log("Authorization URL: " . $authorization_url);
            wp_redirect($authorization_url);
            exit;
        }

        // Henter adgangstoken ved hjælp af autorisationskode
        if (isset($_GET['code'])) {
            $auth = new Administration_MSGraph_Auth();
            $auth->handle_callback();
        }
    }

    public function display_admin_dashboard() {
        echo '<h1>Administration Dashboard</h1>';
        echo '<p>Velkommen til administrationsdashboardet.</p>';
    }

    public function bypass_cache_for_token_pages() {
        if (is_admin() && isset($_GET['page']) && $_GET['page'] === 'administration_settings') {
            nocache_headers(); // Sørg for, at ingen cache bruges på indstillingssiden
        }
    }
}

add_action('admin_init', array('Administration_Settings', 'bypass_cache_for_token_pages'));
