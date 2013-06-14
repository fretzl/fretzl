<?php
/**
 * Plugin to hide the display of user rights and other info if a user does NOT have ADMIN_RIGHTS.
 * 
 * @author Fred Sondaar (fretzl)
 * @package plugins
 * @subpackage admin
 */
 
$plugin_is_filter = 5|ADMIN_PLUGIN;
$plugin_description = gettext("Hide the display of user rights and other info if a user does NOT have ADMIN_RIGHTS.");
$plugin_author = "Fred Sondaar (fretzl)";

$option_interface = 'hideUserRights';

zp_register_filter('admin_head', 'hideUserRights::customDisplayRights');

if (!defined('QUOTA')) define('QUOTA',getOption('quota'));
if (!defined('GROUPS')) define('GROUPS',getOption('groups'));

class hideUserRights {

	function __construct() {
		setOptionDefault('all_rights', 0);
		setOptionDefault('albums', 0);
		setOptionDefault('pages', 0);
		setOptionDefault('categories', 0);
		//setOptionDefault('albums_pages_news', 0);
		setOptionDefault('notebox', 0);
		setOptionDefault('languages', 0);
		setOptionDefault('quota', 0);
		setOptionDefault('groups', 0);
	}
	
	function getOptionsSupported() {
		return array(	gettext('All rights') => array('key' => 'all_rights', 'type' => OPTION_TYPE_CHECKBOX,
										'desc' => gettext('Rights. (the part with all the checkboxes)')),
						gettext('Albums') => array('key' => 'albums', 'type' => OPTION_TYPE_CHECKBOX,
										'desc' => gettext('Managed albums')),
						gettext('Pages') => array('key' => 'pages', 'type' => OPTION_TYPE_CHECKBOX,
										'desc' => gettext('Managed pages')),
						gettext('Categories') => array('key' => 'categories', 'type' => OPTION_TYPE_CHECKBOX,
										'desc' => gettext('Managed news categories')),
						/*
						gettext('Albums, Pages and Categories') => array('key' => 'albums_pages_cats', 'type' => OPTION_TYPE_CHECKBOX,
										'desc' => gettext('Albums, Pages and Categories')),
						*/
						gettext('All Noteboxes') => array('key' => 'notebox', 'type' => OPTION_TYPE_CHECKBOX,
										'desc' => gettext('All Noteboxes')),
						gettext('Languages (Flags)') => array('key' => 'languages', 'type' => OPTION_TYPE_CHECKBOX,
										'desc' => gettext('Languages (Flags)')),
						gettext('Quota') => array('key' => 'quota', 'type' => OPTION_TYPE_CHECKBOX,
										'desc' => gettext('Assigned quota (if the <em>quota_manager</em> plugin is enabled)')),
						gettext('Groups') => array('key' => 'groups', 'type' => OPTION_TYPE_CHECKBOX,
										'desc' => gettext('User group membership information (if the <em>user_groups</em> plugin is enabled).'))
		);
	}

	static function customDisplayRights() {
		global $_zp_admin_tab;
		$active_plugins = getEnabledPlugins();
		if (!zp_loggedin(ADMIN_RIGHTS) && $_zp_admin_tab == 'users') {
			$user_config_add = '';
			$user_config = '
			<script type="text/javascript">
				// <!-- <![CDATA[
					$(document).ready(function(){';
						
					if (getOption("all_rights"))	// Rights. (the part with all the checkboxes).
						$user_config_add .= '$(".box-rights").remove();';
					
					if (getOption("albums"))		// Managed albums
						$user_config_add .= '$(".box-albums-unpadded:eq(0)").remove();';
						
					if (getOption("pages"))			// Managed pages
						$user_config_add .= '$(".box-albums-unpadded:eq(1)").remove();';
						
					if (getOption("categories"))	// Managed news categories
						$user_config_add .= '$(".box-albums-unpadded:eq(2)").remove();'; 
					
					/*	
					if (getOption("albums_pages_cats"))	// Albums, Pages, and Categories.
						$user_config_add .= '$(".box-albums-unpadded").remove();'; 
					*/
							
					if (getOption("notebox"))		// All Noteboxes					
						$user_config_add .= '$(".notebox").remove();';
						
					if (getOption("languages"))		// Languages (Flags)									
						$user_config_add .= '$("label[for=\'admin_language_0\'], ul.flags").remove();'; 	
					
					if (array_key_exists("quota_manager", $active_plugins))  // Assigned quota (if the "quota_manager" plugin is enabled).
						$user_config_add .= '$("td:contains("'.gettext("Quota").'")").parent("tr.userextrainfo").remove();';
					
					if (array_key_exists("user_groups", $active_plugins))  // "User group membership" information (if the "user_groups" plugin is enabled).
						$user_config_add .= '$("tr.userextrainfo td:contains("'.gettext("User group membership").'")").next().andSelf().remove();';
						
					
				$user_config_add .= '
				});
				// ]]> -->
			</script>';
			
			$user_config = $user_config.$user_config_add;
			
			echo $user_config;				
		}
	}
}

?>