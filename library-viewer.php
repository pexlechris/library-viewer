<?php
/**
 * Plugin Name: Library Viewer
 * Description: This is a File & Folder Viewer of FTP folder: yoursite.com/library. So using the shortcode [library-viewer], you can print the containing folders & files of your library on front-end 
 * Version: 3.3.1
 * Stable tag: 3.3.1
 * Plugin URI: https://www.pexlechris.dev/library-viewer
 * Author: Pexle Chris
 * Author URI: https://www.pexlechris.dev
 * Contributors: pexlechris
 * Text Domain: library-viewer
 * Domain Path: /languages
 * Tested up to: 7.0
 * Requires PHP: 7.4
 * Tested up to PHP: 8.3
 * License: GPLv2
 */

/**
 * The version of the plugin.
 *
 * @since 2.0.0
 * @var string
 */
define('LIBRARY_VIEWER_VERSION', '3.3.1');


if ( ! defined( 'ABSPATH' ) ) {
	die;
}



/**
 * The http or https link of plugin with trailing slash.
 *
 * @since 2.0.0
 * @var string
 */
define('LIBRARY_VIEWER_PLUGIN_DIR_URL', plugin_dir_url( __FILE__ ));

/**
 * The absolute path of plugin file constructor.
 *
 * @since 2.0.0
 * @var string
 */
define('LIBRARY_VIEWER_FILE_ABSPATH', __FILE__ );

/**
 * The absolute path of plugin folder with trailing slash.
 *
 * @since 2.0.0
 * @var string
 */
define('LIBRARY_VIEWER_DIR_ABSPATH', __DIR__ . '/');

/**
 * The URL to buy Library Viewer Pro.
 *
 * @since 2.0.0
 * @var string
 */
define('LIBRARY_VIEWER_PRO_BUY_URL', 'https://www.pexlechris.dev/library-viewer/pro-user');

/**
 * The URL to buy Library Viewer File Manager Addon.
 *
 * @since 2.0.0
 * @var string
 */
define('LIBRARY_VIEWER_FILE_MANAGER_BUY_URL', 'https://www.pexlechris.dev/library-viewer/file-manager');

/**
 * The URL of Library Viewer's Documentation.
 *
 * @since 2.0.0
 * @var string
 */
define('LIBRARY_VIEWER_DOCUMENTATION_URL', 'https://www.pexlechris.dev/library-viewer/docs?library-viewer=yes');

/**
 * The version of Library Viewer Pro, that is required
 * in order to use the Library Viewer Pro shortcode parameters.
 *
 * @since 2.0.0
 * @var string
 */
define('LIBRARY_VIEWER_PRO_VERSION_REQUIRED_FOR_LV', '2.0.5');


/**
 * The version of Library Viewer File Manager, that is required
 * in order to use the Library Viewer File Manager shortcode parameters.
 *
 * @since 2.0.3
 * @var string
 */
define('LIBRARY_VIEWER_FILE_MANAGER_VERSION_REQUIRED_FOR_LV', '1.1.0');



/**
 * The version of Library Viewer for Woocommerce, that is required
 * in order to use the Library Viewer for Woocommerce shortcode parameters.
 *
 * @since 3.0.0
 * @var string
 */
define('LIBRARY_VIEWER_FOR_WOOCOMMERCE_VERSION_REQUIRED_FOR_LV', '1.0.0');


/**
 * Class Library_Viewer_Init.
 *
 * Initialize Library_Viewer_Plugin_Page object and adds the frontend hooks.
 */
class Library_Viewer_Init {

    /**
     * @since 3.0.0
     * @since 3.3.0 Property is now static & private
     *
     * @var string
     */
    private static $file_identifier;

    /**
     * Property that knows if a shortcode should be loaded.
     *
     * @since 3.3.0
     *
     * @var bool
     */
    private static $should_load_shortcode;

    /**
     * @var bool
     */
    private static $should_load_file_viewer;

    /**
     * @since 3.0.0
     *
	 * @var array
	 */
	private $shortcode_class_names;

	/**
	 * @since 3.0.0
	 *
	 * @var array
	 */
	private $file_viewer_class_names;

	/**
	 * @since 3.0.0
	 *
	 * @var bool
	 */
    private $is_first_shortcode_in_page = true;

	/**
	 * Library_Viewer_Init constructor.
	 *
	 * Register the shortcode and all actions of plugin.
	 *
	 * @since 2.0.0
	 */
	public function __construct()
	{
		add_action('init', [$this, 'load_plugin_textdomain'], 1); // @since 3.3.0: priority 10 -> 1

		add_action('wp_loaded', [$this, 'custom_login_redirect_action']);


		/**
		 * Below hooks loading only in frontend!
		 *
		 * @since 3.0.0
         * @since 3.0.4 callback set_file_viewer_class_names commented out
         * @since 3.3.0 -> callback set_shortcode_class_names commented out
         *                  -> All logic is executed by set_library_viewer_global_variable()
         *                  -> Now, hooks are not loaded only in frontend, methods should_load_shortcode() & should_load_file_viewer()
         *                     are responsible for the loading condition, controlled by hooks
         */
        //add_action('init', [$this, 'set_shortcode_class_names'], 100);
        //add_action('init', [$this, 'set_file_viewer_class_names'], 100);
		add_action('init', [$this, 'set_library_viewer_global_variable'], 100);


		add_shortcode('library-viewer', [$this, 'register_shortcode']);

		/**
		 * @since 3.0.0 These classes loading in frontend and backend
		 */
		add_filter('lv_shortcode_class_names', [$this, 'filter_lv_shortcode_class_names'], 5);
		add_filter('lv_file_viewer_class_names', [$this, 'filter_lv_file_viewer_class_names'], 5);


        require_once LIBRARY_VIEWER_DIR_ABSPATH . 'admin/class-library-viewer-plugin-row.php';
        new Library_Viewer_Plugin_Row();

        require_once LIBRARY_VIEWER_DIR_ABSPATH . 'admin/class-library-viewer-admin-notices.php';
        new Library_Viewer_Admin_Notices();

        require_once LIBRARY_VIEWER_DIR_ABSPATH . 'admin/class-library-viewer-admin-page.php';

        /**
         * Register the Library Viewer media admin page.
         *
         * IMPORTANT: If you want to hook another admin page instead of this one,
         * you should do it on the `init` hook with priority between 2 and 5.
         *
         * You can also unhook this callback like this:
         *
         * add_action('init', function () {
         *     remove_action('init', ['Library_Viewer_Init', 'register_library_viewer_media_page'], 5);
         * }, 2);
         */
        add_action('init', [__CLASS__, 'register_library_viewer_media_page'], 5);
    }

	/**
     * @used by self::set_library_viewer_global_variable()
     *
	 * @since 3.0.0
     * @since 3.3.0 Is now private
	 */
    private function set_shortcode_class_names()
	{
		/**
		 * level-1 class: Library_Viewer_Shortcode
		 * 		registered in `frontend/class-library-viewer-shortcode.php` file
		 * level-2 classes: These classes extend the level-1 class or each other
		 * 		registered in `register_library_viewer_shortcode_child_class` action
		 */


		require_once LIBRARY_VIEWER_DIR_ABSPATH . 'frontend/class-library-viewer-shortcode.php';// level-1 class

		/**
		 * Register library viewer shortcode child class filter.
		 *
		 * With this filter, we can register level-2 classes that
		 * extend the level-1 class or each other.
		 *
		 * For example,
		 * in action `register_library_viewer_shortcode_child_class` with priority 5,
		 * we can register the class with name Level_2_Class_1 that extends the Library_Viewer_Shortcode class.
		 * and also in action `register_library_viewer_shortcode_child_class` with priority 6,
		 * we can register the class with name Level_2_Class_2 that extends the Level_2_Class_1 class.
		 * So,
		 * The Level_2_Class_2 class extends the Level_2_Class_1 class that extends the Library_Viewer_Shortcode class.
		 * Now,
		 * we need to tell the code what class need to initialize,
		 * this we can do it with below filter lv_shortcode_class_name.
		 *
		 * @since 2.0.0
		 *
		 * @ignore
		 */
		do_action('register_library_viewer_shortcode_child_class');


		/**
		 * Library Viewer shortcode object name.
		 *
		 * Depends on the $attributes the wp filters determine which object will called.
		 * Library Viewer Pro & Addons use this filter.
		 *
		 * @since 2.0.0
		 * @since 2.0.3 array structure was changed
		 *
		 * @ignore
		 *
		 * @param array $LV_shortcode_class_names The shortcode class names (parent & children).
		 * @param array $attributes see Library_Viewer_Init::register_library_viewer_shortcode()
		 */
		$this->shortcode_class_names = apply_filters('lv_shortcode_class_names', []);
	}

	/**
     * @used by self::set_library_viewer_global_variable()
     *
	 * @since 3.0.0
     * @since 3.3.0 Is now private
     */
    private function set_file_viewer_class_names()
	{
		/**
		 * level-1 class: Library_Viewer_Shortcode
		 * 		registered in `frontend/class-library-viewer-shortcode.php` file
		 * level-2 classes: These classes extend the level-1 class or each other
		 * 		registered in `register_library_viewer_shortcode_child_class` action
		 * alias class: Library_Viewer_File_Alias
		 * 		alias of $lv_shortcode_class_names last item class
		 * level-3 class: Library_Viewer_File
		 * 		registered in `frontend/class-library-viewer-file.php` file
		 * level-4 classes: These classes extend the level-3 class or each other
		 * 		registered in `register_library_viewer_file_child_class` file
		 *
		 * level-4 classes extends the level-3 class extends the alias class extend the level-2 classes extends the level-1 class.
		 */



		/**
		 * Make an alias with name `Library_Viewer_File_Alias` from the
		 * shortcode class name that is used,
		 * so
		 * level-3 class to extend the level-2 class
		 * (if there is not level-2 classes, level-3 class extends level-1 class)
		 */
		class_alias( end($this->shortcode_class_names), 'Library_Viewer_File_Alias');  // alias

		require_once LIBRARY_VIEWER_DIR_ABSPATH . 'frontend/class-library-viewer-file.php';// level-3 class

		/**
		 * Register library viewer file child class filter.
		 *
		 * With this filter, we can register level-4 classes that
		 * extend the level-3 class or each other.
		 *
		 * For example,
		 * in action `register_library_viewer_file_child_class` with priority 5,
		 * we can register the class with name Level_4_Class_1 that extends the Library_Viewer_File class.
		 * and also in action `register_library_viewer_shortcode_child_class` with priority 6,
		 * we can register the class with name Level_4_Class_2 that extends the Level_4_Class_1 class.
		 * So,
		 * The Level_4_Class_2 class extends the Level_4_Class_1 class that extends the Library_Viewer_File class.
		 * Now,
		 * we need to tell the code what class need to initialize,
		 * this we can do it with below filter register_library_viewer_file_child_class.
		 *
		 * @since 2.0.0
		 *
		 * @ignore
		 */
		do_action('register_library_viewer_file_child_class'); // level-4 classes


		/**
		 * Library Viewer's file viewer class name.
		 *
		 * Depends on plugin's settings determine which class will be used for object initialization.
		 * Library Viewer Pro & Addons use this filter.
		 *
		 * @since 2.0.0
		 * @since 2.0.2 Array structure was changed
		 *
		 * @ignore
		 *
		 * @param string $lv_file_viewer_class_names The file viewer class name.
		 */
		$this->file_viewer_class_names = apply_filters('lv_file_viewer_class_names', []);
	}

    public function set_library_viewer_global_variable()
    {
		global $library_viewer_object;

		if ( self::should_load_file_viewer() ) {
            $this->set_shortcode_class_names();
            $this->set_file_viewer_class_names();
			$library_viewer_class_name  = end($this->file_viewer_class_names);
			$library_viewer_object      = new $library_viewer_class_name();

		}elseif( self::should_load_shortcode() ){
            $this->set_shortcode_class_names();
            $library_viewer_class_name  = end($this->shortcode_class_names);
			$library_viewer_object      = new $library_viewer_class_name();
        }
    }

	public function register_shortcode($attrs)
	{
        if( self::should_load_shortcode() === false ){
            return '';
        }

		global $library_viewer_object;

        // in order to avoid initializing again if already initialized
		if( $library_viewer_object && $this->is_first_shortcode_in_page ){ // is_first_shortcode_in_page: default is true
			$html = $library_viewer_object->shortcode_html_contents($attrs);

			$this->is_first_shortcode_in_page = false;
			return $html;
		}

		$class_name = end($this->shortcode_class_names);

		$new_instance = new $class_name(false);
		$html = $new_instance->shortcode_html_contents($attrs);

		// in order previous object/instance be available while shortcode is executed
		$library_viewer_object = $new_instance;
		return $html;
	}

	public function filter_lv_shortcode_class_names(){
		return array('Library_Viewer_Shortcode');
	}

	public function filter_lv_file_viewer_class_names(){
		return array('Library_Viewer_File');
	}


	/**
	 * Load plugin's textdomain.
	 *
	 * This hook loads the plugins' po & mo files.
	 * For backend, user's language is being loaded.
	 * For frontend, site's language is being loaded.
	 *
	 * @since 2.0.0
	 * @since 2.0.4 uses load_plugin_textdomain function
	 * @since 2.0.9 Fix plugin rel path
	 */
	public function load_plugin_textdomain() {

		load_plugin_textdomain(
			'library-viewer',
			false,
			'library-viewer/languages'
		);
	}

	/**
	 * Custom login redirect.
	 *
	 * If the get parameter `redirect_to` exists in current URL, the user is being redirected.
	 *
	 * @since 1.0.0
	 * @since 3.0.0 Moved from class Library_Viewer_Init to class Library_Viewer_File
     * @since 3.0.1 Moved back again to class Library_Viewer_Init
	 */
	public function custom_login_redirect_action()
	{
		if( isset($_GET['action']) ) return;
		if( is_user_logged_in() && !empty($_GET['redirect_to']) ){
			if ( wp_safe_redirect( $_GET['redirect_to'] ) ) {
				exit;
			}
		}
	}

    public static function register_library_viewer_media_page(){

        /**
         * Filter the capabilities required to access the default Library Viewer admin page under Media.
         *
         * By default, this admin page is registered under "Media" (upload.php) with 'manage_options' capability.
         * Developers can use this filter to change which capabilities/roles are required to access it.
         *
         * @since 3.3.0
         *
         * @param array $manage_caps List of capabilities/roles required to manage this admin page.
         */
        $manage_caps = apply_filters('lv_default_admin_page_manage_caps', ['manage_options']);

        new Library_Viewer_Admin_Page(
            'upload.php',
            'library-viewer',
            __('Library Viewer', 'library-viewer'),
            $manage_caps
        );
    }

    /**
     * Get the Library Viewer file identifier.
     *
     * This identifier is used to detect special Library Viewer file requests
     * inside the REQUEST_URI. When the identifier is found, the plugin will
     * serve the file directly and stop further PHP execution.
     *
     * The value is retrieved through the `lv_file_identifier` filter and is
     * cached after the first call.
     *
     * IMPORTANT: This method should be used after the `init` action with
     * priority greater than 5.
     *
     * @since 3.3.0
     *
     * @return string|null The file identifier string.
     */
    public static function get_file_identifier()
    {
        if( self::$file_identifier === null ){

            /**
             * Library Viewer file identifier filter.
             *
             * If file identifier is found in REQUEST_URI, the plugin readfile and exits the code.
             * So with this filter, we can change this string to this of our choice.
             * BE CAREFUL!! If this string will be found in a URL of your website, the php execution will stop here.
             *
             * @param string $file_identifier Library Viewer file identifier keyword. Default is `LV`.
             * @since 2.0.0
             * @since 3.0.0 Called at `init` action, and its value cannot be changed.
             */
            $file_identifier = apply_filters('lv_file_identifier', 'LV');

            self::$file_identifier = $file_identifier;
        }

        return self::$file_identifier;
    }

    /**
     * Whether the Library Viewer's file viewer should be loaded.
     *
     * IMPORTANT: Do not call this method before the `init` action with priority 6.
     * It should only be used after `init` has run with priority greater than 5.
     *
     * @since 3.3.0
     *
     * @return bool
     */
    public static function should_load_file_viewer()
    {
        if( self::$should_load_file_viewer === null ){

            $request_uri = parse_url($_SERVER['REQUEST_URI'] ?? '', PHP_URL_PATH);
            $should_load = strpos( $request_uri,  '/' . self::get_file_identifier() . '/' ) !== false;

            /**
             * Filter whether the Library Viewer's file viewer should be loaded for the current request.
             *
             * This filter allows developers to override the automatic detection of the
             * file viewer request. By default, the file viewer is loaded if the request URI
             * contains the Library Viewer file identifier (see Library_Viewer_Init::get_file_identifier()).
             *
             * IMPORTANT: This filter is applied inside Library_Viewer_Init::should_load_file_viewer()
             * and the result is cached after the first call.
             *
             * @since 3.3.0
             *
             * @param bool $should_load Indicates whether the plugin's file viewer  should be loaded.
             *                          Defaults to true if request URI without the get parameters
             *                          contains the Library_Viewer_Init::get_file_identifier().
             *
             */
            $should_load = apply_filters('lv_should_load_file_viewer', $should_load);
            self::$should_load_file_viewer = $should_load;
        }

        return self::$should_load_file_viewer;
    }

    /**
     * Whether the shortcode should be loaded.
     *
     * IMPORTANT: Do not call this method before the `init` action with priority 6.
     * It should only be used after `init` has run with priority greater than 5.
     *
     * @since 3.3.0
     *
     * @return bool
     */
    public static function should_load_shortcode()
    {
        if( self::$should_load_shortcode === null ){

            global $pagenow;

            if( $pagenow === 'wp-login.php' ){
                $should_load = false;
            }elseif( is_admin() ) {
                $should_load = false;
            }else{
                $should_load = true;
            }


            $should_load = apply_filters_deprecated('lv_is_frontend', [$should_load, null], '3.3.0', 'lv_should_load_shortcode');

            /**
             * Filter whether the Library Viewer shortcode should be loaded for the current request.
             *
             * This filter allows developers to override the automatic detection of whether
             * the shortcode should be loaded. By default, the shortcode is loaded only on
             * frontend pages (not in admin or on wp-login.php).
             *
             * IMPORTANT: This filter is applied inside Library_Viewer_Init::should_load_shortcode()
             * and the result is cached after the first call.
             *
             * @since 3.3.0
             * @note This filter is the replacement for the deprecated `lv_is_frontend` filter.
             *
             * @param bool $should_load Indicates whether the shortcode should be loaded.
             *                          Defaults to true on frontend pages, false on admin or login pages.
             */
            $should_load = apply_filters('lv_should_load_shortcode', $should_load);
            self::$should_load_shortcode = $should_load;
        }

        return self::$should_load_shortcode;
    }

	/**
     * @since 3.0.2
     * @since 3.3.0 deprecated
     *
     * @deprecated. Use Library_Viewer_Init::should_load_shortcode() instead.
     *
	 * @param string|null $function_name
	 * @return bool
	 */
    public static function is_frontend( $function_name = null )
	{
        return self::should_load_shortcode();
    }

}

new Library_Viewer_Init();

/**
 * Library viewer error function.
 *
 * This function returns the appropriate error message according to the 1st parameter.
 *
 * @since 2.0.0
 * @since 2.0.7 need_to_login_button, go_back_button cases have been added in order to give the ability to developer to use them.
 *              Also added in filter lv_error_message the $s, $s2 strings. I f not exist, are just empty string.
 *              Strip stings that contains style html tags after translations.
 *
 * @param string $case According to case determines which error message will return.
 * @param string $s The %1$s value.
 * @param string $s2 The %2$s value.
 * @return string $string The returned string.
 */
function library_viewer_error($case, $s = '', $s2 = '') {
	switch ($case) {
        case 'go_back_button':
            ob_start();
            ?>
			<div class="library-viewer-go-back-button-wrapper">
                <button class="library-viewer-go-back-button" onclick="window.history.back();"><?php echo $s; ?></button>
            </div>
            <?php
            $string = ob_get_clean();
			break;

        case 'need_to_buy_access_buttons':
            ?>
            <div class="library-viewer-need-to-buy-access-wrapper">

                <span class="message--wrapper">
                    <?php _e( 'To get full access to this Library, you need to buy access.', 'library-viewer-for-woocommerce' ); ?>
                </span>

                <div class="buttons--wrapper">

                    <a href="<?php echo esc_url($s); ?>" class="button library-viewer-need-to-buy-access">
						<?php _e('Buy Access', 'library-viewer');?>
                    </a>

                    <?php if( !is_user_logged_in() ): ?>
                        <a href="<?php echo esc_url($s2); ?>" class="button library-viewer-need-to-login-button">
                            <?php _e('Login', 'library-viewer')?>
                        </a>
                    <?php endif; ?>

                </div>

            </div>
            <?php
			$string = ob_get_clean();
			break;

		case 'need_to_login_button':
			ob_start();
			?>
			<div class="library-viewer-need-to-login-button-wrapper">
                <span><?php echo $s2 ?: __('You need to login to view this page.', 'library-viewer' ); ?></span>
				<a href="<?php echo esc_url($s); ?>" class="button library-viewer-need-to-login-button"><?php _e('Login', 'library-viewer')?></a>
			</div>
			<?php
			$string = ob_get_clean();
			break;

		case 'path_folder_created':
			$string = sprintf(
			// translators: %s is folder name
			__("The folder <strong>%s</strong> has been created. This page will be refreshed. Please wait.", 'library-viewer'),
				$s
			);
			break;

		case 'file_not_allowed':
			$string = __("You haven't access to files from this folder.", 'library-viewer');
			break;

		case 'file_not_exists':
			$string = __("File doesn't exists.", 'library-viewer');
			break;

		case 'no_appropriate_capabilities':
			$string = __("Sorry. You haven't the appropriate capabilities to view the files of our Library Viewer.", 'library-viewer');
			break;

		case 'redirect_to_login':
			$string = sprintf(
				// translators: %s is seconds
				__('You must login to view this file.<br>You will be redirected to the login page in <strong>%s seconds.</strong>', 'library-viewer'),
				$s
			);
			break;

		case 'php_forbidden':
			$string = __('Download is forbidden for php files', 'library-viewer');
			break;

		case 'shortcode_non_registered_class':
			$string = __('LV_shortcode_class_name filter has returned a non registered class.', 'library-viewer');
			break;

		case 'shortcode_non_registered_method':
			$string = sprintf(
				// translators: %s is the name of class
				__('shortcode_html_contents method is not registered in %s class.', 'library-viewer'),
				$s
			);
			break;

		case 'file_non_registered_class':
			$string = __('LV_file_viewer_class_name filter has returned a non registered class.', 'library-viewer');
			break;

		case 'non_registered_method_in_class':
			$string = sprintf(
				/* translators: %2$s is the name of class
				%1$s is the name of method */
				__('%1$s method is not registered in %2$s class.', 'library-viewer'),
				$s,
				$s2
			);
			break;

		case 'shortcode_more_than_1_times':
			$s2 = '<a href="' . LIBRARY_VIEWER_PRO_BUY_URL . '" target="_blank">Library Viewer Pro</a>';
			$string = sprintf(
				/* translators: %1$s is [library-viewer]
				%2$s is Library Viewer Pro Buy URL */
				__('You cannot use shortcode %1$s more than 1 times in the same page without the use of parameter <strong>url_suffix</strong>. You can use this parameter with the <strong>latest version</strong> of %2$s.<br>View documentation of <strong>url_suffix</strong> parameter for more.', 'library-viewer'),
				$s, $s2
			);
			break;

		case 'not_acceptable_parameter':
			$lv_pro_url = '<a target="_blank" href="' . LIBRARY_VIEWER_PRO_BUY_URL . '">Library Viewer Pro</a>';
			$lv_fm_url = '<a target="_blank" href="' . LIBRARY_VIEWER_FILE_MANAGER_BUY_URL . '">Library Viewer File Manager Addon</a>';
			$lv_pro_parameters = array('breadcrumb', 'shown_folders', 'hidden_folders', 'shown_files', 'hidden_files', 'waiting_seconds', 'url_suffix');
			$lv_fm_parameters = array('delete_folder', 'delete_file', 'rename_folder', 'rename_file', 'create_folder', 'upload_file', 'unzip_file', 'download_folder', 'download_file', 'add_folder_tags', 'add_file_tags');

			if ( 'path' === $s ) {
				$string = sprintf(
					// translators: %s Library Viewer Pro URL
					__('You cannot use <b>path</b> parameter in shortcode.<br>If you want to display the containing files and the containing folders of <strong>a different folder</strong> (instead of library) of your (FTP) server to your users in the front-end, <br> consider buying %s', 'library-viewer'),
					$lv_pro_url
				);
			} elseif ( in_array($s, $lv_pro_parameters) ) {
				$string = sprintf(
					/* translators: %1$s is the parameter
					%2$s is the Library Viewer Pro URL */
					__('You cannot use <b>%1$s</b> parameter in shortcode.<br>If you want to use <b>%1$s</b> parameter,<br> consider buying %2$s or if you already have it installed, update it to the latest version.', 'library-viewer'),
					$s,
					$lv_pro_url
				);
			} elseif ( in_array($s, $lv_fm_parameters) ) {
				$string = sprintf(
				/* translators: %1$s is the parameter
				%2$s is the Library Viewer Pro URL */
					__('You cannot use <b>%1$s</b> parameter in shortcode.<br>If you want to use <b>%1$s</b> parameter,<br> consider buying %2$s or if you already have it installed, update it to the latest version.', 'library-viewer'),
					$s,
					$lv_fm_url
				);
			} else {
				$string = sprintf(
					// translators: %s shortcode parameter
					__('Probably, <b>%s</b> parameter is not supported yet.', 'library-viewer'),
					$s
				);
			}
			break;

		case 'folder_not_exists':
			$string = __("Error 404: This folder doesn't exists.", 'library-viewer');
			break;

		case 'no_access':
			$string = __("You haven't access to this folder.", 'library-viewer');
			break;

		case 'empty_folder':
			$string = __("This folder is empty.", 'library-viewer');
			break;

		case 'go_back':
			$string = __('Go Back', 'library-viewer');
			break;

		case 'redirection_page_title':
			$string = __('Redirection to login page', 'library-viewer');
			break;

		default:
			$string = '';
	}

	//sanitization
	$string = str_replace(
		array('<script>', '</script>', '<style>', '</style>'),
		'',
		$string
	);

	/**
	 * Error message filter.
	 *
	 * Filter the plugin's error messages according to $case.
	 *
	 * @since 2.0.3
     * @since 2.0.7 $s & $s2 parameters added
	 *
	 * @param string $string The error message HTML.
	 * @param string $case The case. According to this,
	 *                     different messages will be displayed.
	 * @param string $s The first string that is used in the main string. If exists, in that case.
	 * @param string $s2 The second string that is used in the main string. If exists, in that case.
	 */
	$string = apply_filters('lv_error_message', $string, $case, $s, $s);
	return $string;
}

