<?php

class Library_Viewer_Admin {

	private $version_deprecation = '3.0.0';

	/**
	 * @since 2.0.0 LV__file_was_viewed, LV__mime_types, wp_get_mime_types, LV__array_replace_to__in_filenames, LV__array_replace_from__in_foldernames, LV__array_replace_from__in_filenames, LV__folder_html, LV__file_html
	 *
	 * @var array
	 */
	private $deprecated_hooks = [
		'lv_before_breadcrumb_start'	=> 'filter lv_breadcrumb_html',
		'lv_after_breadcrumb_start'		=> 'filter lv_breadcrumb_html',
		'lv_after_breadcrumb_end'		=> 'filter lv_breadcrumb_html',
		'lv_before_breadcrumb_end'		=> 'filter lv_breadcrumb_html',
	];

	/**
	 * Library_Viewer_Admin constructor.
	 *
	 * This function calls the hooks.
	 *
	 * @since 2.0.0
	 * @since 3.0.0 Determine here if these hooks will be executed
	 */
	public function __construct()
	{
		if( !is_admin() ) return;

		add_action('admin_notices', [$this, 'lv_addons_need_update']);
		add_action('admin_notices', [$this, 'deprecated_hooks_notices']);
	}


	/**
	 * This method is a callback for admin_notices action,
	 * that display an admin notice if a deprecated action/filter is used
	 */
	public function deprecated_hooks_notices()
	{
		$LIBRARY_VIEWER_DOCUMENTATION_URL = LIBRARY_VIEWER_DOCUMENTATION_URL;
		foreach ($this->deprecated_hooks as $deprecated_hook => $new_hook) {
			if( has_filter($deprecated_hook) || has_action($deprecated_hook) ){
				echo "<div class='notice notice-error'><p>The <b>$deprecated_hook</b> hook is deprecated from <b>$this->version_deprecation</b> . Please replace it with <b>$new_hook</b>. View <a target='_blank' href='$LIBRARY_VIEWER_DOCUMENTATION_URL'>Documentation</a> for more details.</p></div>";
			}
		}
	}

	/**
	 * Add admin notice that say need to update the Library Viewer Pro in order to work
	 *
	 * @since 2.0.0
	 * @since 3.0.0 lv_pro_need_update renamed to lv_addons_need_update
	 */
	public function lv_addons_need_update()
	{
		if ( defined('LIBRARY_VIEWER_PRO_PLUGIN_BASENAME') && defined('LIBRARY_VIEWER_PRO_VERSION_REQUIRED_FOR_LV') ) {
			require_once ABSPATH . 'wp-admin/includes/plugin.php';
			$plugin_data = get_plugin_data( WP_PLUGIN_DIR  . '/' . LIBRARY_VIEWER_PRO_PLUGIN_BASENAME );
			$plugin_version = $plugin_data['Version'];

			if ( -1 === version_compare($plugin_version, LIBRARY_VIEWER_PRO_VERSION_REQUIRED_FOR_LV) ) {
				echo '<div class="notice notice-error"><p>Library Viewer Pro cannot work. You need to update Library Viewer Pro in order to work in addition to Library Viewer.<br>
					 If you can\'t update Library Viewer Pro, contact me via <a href="mailto:info@pexlechris.dev">email</a></p></div>';
			}
		}

		if ( defined('LIBRARY_VIEWER_FILE_MANAGER_PLUGIN_BASENAME') && defined('LIBRARY_VIEWER_FILE_MANAGER_VERSION_REQUIRED_FOR_LV') ) {
			require_once ABSPATH . 'wp-admin/includes/plugin.php';
			$plugin_data = get_plugin_data( WP_PLUGIN_DIR  . '/' . LIBRARY_VIEWER_FILE_MANAGER_PLUGIN_BASENAME );
			$plugin_version = $plugin_data['Version'];

			if ( -1 === version_compare($plugin_version, LIBRARY_VIEWER_FILE_MANAGER_VERSION_REQUIRED_FOR_LV) ) {
				echo '<div class="notice notice-error"><p>Library Viewer File Manager cannot work. You need to update Library Viewer File Manager in order to work in addition to Library Viewer.<br>
					 If you can\'t update Library Viewer File Manager, contact me via <a href="mailto:info@pexlechris.dev">email</a></p></div>';
			}
		}

		if ( defined('LIBRARY_VIEWER_FOR_WOOCOMMERCE_PLUGIN_BASENAME') && defined('LIBRARY_VIEWER_FOR_WOOCOMMERCE_VERSION_REQUIRED_FOR_LV') ) {
			require_once ABSPATH . 'wp-admin/includes/plugin.php';
			$plugin_data = get_plugin_data( WP_PLUGIN_DIR  . '/' . LIBRARY_VIEWER_FOR_WOOCOMMERCE_PLUGIN_BASENAME );
			$plugin_version = $plugin_data['Version'];

			if ( -1 === version_compare($plugin_version, LIBRARY_VIEWER_FOR_WOOCOMMERCE_VERSION_REQUIRED_FOR_LV) ) {
				echo '<div class="notice notice-error"><p>Library Viewer for Woocommerce cannot work. You need to update Library Viewer for Woocommerce in order to work in addition to Library Viewer.<br>
					 If you can\'t update Library Viewer for Woocommerce, contact me via <a href="mailto:info@pexlechris.dev">email</a></p></div>';
			}
		}
	}



}
