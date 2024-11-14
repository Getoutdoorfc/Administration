<?php
namespace Administration\Components\AdminInterface\Templates;

defined('ABSPATH') || exit;

/**
 * Class MicrosoftSetupGuide
 *
 * Indeholder vejledninger og tips til Microsoft opsætning.
 *
 * @package Administration\Components\AdminInterface\Templates
 */
class MicrosoftSetupGuide {

    /**
     * Render vejledninger og tips til opsætning.
     */
    public static function render_guide() {
        ?>
        <div class="setup-guide">
            <h2><?php esc_html_e('Opsætningsvejledning til Microsoft Integration', 'administration'); ?></h2>
            <p><?php esc_html_e('Følg disse trin for at opsætte din Microsoft integration korrekt:', 'administration'); ?></p>
            <ol>
                <li><?php esc_html_e('Gå til Azure-portalen på portal.azure.com og log ind med din konto.', 'administration'); ?></li>
                <li><?php esc_html_e('Naviger til "Azure Active Directory" > "App-registreringer" og vælg "Ny registrering".', 'administration'); ?></li>
                <li><?php esc_html_e('Indtast et navn til appen, og vælg "Konti i denne organisations bibliotek" som kontotype.', 'administration'); ?></li>
                <li><?php esc_html_e('Indtast din WordPress URL som Omdirigerings-URI (fx: https://ditdomæne.dk/callback).', 'administration'); ?></li>
                <li><?php esc_html_e('Efter registreringen, find "Applikations-ID (Client ID)" og "Directory ID (Tenant ID)" under "Oversigt".', 'administration'); ?></li>
                <li><?php esc_html_e('Gå til "Certifikater og hemmeligheder", opret en ny klienthemmelighed, og kopier værdien (Client Secret).', 'administration'); ?></li>
                <li><?php esc_html_e('Gå til "API-tilladelser" og tilføj "Microsoft Graph" med nødvendige tilladelser (fx: Calendars.ReadWrite).', 'administration'); ?></li>
            </ol>
            <p><?php esc_html_e('Når ovenstående er udført, skal du indtaste dine credentials nedenfor og klikke på "Gem indstillinger".', 'administration'); ?></p>
        </div>
        <?php
    }
}
