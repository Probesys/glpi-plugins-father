<?php

// Version of the plugin
define('PLUGIN_FATHER_VERSION', '1.4.1');
// Minimal GLPI version, inclusive
define('PLUGIN_FATHER_GLPI_MIN_VERSION', '10');
// Maximum GLPI version, exclusive
define('PLUGIN_FATHER_GLPI_MAX_VERSION', '11');

if (!defined("PLUGIN_FATHER_DIR")) {
   define('PLUGIN_FATHER_DIR', Plugin::getPhpDir("father"));
}
if (!defined("PLUGIN_FATHER_WEB_DIR")) {
   define("PLUGIN_FATHER_WEB_DIR", Plugin::getWebDir("father"));
}

/**
 * Check plugin's config before activation
 */
function plugin_father_check_config($verbose = false)
{
    return true;
}

function plugin_init_father()
{
    global $PLUGIN_HOOKS;
    $PLUGIN_HOOKS['csrf_compliant']['father'] = true;

    if (class_exists('PluginFatherFather')) {
        $config = new PluginFatherConfig();
        $PLUGIN_HOOKS['csrf_compliant']['father'] = true;
        $PLUGIN_HOOKS['config_page']['father'] = 'front/config.form.php';

        Plugin::registerClass(
            'PluginFatherFatherItem',
            ['addtabon' => ['PluginFatherFather']]
        );

        if ( isset($_SERVER) && (
                ((strpos($_SERVER['REQUEST_URI'], "/ticket.form.php") !== false) && $config->isOk(0)) ||
                ((strpos($_SERVER['REQUEST_URI'], "/problem.form.php") !== false) && $config->isOk(1)) ||
                ((strpos($_SERVER['REQUEST_URI'], "/change.form.php") !== false) && $config->isOK(2))
             )        
        ) {
            $PLUGIN_HOOKS['add_javascript']['father'][] = 'js/show_father.js';
        }
        if ($config->isOk(0)) {
            $PLUGIN_HOOKS['pre_item_update']['father']['Ticket'] = ['PluginFatherFather', 'beforeUpdate'];
        }
        if ($config->isOk(1)) {
            $PLUGIN_HOOKS['pre_item_update']['father']['Problem'] = ['PluginFatherFather', 'beforeUpdate'];
        }

        if ($config->isOk(2)) {
            $PLUGIN_HOOKS['pre_item_update']['father']['Change'] = ['PluginFatherFather', 'beforeUpdate'];
        }
    }
}

function plugin_version_father()
{
    return [
        'name' => __('Father&Sons', 'father'),
        'version' => PLUGIN_FATHER_VERSION,
        'author' => '<a href="https://www.probesys.com">PROBESYS</a>',
        'homepage' => 'https://github.com/Probesys/glpi-plugins-father',
        'license' => '<a href="'. Plugin::getPhpDir('father', false).'/LICENSE" target="_blank">AGPLv3</a>',
        'minGlpiVersion' => PLUGIN_FATHER_GLPI_MIN_VERSION
    ];
}

/**
 * Check plugin's prerequisites before installation
 */
function plugin_father_check_prerequisites()
{
    if (version_compare(GLPI_VERSION, PLUGIN_FATHER_GLPI_MIN_VERSION, 'lt') || version_compare(GLPI_VERSION, PLUGIN_FATHER_GLPI_MAX_VERSION, 'ge')) {
        echo __('This plugin requires GLPI >= ' . PLUGIN_FATHER_GLPI_MIN_VERSION . ' and GLPI < ' . PLUGIN_FATHER_GLPI_MAX_VERSION . '<br>');
    } else {
        return true;
    }
    return false;
}
