<?php 
/**
 * Plugin Name: Group Permissions (Matomo Plugin)
 * Plugin URI: http://plugins.matomo.org/GroupPermissions
 * Description: Manage user permissions with groups.
 * Author: Michael Roosz
 * Author URI: https://github.com/MichaelRoosz/plugin-GroupPermissions
 * Version: 4.0.5
 */
?><?php
/**
 * Matomo - free/libre analytics platform
 *
 * @link http://matomo.org
 * @license http://www.gnu.org/licenses/gpl-3.0.html GPL v3 or later
 *
 */
namespace Piwik\Plugins\GroupPermissions;

use Piwik\Plugin;

 
if (defined( 'ABSPATH')
&& function_exists('add_action')) {
    $path = '/matomo/app/core/Plugin.php';
    if (defined('WP_PLUGIN_DIR') && WP_PLUGIN_DIR && file_exists(WP_PLUGIN_DIR . $path)) {
        require_once WP_PLUGIN_DIR . $path;
    } elseif (defined('WPMU_PLUGIN_DIR') && WPMU_PLUGIN_DIR && file_exists(WPMU_PLUGIN_DIR . $path)) {
        require_once WPMU_PLUGIN_DIR . $path;
    } else {
        return;
    }
    add_action('plugins_loaded', function () {
        if (function_exists('matomo_add_plugin')) {
            matomo_add_plugin(__DIR__, __FILE__, true);
        }
    });
}

class GroupPermissions extends Plugin
{
    public function install()
    {
        $model = new Model();
        $model->install();
    }

    public function uninstall()
    {
        $model = new Model();
        $model->uninstall();
    }
    
    /**
     * @see Piwik\Plugin::registerEvents
     */
    public function registerEvents()
    {
        return array(
            'AssetManager.getJavaScriptFiles'        => 'getJsFiles',
            'AssetManager.getStylesheetFiles'        => 'getStylesheetFiles',
            'SitesManager.deleteSite.end'            => 'deleteSite',
            'UsersManager.deleteUser'                => 'deleteUser',
        );
    }
    
    /**
     * Return list of plug-in specific JavaScript files to be imported by the asset manager
     *
     * @see Piwik\AssetManager
     */
    public function getJsFiles(&$jsFiles)
    {
        $jsFiles[] = "plugins/GroupPermissions/javascripts/groupPermissions.js";
        $jsFiles[] = "plugins/GroupPermissions/javascripts/choices.min.js";
    }
    
    /**
     * Return list of plug-in specific Stylesheet files to be imported by the asset manager
     *
     * @see Piwik\AssetManager
     */
    public function getStylesheetFiles(&$stylesheetFiles)
    {
        $stylesheetFiles[] = "plugins/GroupPermissions/stylesheets/groupPermissions.less";
        $stylesheetFiles[] = "plugins/GroupPermissions/stylesheets/choices.less";
    }

    /**
     * Delete group preferences associated with a particular site
     */
    public function deleteSite($idSite)
    {
        $model = new Model();
        $model->removeAllPermissionsForSite($idSite);
    }

    /**
     * Delete group preferences associated with a particular user
     */
    public function deleteUser($login)
    {
        $model = new Model();
        $model->removeUserFromAllGroups($login);
    }
}
