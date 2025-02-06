<?php

class Admin_Bar_Customizer {
    /**
     * Initialize the admin bar customizer
     */
    public function init() {
        add_action('admin_head', array($this, 'customize_admin_bar_color'));
        add_action('wp_head', array($this, 'customize_admin_bar_color'));
    }

    /**
     * Customize admin bar color based on environment
     */
    public function customize_admin_bar_color() {
        if (!is_admin_bar_showing()) {
            return;
        }

        $current_site_url = get_site_url();
        $style = '';

        if (strpos($current_site_url, 'geregeld.online') !== false) {
            $style = $this->get_production_styles();
        } elseif (strpos($current_site_url, 'multi2.local') !== false) {
            $style = $this->get_local_styles();
        }

        if ($style) {
            echo '<style type="text/css">' . $style . '</style>';
        }
    }

    /**
     * Get styles for production environment
     * 
     * @return string CSS styles for production
     */
    private function get_production_styles() {
        return '
            #wpadminbar {
                background: #d63638 !important;
            }
            #wpadminbar .ab-item, 
            #wpadminbar a.ab-item, 
            #wpadminbar > #wp-toolbar span.ab-label {
                color: #fff !important;
            }
            #wpadminbar .ab-icon, 
            #wpadminbar .ab-icon:before,
            #wpadminbar .ab-item:before,
            #wpadminbar .ab-item:after {
                color: #fff !important;
            }
        ';
    }

    /**
     * Get styles for local environment
     * 
     * @return string CSS styles for local
     */
    private function get_local_styles() {
        return '
            #wpadminbar {
                background: #666666 !important;
            }
            #wpadminbar .ab-item, 
            #wpadminbar a.ab-item, 
            #wpadminbar > #wp-toolbar span.ab-label {
                color: #fff !important;
            }
            #wpadminbar .ab-icon, 
            #wpadminbar .ab-icon:before,
            #wpadminbar .ab-item:before,
            #wpadminbar .ab-item:after {
                color: #fff !important;
            }
        ';
    }
} 