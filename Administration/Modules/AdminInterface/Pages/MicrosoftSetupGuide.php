<?php
namespace Administration\Components\AdminInterface\Templates;

defined('ABSPATH') || exit;

/**
 * Class MicrosoftSetupGuide
 *
 * Dynamisk vejledning til opsætning af Microsoft integrationen.
 *
 * @package Administration\Components\AdminInterface\Templates
 */
class MicrosoftSetupGuide {

    /**
     * Renderer vejledningen baseret på credentials-status.
     *
     * @param bool $credentials_saved Om credentials er gemt. Standard er false.
     * @return void
     */
    public function render_guide(bool $credentials_saved = false): void {
        ?>
        <h2><?php esc_html_e('Vejledning til Microsoft Opsætning', 'administration'); ?></h2>
        <ol>
            <?php if (!$credentials_saved): ?>
                <li><?php esc_html_e('Log ind på Microsoft Azure Portal.', 'administration'); ?></li>
                <li><?php esc_html_e('Opret en ny appregistrering og noter Client ID og Tenant ID.', 'administration'); ?></li>
                <li><?php esc_html_e('Under "Certificates & Secrets" opretter du en ny Client Secret.', 'administration'); ?></li>
                <li><?php esc_html_e('Indtast Client ID, Client Secret og Tenant ID i formularen nedenfor.', 'administration'); ?></li>
                <li><?php esc_html_e('Klik på "Gem indstillingerne".', 'administration'); ?></li>
            <?php else: ?>
                <li><?php esc_html_e('Credentials er gemt korrekt.', 'administration'); ?></li>
                <li><?php esc_html_e('Klik på "Log in with Microsoft" for at autorisere applikationen.', 'administration'); ?></li>
            <?php endif; ?>
        </ol>
        <?php
    }
}
