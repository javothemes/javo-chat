<?php
class Javo_Chat_Base
{
    public function get_template_path($skin)
    {
        // Return the path based on skin
        switch ($skin) {
            case 'professional':
                return plugin_dir_path(dirname(__FILE__)) . 'includes/email-templates/professional_template.php';
            case 'modern':
                return plugin_dir_path(dirname(__FILE__)) . 'includes/email-templates/modern_template.php';
            case 'simple':
                return plugin_dir_path(dirname(__FILE__)) . 'includes/email-templates/simple_template.php';
            default:
                return '';
        }
    }
}
