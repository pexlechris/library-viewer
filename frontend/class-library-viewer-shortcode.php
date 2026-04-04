<?php

/**
 * Class Library_Viewer_Shortcode.
 *
 * With this class and the method `shortcode_html_contents`,
 * we can print the library in the front-end.
 *
 * @since 2.0.0
 */
class Library_Viewer_Shortcode {

	/**
	 * The parameters that are passed to the shortcode.
	 *
	 * @since 2.0.0
	 * @var array $parameters {
	 * 		The shortcode parameters.
	 *
	 * 		@type string $have_file_access @see Library_Viewer_Shortcode::init_have_file_access_parameter
	 * 		@type string $my_doc_viewer @see Library_Viewer_Shortcode::init_my_doc_viewer_parameter
	 * 		@type string $login_page @see Library_Viewer_Shortcode::init_login_page_parameter
	 * }
	 */
	protected $parameters = null;

	/**
	 * All the useful variables that methods use, parameters are included.
	 *
	 * Note1: for each parameter not passed to the shortcode,
	 * $globals keeps its DEFAULT value, that is initialized
	 * in `init_parameters_default_values` method,
	 * except these that is initialized as null and defined in `init_global_{$global}` method.
	 * Note2: The parameters that accept commas seperated strings are converted to arrays.
	 *
	 * @since 2.0.0
	 *
	 * @var array $globals{
	 * 		The globals variables.
	 *
	 * 		@type array $have_file_access The shortcode parameter `have_file_access`, as an array!
	 * 		@type string $my_doc_viewer The shortcode parameter `my_doc_viewer`.
	 * 		@type string $login_page The shortcode parameter `login_page`.
	 * 		@type string $path For Library Viewer, `path` is `library`. Pro parameter!
	 * 		@type array $have_folder_access The users that have folder access. Default are the users that have file access.
	 * 		@type array $hidden_folders For Library Viewer, `$hidden_folders` is `hidden-folder`. Pro parameter!
	 * 		@type array $hidden_files For Library Viewer, `$hidden_files` are .php', '.ini', 'hidden-file'. Pro parameter!
	 * 		@type string $abspath the absolute path of WordPress installation. Default value is the WP constant ABSPATH.
	 * 		@type string $real_path The real path of current folder with a trailing slash.
	 * 		@type string $dir The link (fake link) of current folder without trailing slash.
	 * 		@type string $current_viewer The current viewer. Are you viewing a folder or a file?
	 * 									 If you are viewing a folder, current_viewer is `folder`.
	 * 									 If you are viewing a file, current_viewer is `file`.
	 * 		@type string $current_page_url URL of shortcode's page.
	 * 									   Without get parameters, without trailing slash and with url_suffix if exists.
	 * 									   Useful only for Library Viewer file viewer. DON'T use it in your hooks.
	 *		@type string $real_shortcode_page_link The URL of shortcode's page without get parameters.
	 * 		@type string $file_identifier String that identifies if file will be loaded. Default is `LV`.
	 * 		@type array $folder_fake_path_symbols These symbols will replace the `$folder_real_path_symbols`,
	 * 											  if the fake path of folder was called.
	 * 		@type array $folder_real_path_symbols These symbols will replace the `$folder_fake_path_symbols`,
	 * 											  if the real path of folder will be asked.
	 * 											  (real path is the relative path of the folder)
	 * 		@type array $file_fake_path_symbols These symbols will replace the `$file_real_path_symbols`,
	 * 											if the fake path of file was called.
	 * 		@type array $file_real_path_symbols These symbols will replace the `$file_fake_path_symbols`,
	 * 											if the real path of file will be asked.
	 * 											(real path is the relative path of the file)
	 * }
	 */
	protected $globals = array();

	/**
	 * All folder counter.
	 *
	 * @since 2.0.0
	 * @var int $all_folders_counter
	 */
	protected $all_folders_counter;

	/**
	 * All files counter.
	 *
	 * @since 2.0.0
	 * @var int $all_files_counter
	 */
	protected $all_files_counter;

	/**
	 * Invalid parameters that passed to the shortcode.
	 *
	 * @since 2.0.0
	 * @var array $invalid_parameters
	 */
	protected $invalid_parameters = array();

	/**
	 * Errors that would be displayed in the shortcode contents.
	 *
	 * @since 2.0.0
	 * @var string $display_errors
	 */
	protected $display_errors = '';

	/**
	 * The shortcode class names,
	 * parent & children.
	 *
	 * @since 2.0.3
	 * @since 3.0.0 Removed!
	 *
	 * @var array
	 */
	//protected $shortcode_class_names;

	/**
	 * The file viewer class names,
	 * parent & children.
	 *
	 * @since 2.0.3
	 * @since 3.0.0 Removed!
	 *
	 * @var array
	 */
	//protected $file_viewer_class_names;

	/**
	 * The shortcode & file viewer class names,
	 * parents & children.
	 *
	 * @since 2.0.3
	 * @since 3.0.0 Removed!
	 *
	 * @var array
	 */
	//protected $all_class_names;

	/**
	 * All the class names of this object.
	 * Shortcode class names for shortcode call.
	 * All, shortcode and file names for file viewer call.
	 *
	 * @since 3.0.0
	 *
	 * @var array
	 */
	protected $class_names;

	/**
	 * If init actions has been executed yet or not.
	 *
	 * @since 3.0.0
	 *
	 * @var bool
	 */
	protected $initialized = false;

	/**
	 * @since 3.0.0
	 *
	 * @var null|array
	 */
	protected $page_shortcodes_matches = null;

	/**
	 * Class constructor.
	 *
	 * Ued to register front-end hooks.
	 *
	 * @since 2.0.0
	 * @since 3.0.0 code moved to init method.
	 */
	public function __construct( $file_identifier = false, $register_hooks = true )
	{
		$this->class_names = array_reverse(array_values(array_merge([get_class($this)], class_parents($this))));

		$this->globals['file_identifier'] = $file_identifier !== false
			? $file_identifier
			/**
			 * @ignore
			 */
			: apply_filters('lv_file_identifier', 'LV');

		if($register_hooks){
			add_filter('document_title_parts', [$this, 'maybe_alter_document_title']);
			add_action('wp_enqueue_scripts', [$this, 'enqueue_styles_and_scripts'] );
		}

	}

	/**
	 * Callback of hook document_title_parts.
	 *
	 * @since 3.0.0
	 *
	 * @param array $parts
	 * @return array
	 */
	public function maybe_alter_document_title($parts)
	{
		$current_folder = false;
		$single_shortcode_attrs = $this->get_single_shortcode_attrs();

		if( $single_shortcode_attrs !== false ){
			$this->init($single_shortcode_attrs);
			$current_folder = $this->get_current_folder();
		}

		$prepend_default_string = $single_shortcode_attrs !== false && is_page()
			? $current_folder['folder_name']
			: null;

		/**
		 * Filter to prepend the current folder in document title parts.
		 *
		 * Case 1:
		 * Use the following hook to disable this functionality:
		 * add_filter('lv_prepend_document_title', '__return_false');
		 *
		 * Case 2:
		 * Use te following hook to prepend the document title also for 'my_post_type' post type:
		 * add_filter('lv_prepend_document_title', function($prepend_string, $current_folder){
		 * 		if( is_singular('my_post_type') ){
		 * 			return $current_folder;
		 * 		}
		 *     return $prepend_string;
		 * }, 10, 2);
		 *
		 * @since 3.0.0
		 *
		 * @param string $prepend_default_string Default value is the current folder name,
		 * 										 if the library-viewer shortcode exists
		 * 										 exactly once and only in a page. Otherwise, null.
		 * @param array $current_folder{
		 * 		The folder array with its details.
		 *
		 *		@type string $folder_name The folder name as link text.
		 * 		@type string $folder_abs_path The absolute path of the folder.
		 * 		@type string $folder_fake_link The folder fake link as link URL.
		 * }
		 */
		$prepend_string_to_doc_title = apply_filters('lv_prepend_document_title', $prepend_default_string, $current_folder);

		if( $prepend_string_to_doc_title ){
			$parts = array_merge(['folder' => $prepend_string_to_doc_title], $parts);
		}


		return $parts;
	}

	/**
	 * @since 3.0.0
	 *
	 * @return array|false Shortcode attrs as array, or false if there in no or more than 1 shortcode in same page
	 */
	public function get_single_shortcode_attrs()
	{
		$matches = $this->get_page_shortcodes_matches();
		if( count($matches) !== 1 ) return false;

		$attrs = shortcode_parse_atts( $matches[0][3] );

		return $attrs;
	}

	/**
	 * @since 3.0.0
	 */
	public function get_page_shortcodes_matches()
	{
		if( $this->page_shortcodes_matches !== null ) return $this->page_shortcodes_matches;

		preg_match_all( '/' . get_shortcode_regex(['library-viewer']) . '/', get_the_content(), $matches, PREG_SET_ORDER );
		return $matches;
	}

	/**
	 * @since 3.0.0
	 */
	public function get_page_shortcodes_counter()
	{
		$matches = $this->get_page_shortcodes_matches();

		return count($matches);
	}

	/**
	 * @since 3.0.0
	 */
	public function get_globals()
	{
		if( !$this->initialized ) return [];

		return $this->globals;
	}

	/**
	 * @since 3.0.0
	 *
	 * @uses global $library_viewer_styles_and_scripts_enqueued
	 *
	 * @param bool $is_in_shortcode. Default is false.
	 */
	public function enqueue_styles_and_scripts( $is_in_shortcode = false )
	{
		global $library_viewer_styles_and_scripts_enqueued;

		if( !empty( $library_viewer_styles_and_scripts_enqueued ) ) return;

		if( $is_in_shortcode !== true ){
			if( $this->get_page_shortcodes_counter() === 0 ) return;
		}

		$this->action('enqueue_styles');
		$this->action('enqueue_scripts');
		$library_viewer_styles_and_scripts_enqueued = true;
	}


	/**
	 * Library_Viewer_Shortcode init method.
	 *
	 * Inits the `globals` and `parameters` properties.
	 *
	 * @since 3.0.0
	 * @param array $parameters The parameters that are passed to the shortcode. Default is empty array.
	 */
	protected function init( $parameters = [] )
	{
		/**
		 * Init runs only one time
		 */
		if( $this->initialized ) return;
		$this->initialized = true;

		/**
		 * @since 2.0.8 $parameters added
		 */
		$this->action('init_globals_before_init_parameters', $parameters);

		$this->init_parameters($parameters);

		$this->action('init_parameters_default_values', $this->parameters);

//		$this->action('init_globals_after_init_parameters', $parameters);

		$rest_globals = $this->filter('rest_globals', []);

		/**
		 * globals assigned as null, will be initialized with a value by method init_global_{$_global}
		 */
		foreach ($rest_globals as $the_global) {
			if ( !isset($this->globals[$the_global]) ) {// it may be declared before
				$this->globals[$the_global] = null;
			}
		}

		/**
		 * If method exists for each parameter, initialized.
		 * If not, is added to `invalid_parameters` property.
		 */
		foreach ($this->parameters as $parameter_name => $v) {
			$method = 'init_parameter_' . $parameter_name;
			if ( method_exists($this, $method) ) {
				$this->$method($this->parameters);
			} else {
				$this->invalid_parameters[] = $parameter_name;
			}
		}

		$filter_allowed_parameters = $this->filter('filter_allowed_parameters', []);

		foreach ($filter_allowed_parameters as $parameter) {
			if ( isset($this->globals[$parameter]) ) {

				/**
				 * Filter the $parameter (the parameters as globals 'have_file_access' etc...).
				 *
				 * With this filter, you can filter the parameters (as global variables).
				 * These parameters can be filtered BEFORE the rest globals' initialization.
				 *
				 * But I mean the corresponding globals variables,
				 * not exactly the strings/parameters that user pass in the shortcode.
				 *
				 * For example,
				 * When the shortcode is
				 * [library-viewer have_file_access="editor, author"]
				 * the $global variable have_file_access is the array
				 * array(
				 *		0 => 'editor',
				 *		1 => 'author'
				 * )
				 * and NOT the 'editor, author' string.
				 *
				 * @since 2.0.3
				 *
				 * @param string|array $this->globals[$parameter] The value of the $parameter variable.
				 * @param array $this->globals See property's documentation.
				 */
				$this->globals[$parameter] = apply_filters("lv_filter_global_{$parameter}", $this->globals[$parameter], $this->globals);
			}
		}

		/**
		 * globals assigned as null, will be initialized with a value by method init_global_{$_global}
		 */
		foreach ($this->globals as $k => $v) {
			if ( is_null($v) ) {
				$method = 'init_global_' . $k;
				if ( method_exists($this, $method) ) {
					$this->$method();
				} else {
					wp_die(library_viewer_error(
						'non_registered_method_in_class',
						$method,
						get_class($this)
					));
				}
			}
		}

		$filter_allowed_globals = $this->filter('filter_allowed_globals', []);

		foreach ($filter_allowed_globals as $global) {
			if ( isset($this->globals[$global]) ) {

				/**
				 * Filter the $global (the parameters as globals 'have_file_access' etc...).
				 *
				 * With this filter, you can filter the global variables that class allows.
				 * The globals that can be filtered, maybe are the parameters of the shortcode.
				 * These parameters can be filtered AFTER the rest globals' initialization.
				 *
				 * But I mean the corresponding globals variables,
				 * not exactly the strings/parameters that user pass in the shortcode.
				 *
				 * For example,
				 * When the shortcode is
				 * [library-viewer have_file_access="editor, author"]
				 * the $global variable have_file_access is the array
				 * array(
				 *		0 => 'editor',
				 *		1 => 'author'
				 * )
				 * and NOT the 'editor, author' string.
				 *
				 * @since 2.0.0
				 *
				 * @param string|array $this->globals[$global] The value of the $global variable.
				 * @param array $this->globals See property's documentation.
				 */
				$this->globals[$global] = apply_filters("lv_filter_global_{$global}", $this->globals[$global], $this->globals);
			}
		}

		if ( 'shortcode' == $this->globals['current_viewer'] && $this->rtrim($this->globals['real_path'], '/') == $this->globals['path'] && !file_exists($this->globals['real_path']) ) {
			mkdir($this->globals['abspath'] . $this->globals['real_path']);
			echo library_viewer_error('path_folder_created', $this->globals['path']) . '<script>window.location.reload(true);</script>';
		}

	}

	/**
	 * Initialize the parameters
	 *
	 * @since 2.0.0
	 *
	 * @param array $parameters The shortcode parameters.
	 */
	protected function init_parameters( $parameters = [] )
	{
		$this->parameters = $parameters ?: [];
	}

	protected function Library_Viewer_Shortcode__rest_globals($rest_globals)
	{
		return array('abspath', 'current_viewer', 'current_page_url', 'real_shortcode_page_link', 'file_identifier', 'folder_fake_path_symbols', 'folder_real_path_symbols',
			'file_fake_path_symbols', 'file_real_path_symbols', 'dir', 'real_path', 'have_folder_access');
	}

	/**
	 * This method acts like WordPress filters.
	 *
	 * It calls the method `$class_name.'__'.$hook_name`!
	 *
	 * @since 2.0.3
	 * @since 2.0.8 hook method removed, filter method added.
	 * @since 3.0.0 Arg $iterate_class_names removed.
	 *
	 * @param string $hook_name
	 * @param mixed|null $var The variable that will be filtered
	 * @param string $iterate_class_names In which classes will be executed.
	 *
	 * @return mixed|null
	 */
	protected function filter($hook_name, $var = null, $iterate_class_names = '')
	{
		foreach($this->class_names as $class_name){
			$method = $class_name . '__' . $hook_name;
			if ( method_exists($this, $method) ) {
				$var = $this->$method($var);
			}
		}

		return $var;
	}

	/**
	 * This method acts like WordPress actions.
	 *
	 * It calls the method `$class_name.'__'.$hook_name`!
	 *
	 * @since 2.0.3
	 * @since 2.0.8 hook method removed, action method added.
	 * @since 3.0.0 Arg $iterate_class_names removed.
	 *
	 * @param string $hook_name
	 * @param mixed|null $var The variable that is passed as argument
	 *
	 * @return mixed|null
	 */
	protected function action($hook_name, $var = null)
	{
		foreach($this->class_names as $class_name){
			$method = $class_name . '__' . $hook_name;
			if ( method_exists($this, $method) ) {
				$this->$method($var);
			}
		}

		return $var;
	}

	/**
	 * Filter-allowed globals method.
	 *
	 * This method determines which globals variables can be filtered by wordpress filters.
	 *
	 * @since 2.0.0
	 *
	 * @return array The filter-allowed globals array.
	 */
	protected function Library_Viewer_Shortcode__filter_allowed_globals(){
		return array('have_file_access', 'my_doc_viewer', 'login_page');
	}

	/**
	 * Filter-allowed parameters method.
	 *
	 * This method determines which parameters variables can be filtered by wordpress filters.
	 *
	 * @since 2.0.3
	 *
	 * @return array The filter-allowed globals array.
	 */
	protected function Library_Viewer_Shortcode__filter_allowed_parameters(){
		return array('have_file_access', 'my_doc_viewer', 'login_page');
	}

	/**
	 * This method inits the default values of parameters.
	 *
	 * @since 2.0.0
	 *
	 * @param array $parameters The shortcode parameters.
	 */
	protected function Library_Viewer_Shortcode__init_parameters_default_values($parameters)
	{
		/**
		 * Parameters! Will be assigned by method init_parameter_{$_global}
		 */
		$this->globals['have_file_access'] = array('all');
		if ( !isset($parameters['have_file_access']) || 'all' == $parameters['have_file_access'] ) {
			$this->globals['my_doc_viewer']	 = 'default';
		} else {
			$this->globals['my_doc_viewer']	 = 'library-viewer';
		}
		$this->globals['login_page']	 = 'wp-login.php';

		/**
		 * Library Viewer Pro parameters.
		 */
		$this->globals['path']			  = 'library';
		$this->globals['hidden_folders']  = array('hidden-folder');
		$this->globals['hidden_files']    = array('.php', '.ini', 'hidden-file');
		$this->globals['waiting_seconds'] = 5;
	}

	/**
	 * Inits the `have_file_access` parameter.
	 *
	 * *******************************************************************************
	 * have_file_access parameter determines which user have access to view the files.
	 * Accepts user capabilities or roles separated by commas, `logged_in` and `all`.
	 * "all" is the DEFAULT value.
	 *
	 * HAVE_FILE_ACCESS PARAMETER USE CASES
	 *
	 * - have_file_access="all" : public access, all people can open the files.
	 * - have_file_access="administrator" : only administrators can open the files.
	 * - have_file_access="administrator,author,subscriber" : only administrators,
	 *   authors and subscribers can open the files.
	 * - have_file_access="edit_posts" : only those that have the CAPABILITY
	 *   to edit posts, can open the files.
	 * - have_file_access="subscriber,edit_posts" : only subscribers and those that
	 *   have the CAPABILITY to edit posts, can open the files.
	 * - have_file_access="logged_in" : all logged-in users can open the files.
	 * - have_file_access="customer_with_folder_access" : LIBRARY VIEWER FOR WOOCOMMERCE capability.
	 *   This parameter grants permission to customers who purchase a "with folder access" product.
	 * *******************************************************************************
	 *
	 * @param array $parameters The shortcode parameters.
	 *
	 * @since 2.0.0
	 * @since 2.0.8 "customer_with_folder_access" LIBRARY VIEWER FOR WOOCOMMERCE capability
	 * 				use case has been added
	 */
	protected function init_parameter_have_file_access($parameters)
	{
		if( !empty($parameters['have_file_access']) ) {
			$have_file_access = $this->convert_string_to_array($parameters['have_file_access']);

			$this->globals['have_file_access'] = $have_file_access;
		}
		//else: the default value
	}

	/**
	 * Inits the `my_doc_viewer` parameter.
	 *
	 * **************************************************************************************
	 * my_doc_viewer parameter determines in which viewer the file will be opened.
	 * Accepts `default`, `library-viewer` or any other link.
	 * If $have_file_access=='all', `default` is the default value,
	 * If not, `library-viewer` is the default value.
	 *
	 * MY_DOC_VIEWER PARAMETER USE CASES
	 *
	 * NOTE: If have_file_access is NOT in the DEFAULT state, my_doc_viewer does NOTHING.
	 *
	 * - my_doc_viewer="default" : the files open in the default viewer of your browser
	 *   or they are downloaded.
	 * - my_doc_viewer="library-viewer" : the files open in the Library Viewer’ viewer.
	 * - my_doc_viewer="https://docs.google.com/viewerng/viewer?url=" : the files open
	 *   with Google Doc Viewer.
	 *   (The advantage of google doc viewer is that opens
	 *   ALL the files -pdf,doc,docx,ppt etc- in the browser, in smartphones too)
	 * - my_doc_viewer="http://ouo.io/qs/Is36k2da?s=" : the files open via paid URL shortener
	 * - Or : you can use your custom viewer.
	 * **************************************************************************************
	 *
	 * @since 2.0.0
	 * @param array $parameters The shortcode parameters.
	 */
	protected function init_parameter_my_doc_viewer($parameters)
	{
		$my_doc_viewer = !empty( $parameters['my_doc_viewer'] ) ? $parameters['my_doc_viewer'] : $this->globals['my_doc_viewer'];
		if( !in_array( $my_doc_viewer, ['default', 'library-viewer'] ) ){
			$my_doc_viewer = esc_url( $my_doc_viewer );
		}
		$this->globals['my_doc_viewer'] = $my_doc_viewer;
	}

	/**
	 * Inits the `login_page` parameter.
	 *
	 * **************************************************************************************
	 * login_page parameter defines the login page that user will be redirected -if need it-,
	 * to log in. Accepts `wp-login.php` or any slug. Default is `wp-login.php`.
	 * DON'S USE SLUG WITH GET PARAMETERS PLEASE.
	 *
	 * LOGIN_PAGE PARAMETER USE CASES
	 *
	 * If have_file_access is NOT in the DEFAULT state and you had set your custom login page,
	 * you can define it in this parameter.
	 *
	 * - login_page="wp-login.php" : the user will be redirected in the default WP login page
	 *   to log in.
	 * - login_page="login" : if your login page is located in the link
	 *   yoursite.com/login/
	 * - login_page="my-account" : if your login page is located in the link
	 *   yoursite.com/my-account/
	 * - login_page="pages/login" : if your login page is located in the link
	 *   yoursite.com/pages/login/
	 * **************************************************************************************
	 *
	 * @since 2.0.0
	 * @param array $parameters The shortcode parameters.
	 */
	protected function init_parameter_login_page($parameters)
	{
		$this->globals['login_page'] = !empty($parameters['login_page']) ? $parameters['login_page'] : $this->globals['login_page'];
	}

	/**
	 * Inits the `current_viewer` global variable.
	 *
	 * @since 2.0.0
	 * @since 2.0.3 Bug Fix! The corresponding global is set now
	 */
	protected function Library_Viewer_Shortcode__init_globals_before_init_parameters()
	{
		$this->globals['current_viewer'] = 'folder';
	}

	/**
	 * Inits the `current_page_url` global variable.
	 *
	 * @since 3.0.0
	 */
	protected function init_global_abspath()
	{
		$this->globals['abspath'] = ABSPATH;
	}

	/**
	 * Inits the `current_page_url` global variable.
	 *
	 * @since 2.0.0
	 */
	protected function init_global_current_page_url()
	{
		$this->globals['current_page_url'] = $this->get_current_page_url();
	}

	/**
	 * Inits the global `shortcode_page_link` variable.
	 *
	 * @since 2.0.10
	 */
	protected function init_global_real_shortcode_page_link()
	{
		$this->globals['real_shortcode_page_link'] = $this->get_current_page_url();
	}

	public function get_current_page_url($url_suffix = '')
	{
		$protocol = explode('://', site_url());
		$protocol = $protocol[0];
		$current_page_url = $protocol . '://' . $_SERVER['HTTP_HOST'] . parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
		$current_page_url = $this->rtrim($current_page_url, '/');

		$current_page_url = str_replace('+', '%2B', $current_page_url);
		$current_page_url = urldecode($current_page_url);

		if($url_suffix){
			$current_page_url = $current_page_url . $url_suffix;
		}

		return $current_page_url;
	}


	/**
	 * Inits the `file_identifier` global variable.
	 *
	 * @since 2.0.0
	 * @since 3.0.0 Init by applying filters, if not exist yet.
	 */
	public function init_global_file_identifier()
	{
		/**
		 * @ignore
		 */
		$this->globals['file_identifier'] = $this->globals['file_identifier'] ?? apply_filters('lv_file_identifier', 'LV');
	}

	/**
	 * Inits the `folder_fake_path_symbols` global variable.
	 *
	 * @since 2.0.0
	 */
	protected function init_global_folder_fake_path_symbols()
	{
		/**
		 * Folder fake path symbols filter.
		 *
		 * Folder fake path is the path of folder
		 * that user sees in the front-end.
		 * With this filter, we can change the symbols of the
		 * fake path of folders that user sees in the front-end.
		 * So, these symbols will replace the `$folder_real_path_symbols`.
		 *
		 * @since 2.0.0
		 *
		 * @param array $lv_folder_fake_path_symbols The array of folder fake path's symbols.
		 * @param array $this->globals See property's documentation.
		 */
		$this->globals['folder_fake_path_symbols'] = apply_filters('lv_folder_fake_path_symbols', ['–', '＆', '┼', '·', '΄', '%23'], $this->globals);
	}

	/**
	 * Inits the `folder_real_path_symbols` global variable.
	 *
	 * @since 2.0.0
	 */
	protected function init_global_folder_real_path_symbols()
	{
		/**
		 * Folder real(relative) path symbols filter.
		 *
		 * Folder real path is the relative path of folder,
		 * that user doesn't see in the front-end.
		 * With this filter, we can change the symbols of the
		 * real(relative) path of folders that user doesn't see in the front-end.
		 * So, these symbols will replace the `$folder_fake_path_symbols`.
		 *
		 * @since 2.0.0
		 *
		 * @param array $folder_real_path_symbols The array of folder real path's symbols.
		 * @param array $this->globals See property's documentation.
		 */
		$this->globals['folder_real_path_symbols'] = apply_filters('lv_folder_real_path_symbols', [' ', '&', '+', '.', "'", '#'], $this->globals);
	}

	/**
	 * Inits the `file_fake_path_symbols` global variable.
	 *
	 * @since 2.0.0
	 */
	protected function init_global_file_fake_path_symbols()
	{
		/**
		 * File fake path symbols filter.
		 *
		 * File fake path is the path of file
		 * that user sees in the front-end.
		 * With this filter, we can change the symbols of the
		 * fake path of files, that user sees in the front-end.
		 * So, these symbols will replace the `$file_real_path_symbols`.
		 *
		 * @since 2.0.0
		 *
		 * @param array $lv_folder_fake_path_symbols The array of folder fake path's symbols
		 * @param array $this->globals See property's documentation.
		 */
		$this->globals['file_fake_path_symbols'] = apply_filters('lv_file_fake_path_symbols', ['–', '΄', '%23', '%3F'], $this->globals);
	}

	/**
	 * Inits the `file_real_path_symbols` global variable.
	 *
	 * @since 2.0.0
	 */
	protected function init_global_file_real_path_symbols()
	{
		/**
		 * File real(relative) path symbols filter.
		 *
		 * File real path is the relative path of file,
		 * that user doesn't see in the front-end.
		 * With this filter, we can change the symbols of the
		 * real(relative) path of files that user doesn't see in the front-end.
		 * So, these symbols will replace the `$file_fake_path_symbols`.
		 *
		 * @since 2.0.0
		 *
		 * @param array $file_real_path_symbols The array of file real path's symbols.
		 * @param array $this->globals See property's documentation.
		 */
		$this->globals['file_real_path_symbols'] = apply_filters( 'lv_file_real_path_symbols', [' ', "'", '#', '?'], $this->globals );
	}

	/**
	 * Inits `dir` global variable.
	 *
	 * @since 2.0.0
	 */
	protected function init_global_dir()
	{
		$dir_name = $this->get_dir_name();

		$this->globals['dir'] = !empty( $_GET[$dir_name] )
			? $this->trim( $_GET[$dir_name] , '/')
			: $this->globals['path'];
	}

	/**
	 * Inits real path `global` variable.
	 *
	 * @since 2.0.0
	 */
	protected function init_global_real_path()
	{
		$this->globals['real_path'] = $this->get_folder_real_link( $this->globals[ $this->get_dir_name() ] ) . '/';
	}

	/**
	 * Inits have_folder_access `global` variable.
	 *
	 * @since 2.0.7
	 */
	protected function init_global_have_folder_access()
	{
		$this->globals['have_folder_access'] = ['all'];
	}

	/**
	 * Get breadcrumb items public method.
	 *
	 * @since 3.0.0
	 *
	 * @param bool $suppress_filters Default is false
	 *
	 * @return array $breadcrumb_items {
	 *        An indexed array containing all breadcrumb items in the trail.
	 *        Each item is an associative array representing a single breadcrumb step.
	 *
	 *        @type array $item {
	 *            A single breadcrumb item containing relevant folder details.
	 *
	 *            @type string $folder_name The folder name as link text.
	 *            @type string $folder_abs_path The absolute path of the folder.
	 *            @type string $folder_fake_link The folder fake link as link URL.
	 *        }
	 * }
	 */
	public function get_breadcrumb_items($suppress_filters = false)
	{
		extract($this->globals);

		if ( '' === $real_path ) {
			$url_parts = array();
		} else {
			$url_parts = explode( '/', trim( $this->get_encrypted_path( $real_path ), '/') );
		}

		$breadcrumb_items = array();

		foreach ($url_parts as $key => $url_part) {
			$folder_link = '';
			for ($i=0; $i<=$key; $i++) {
				$folder_link .= $this->get_folder_fake_link( $url_parts[$i] ) . '/';
			}
			$folder_link = trim($folder_link, '/');

			$breadcrumb_items[$key]['folder_name'] = $this->get_folder_name($folder_link);
			$breadcrumb_items[$key]['folder_abs_path'] = $abspath . $this->get_folder_real_link($folder_link) . '/';
			$breadcrumb_items[$key]['folder_fake_link'] = $folder_link;
		}

		if( !$suppress_filters ){
			/**
			 * Breadcrumb items filter.
			 *
			 * With this filter, you can alter the breadcrumb items,
			 * for example the folder name and folder fake link.
			 *
			 * @since 2.0.0
			 *
			 * @param array $breadcrumb_items {
			 * 		An indexed array containing all breadcrumb items in the trail.
			 * 		Each item is an associative array representing a single breadcrumb step.
			 *
			 * 		@type array $item {
			 * 			A single breadcrumb item containing relevant folder details.
			 *
			 * 			@type string $folder_name The folder name as link text.
			 * 			@type string $folder_abs_path The absolute path of the folder.
			 * 			@type string $folder_fake_link The folder fake link as link URL.
			 * 		}
			 * }
			 * @param array $this->globals See property's documentation.
			 */
			$breadcrumb_items = apply_filters('lv_breadcrumb_items', $breadcrumb_items, $this->globals);
		}

		return $breadcrumb_items;
	}

	/**
	 * Get breadcrumb items public method.
	 *
	 * @since 3.0.0
	 *
	 * @return array $folder {
	 * 		The folder array with its details.
	 *
	 *      @type string $folder_name The folder name as link text.
	 *      @type string $folder_abs_path The absolute path of the folder.
	 *      @type string $folder_fake_link The folder fake link as link URL.
	 * }
	 */
	public function get_current_folder()
	{
		extract($this->globals);

		if ( '' === $real_path ) {
			$url_parts = array();
		} else {
			$url_parts = explode( '/', trim( $this->get_encrypted_path( $real_path ), '/') );
		}


		$key = count($url_parts) - 1;
		$folder_link = '';
		for ($i=0; $i<=$key; $i++) {
			$folder_link .= $this->get_folder_fake_link( $url_parts[$i] ) . '/';
		}
		$folder_link = trim($folder_link, '/');

		return [
			'folder_name'		=> $this->get_folder_name($folder_link),
			'folder_abs_path'	=> $abspath . $this->get_folder_real_link($folder_link) . '/',
			'folder_fake_link'	=> $folder_link
		];

	}

	/**
	 * Prints the breadcrumb.
	 *
	 * @since 2.0.0
	 */
	protected function print_breadcrumb()
	{
		extract($this->globals);

		/**
		 * Action: lv_before_breadcrumb_start
		 *
		 * With this action, you can echo any content you want before
		 * opening of div with class library-viewer--breadcrumb.
		 *
		 * @since 2.0.0
		 * @since 3.0.0 Deprecated
		 *
		 * @deprecated
		 *
		 * @param array $this->globals See property's documentation.
		 */
		$deprecated_args = [$this->globals];
		do_action_deprecated('lv_before_breadcrumb_start', $deprecated_args, '3.0.0', 'filter lv_breadcrumb_html');


		ob_start();

		echo '<div class="library-viewer--breadcrumb">';

		/**
		 * Action: lv_after_breadcrumb_start
		 *
		 * With this action, you can echo any content you want after
		 * opening of div with class library-viewer--breadcrumb.
		 *
		 * @since 2.0.0
		 * @since 3.0.0 Deprecated
		 *
		 * @deprecated
		 *
		 * @param array $this->globals See property's documentation.
		 */
		$deprecated_args = [$this->globals];
		do_action_deprecated('lv_after_breadcrumb_start', $deprecated_args, '3.0.0', 'filter lv_breadcrumb_html');

		$delimiter_html = '<span class="library-viewer--breadcrumb_delimiter">/</span>';

		/**
		 * Breadcrumb folder delimiter html filter.
		 *
		 * With this filter, you can change the html of the breadcrumb delimiter.
		 *
		 * @since 2.0.0
		 *
		 * @param string $delimiter_html The html of breadcrumb delimiter.
		 * @param array $this->globals See property's documentation.
		 */
		$delimiter = apply_filters('lv_breadcrumb_folder_delimiter_html', $delimiter_html, $this->globals);

		$breadcrumb_items = $this->get_breadcrumb_items();

		foreach ($breadcrumb_items as $item) {
			echo $delimiter;

			$breadcrumb_item_html = '<a class="library-viewer--breadcrumb-item" href="?' . $this->http_build_query($item['folder_fake_link']) . '">' . $item['folder_name'] . '</a>';

			/**
			 * Breadcrumb folder delimiter html filter.
			 *
			 * With this filter, you can change the html of the breadcrumb delimiter.
			 *
			 * @since 3.0.0
			 *
			 * @param string $breadcrumb_item_html The html of breadcrumb item.
			 * @param array $item {
			 * 		The breadcrumb item.
			 *
			 * 		@type string $folder_name The folder name as link text.
			 * 		@type string $folder_abs_path The absolute path of the folder.
			 * 		@type string $folder_fake_link The folder fake link as link URL.
			 * }
			 * @param array $this->globals See property's documentation.
			 */
			$breadcrumb_item_html = apply_filters('lv_breadcrumb_item_html', $breadcrumb_item_html, $item, $this->globals);

			echo $breadcrumb_item_html;
		}

		/**
		 * Action: lv_before_breadcrumb_end
		 *
		 * With this action, you can echo any content you want before
		 * closing of div with class library-viewer--breadcrumb.
		 *
		 * @since 2.0.0
		 * @since 3.0.0 Deprecated
		 *
		 * @deprecated
		 *
		 * @param array $this->globals See property's documentation.
		 */
		$deprecated_args = [$this->globals];
		do_action_deprecated('lv_before_breadcrumb_end', $deprecated_args, '3.0.0', 'filter lv_breadcrumb_html');

		echo '</div>';

		$breadcrumb_html = ob_get_clean();

		/**
		 * The breadcrumb html filter.
		 *
		 * With this filter, you can filter the html of breadcrumb.
		 * Contains the div with class `library-viewer--breadcrumb`.
		 *
		 * @since 2.0.3
		 *
		 * @param string $breadcrumb_html The html of the breadcrumb.
		 * @param $breadcrumb_items {
		 * 		The items of breadcrumb, separated with delimiter(/).
		 *
		 * 		@type string $folder_name The folder name as link text.
		 * 		@type string $folder_abs_path The absolute path of the folder.
		 * 		@type string $folder_fake_link The folder fake link as link URL.
		 * }
		 * @param array $this->globals See property's documentation.
		 */
		$breadcrumb_html = apply_filters('lv_breadcrumb_html', $breadcrumb_html, $breadcrumb_items, $this->globals);

		echo $breadcrumb_html;

		/**
		 * Action: lv_after_breadcrumb_end
		 *
		 * With this action, you can echo any content you want after
		 * closing of div with class library-viewer--breadcrumb.
		 *
		 * @since 2.0.0
		 * @since 3.0.0 Deprecated
		 *
		 * @deprecated
		 *
		 * @param array $this->globals See property's documentation.
		 */
		$deprecated_args = [$this->globals];
		do_action_deprecated('lv_after_breadcrumb_end', $deprecated_args, '3.0.0', 'filter lv_breadcrumb_html');
	}

	/**
	 * Prints folder's contents method.
	 *
	 * This method prints all th contents of the current folder,
	 * breadcrumb, containing folders and files, text at beginning, text at end.
	 *
	 * @since 2.0.0
	 */
	protected function print_folder_contents()
	{
		$this->print_folder_text_at_beginning();

		echo $this->get_create_actions_html();

		$this->print_containing_folders();

		$this->print_containing_files();

		if ( 0 === $this->all_folders_counter + $this->all_files_counter ) {
			$empty_folder_html = '<span class="library-viewer--empty_folder">' . library_viewer_error('empty_folder') . '</span>';

			/**
			 * Empty folder html message.
			 *
			 * If the current folder contains neither files nor folders,
			 * an equivalent message will be displayed an with filter.
			 * With this filter you can change it.
			 *
			 * @since 2.0.0
			 *
			 * @param string $empty_folder_html The html of the empty folder message.
			 * @param array $this->globals See property's documentation.
			 */
			$empty_folder_html = apply_filters('lv_empty_folder_html', $empty_folder_html, $this->globals);
			echo $empty_folder_html;
		}

		$this->print_folder_text_at_end();
	}

	/**
	 * Folder text at beginning method.
	 *
	 * This method prints the message at the
	 * beginning of current folder's contents,
	 * before the first containing folder.
	 *
	 * @since 2.0.0
	 */
	protected function print_folder_text_at_beginning()
	{
		$real_path = $this->globals['real_path'];

		$include_directory = $real_path . 'include.php';

		$text_at_beginning = '';

		if ( file_exists($include_directory) ) {
			include $include_directory;
		}

		/**
		 * Folder text at beginning filter.
		 *
		 * This filter allow us to add or change the text at beginning of the folder,
		 * i.e. the text before the first containing folder.
		 *
		 * THE OTHER WAY to add text at beginning is
		 * adding the php file include.php with a variable $text_at_beginning.
		 *
		 * @since 2.0.0
		 *
		 * @param string $text_at_beginning The beginning text.
		 * @param array $this->globals See property's documentation.
		 */
		$text_at_beginning = apply_filters('lv_folder_text_at_beginning', $text_at_beginning, $this->globals);

		if ( '' != $text_at_beginning ) {
			echo '<div class="library-viewer--beginning-text">';
			echo $text_at_beginning;
			echo '</div>';
		}
	}

	/**
	 * Prints the containing folders of asked directory.
	 *
	 * @since 2.0.0
	 */
	protected function print_containing_folders()
	{
		extract($this->globals);

		//Echo all the containing directories except...
		//...all the FTP folders that have in their names the string "hidden-folder"
		$all_folders = array_filter(glob( $real_path . '*' ), 'is_dir');
		natsort($all_folders); //natural sorting

		$_all_folders = array();

		foreach ($all_folders as $folder_real_link) {

			if ( $this->is_folder_hidden($folder_real_link) ) {
				continue;
			}

			$folder_fake_link = $this->get_folder_fake_link($folder_real_link);
			$folder_name	  = $this->get_folder_name($folder_fake_link);
			$folder_abs_path  = $abspath . $folder_real_link . '/';

			$_all_folders[] = array(
				'folder_name' 	   => $folder_name,
				'folder_fake_link' => $folder_fake_link,
				'folder_real_link' => $folder_real_link . '/',
				'folder_abs_path'  => $folder_abs_path,
			);

		}

		$all_folders = $_all_folders;

		/**
		 * Containing folders of current folder filter.
		 *
		 * With this filter, we can filter the folders that will be
		 * displayed in the library (front-end), their name, URL or order.
		 * Folders that are contained in the current folder ($this->real_path).
		 *
		 * @since 2.0.0
		 *
		 * @param array $all_folders {
		 *		All folders.
		 *
		 * 		@type array $key  {
		 * 			The folder's array.
		 *
		 * 			@type string $folder_name Folder's name.
		 * 			@type string $folder_fake_link Folder's fake link.
		 * 			@type string $folder_real_link Folder's real (relative) link.
		 * 			@type string $folder_abs_path The folder's absolute path.
		 * 			@type string $folder_icon_html If exists, replaces the default folder icon.
		 * 		}
		 * }
		 */
		$all_folders = apply_filters('lv_containing_folders', $all_folders, $this->globals);

		$default_folder_icon_html = '<span class="library-viewer--folder__icon" style="background-image:url('. LIBRARY_VIEWER_PLUGIN_DIR_URL .'assets/folder-icon.png)"></span>'; //FROM 1.0.6

		/**
		 * Folder icon html filter.
		 *
		 * With this filter we can change the folder icon that is printed
		 * before the folder in the Library in the front-end.
		 * Before 2.0.0, we could change the folder icon using only css
		 * (background-image attribute).
		 * Now, we can set the folder icon with the php filter `lv_folder_icon_html`.
		 *
		 * 		Example:
		 * 		add_filter('lv_folder_icon_html', function($html){
		 * 			$html = '<svg version="1.1" id="Capa_1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" x="0px" y="0px" width="45px" height="45px" viewBox="0 0 45 45" style="enable-background:new 0 0 45 45;" xml:space="preserve"> <g> <path d="M44.45,13.436c-0.474-0.591-1.192-0.936-1.95-0.936H40c0-1.381-1.119-2.5-2.5-2.5H35V7.5C35,6.119,33.881,5,32.5,5h-30 C1.119,5,0,6.119,0,7.5v30C0,38.881,1.119,40,2.5,40h5h25h5c1.172,0,2.188-0.814,2.439-1.958l5-22.5 C45.105,14.802,44.925,14.027,44.45,13.436z M2.5,7.5h30V10H30c-1.381,0-2.5,1.119-2.5,2.5h-15c-1.172,0-2.186,0.814-2.44,1.958 c0,0-5.058,22.862-5.058,23.042H2.5V7.5L2.5,7.5z"/> </g> <g> </g> <g> </g> <g> </g> <g> </g> <g> </g> <g> </g> <g> </g> <g> </g> <g> </g> <g> </g> <g> </g> <g> </g> <g> </g> <g> </g> <g> </g> </svg>';
		 * 			//instead of span with background image
		 * 			return $html;
		 *		});
		 *
		 * @since 2.0.0
		 * @param string $default_folder_icon_html The html of the default folder icon.
		 * @param array $this->globals See property's documentation.
		 */
		$default_folder_icon_html = apply_filters('lv_folder_icon_html', $default_folder_icon_html, $this->globals);

		foreach ($all_folders as $folder) {
			$folder_fake_link		 = $folder['folder_fake_link'];
			$folder_name			 = $folder['folder_name'];

			$folder_html  = '<div class="library-viewer--folder">';
			$folder_html .= '<h3 class="entry-title"><a href="?' . $this->http_build_query($folder_fake_link) . '">';

			$folder_html .= $folder['folder_icon_html'] ?? $default_folder_icon_html;

			$folder_html .= $folder_name;
			$folder_html .= '</a></h3>';
			$folder_html .= $this->get_folder_actions_html($folder);
			$folder_html .= '</div>';

			/**
			 * Folder HTML filter.
			 *
			 * Used to filter the html output of printed folder.
			 *
			 * @since 2.0.0
			 *
			 * @param string $folder_html The html output of printed folder.
			 * @param array $folder {
			 * 		Details of the printed folder.
			 *
			 * 		@type string $folder_name Folder's name.
			 * 		@type string $folder_fake_link Folder's fake link.
			 * 		@type string $folder_real_link Folder's real (relative) link.
			 *		                               (Used nowhere, just an extra parameter in the filter)
			 * 		@type string $folder_abs_path The folder's absolute path.
			 * 		                              (Used nowhere, just an extra parameter in the filter)
			 * }
			 * @param array $this->globals See property's documentation.
			 */
			$folder_html  = apply_filters('lv_folder_html', $folder_html, $folder, $this->globals);

			/**
			 * Before folder html action.
			 *
			 * Print any html you want before opening of
			 * div with class library-viewer--folder.
			 *
			 * @since 2.0.0
			 *
			 * @param string $folder_html The html output of printed folder.
			 * @param array $folder {
			 * 		Details of the printed folder.
			 *
			 * 		@type string $folder_name Folder's name.
			 * 		@type string $folder_fake_link Folder's fake link.
			 * 		@type string $folder_real_link Folder's real (relative) link.
			 *		                               (Used nowhere, just an extra parameter in the filter)
			 * 		@type string $folder_abs_path The folder's absolute path.
			 * 		                              (Used nowhere, just an extra parameter in the filter)
			 * }
			 * @param array $this->globals See property's documentation.
			 */
			do_action('lv_before_folder', $folder_html, $folder, $this->globals);
			echo $folder_html;

			/**
			 * After folder html action.
			 *
			 * Print any html you want after closing of
			 * div with class library-viewer--folder.
			 *
			 * @since 2.0.0
			 *
			 * @param string $folder_html The html output of printed folder.
			 * @param array $folder {
			 * 		Details of the printed folder.
			 *
			 * 		@type string $folder_name Folder's name.
			 * 		@type string $folder_fake_link Folder's fake link.
			 * 		@type string $folder_real_link Folder's real (relative) link.
			 *		                               (Used nowhere, just an extra parameter in the filter)
			 * 		@type string $folder_abs_path The folder's absolute path.
			 * 		                              (Used nowhere, just an extra parameter in the filter)
			 * }
			 * @param array $this->globals See property's documentation.
			 */
			do_action('lv_after_folder', $folder_html, $folder, $this->globals);
		}

		$this->all_folders_counter = count($all_folders);

	}

	/**
	 * Prints the containing files of asked directory.
	 *
	 * @since 2.0.0
	 */
	protected function print_containing_files()
	{
		extract($this->globals);

		//All the files (pdf, jpg, png ,doc, php, ini etc. etc.)...
		//...except these that have in their names the string "hidden-file" or they are .php or .ini files
		$all_files = array_filter(glob( $real_path .'*'), 'is_file');
		natsort($all_files); //natural sorting

		$_all_files = array();

		foreach ($all_files as $key => $file) {

			$file_name = $this->basename($file);
			if ( $this->is_file_hidden($file_name) ) {
				continue;
			}

			$file_fake_link = $this->get_file_fake_link($file);

			$_all_files[] = array(
				'file_name'		 => $file_name,
				'file_fake_link' => $file_fake_link,
				'file_real_link' => $file,
				'file_full_url'  => site_url($file),
				'file_abs_path'	 => $abspath . $real_path . $file_name,
			);
		}

		$all_files = $_all_files;
		foreach ($all_files as $key => $file){
			$all_files[$key]['file_viewer'] = $my_doc_viewer;
		}

		/**
		 * Containing files of current folder filter.
		 *
		 * With this filter, we can filter the files that will be
		 * displayed in the library (front-end), their name, URL or order.
		 * Files that are contained in the current folder ($this->real_path).
		 *
		 * @since 2.0.0
		 * @since 2.0.3 $file_abs_path is added in the $all_files parameter ( $all_files['file_abs_path'] ).
		 *
		 * @param array $all_files {
		 *		All files.
		 *
		 * 		@type array $key {
		 * 			The file's array.
		 *
		 * 			@type string $file_name File's name.
		 * 			@type string $file_fake_link File's fake link.
		 * 			@type string $file_real_link File's real (relative) link.
		 * 			@type string $file_full_url File's full URL (with site_url as prefix).
		 * 			@type string $file_abs_path File's absolute URL.
		 * 			@type string $file_icon_html If exists, replaces the default file icon.
		 * 		}
		 * }
		 * @param array $this->globals See property's documentation.
		 */
		$all_files = apply_filters('lv_containing_files', $all_files, $this->globals);

		/**
		 * Save shortcode parameters in db, if need it.
		 */
		foreach ($all_files as $file) {
			if ( 'library-viewer' == $file['file_viewer'] ) {
				$this->save_shortcode_parameters_in_db();
				break;
			}
		}

		foreach ($all_files as $file) {
			$file_real_link		   = $file['file_real_link'];
			$file_fake_link 	   = $file['file_fake_link'];
			$file_name 			   = $file['file_name'];

			$file_html  = '<div class="library-viewer--file">';

			$extension = pathinfo($file_real_link,PATHINFO_EXTENSION);

			$default_file_icon_html = '<span class="library-viewer--file__icon library-viewer--'. $extension .'__icon"></span>'; //FROM 1.1.0

			/**
			 * File icon html filter.
			 *
			 * With this filter, we can change the html (the span html element)
			 * before the file in the Library in the front-end.
			 * Before 2.0.0, we could set an icon for files using only css
			 * (width, height & background-image attributes).
			 * Now, we can set the file icon also with the php filter `lv_file_icon_html`.
			 *
			 * 		Example:
			 * 		add_filter('lv_file_icon_html', function($html, $extension){
			 * 			if ( 'pdf' == $extension ) {
			 * 				$html = '<svg style="width: 25px;" version="1.1" id="Capa_1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" x="0px" y="0px" viewBox="0 0 56 56" style="enable-background:new 0 0 56 56;" xml:space="preserve"> <g> <path style="fill:#E9E9E0;" d="M36.985,0H7.963C7.155,0,6.5,0.655,6.5,1.926V55c0,0.345,0.655,1,1.463,1h40.074 c0.808,0,1.463-0.655,1.463-1V12.978c0-0.696-0.093-0.92-0.257-1.085L37.607,0.257C37.442,0.093,37.218,0,36.985,0z"/> <polygon style="fill:#D9D7CA;" points="37.5,0.151 37.5,12 49.349,12 	"/> <path style="fill:#CC4B4C;" d="M19.514,33.324L19.514,33.324c-0.348,0-0.682-0.113-0.967-0.326 c-1.041-0.781-1.181-1.65-1.115-2.242c0.182-1.628,2.195-3.332,5.985-5.068c1.504-3.296,2.935-7.357,3.788-10.75 c-0.998-2.172-1.968-4.99-1.261-6.643c0.248-0.579,0.557-1.023,1.134-1.215c0.228-0.076,0.804-0.172,1.016-0.172 c0.504,0,0.947,0.649,1.261,1.049c0.295,0.376,0.964,1.173-0.373,6.802c1.348,2.784,3.258,5.62,5.088,7.562 c1.311-0.237,2.439-0.358,3.358-0.358c1.566,0,2.515,0.365,2.902,1.117c0.32,0.622,0.189,1.349-0.39,2.16 c-0.557,0.779-1.325,1.191-2.22,1.191c-1.216,0-2.632-0.768-4.211-2.285c-2.837,0.593-6.15,1.651-8.828,2.822 c-0.836,1.774-1.637,3.203-2.383,4.251C21.273,32.654,20.389,33.324,19.514,33.324z M22.176,28.198 c-2.137,1.201-3.008,2.188-3.071,2.744c-0.01,0.092-0.037,0.334,0.431,0.692C19.685,31.587,20.555,31.19,22.176,28.198z M35.813,23.756c0.815,0.627,1.014,0.944,1.547,0.944c0.234,0,0.901-0.01,1.21-0.441c0.149-0.209,0.207-0.343,0.23-0.415 c-0.123-0.065-0.286-0.197-1.175-0.197C37.12,23.648,36.485,23.67,35.813,23.756z M28.343,17.174 c-0.715,2.474-1.659,5.145-2.674,7.564c2.09-0.811,4.362-1.519,6.496-2.02C30.815,21.15,29.466,19.192,28.343,17.174z M27.736,8.712c-0.098,0.033-1.33,1.757,0.096,3.216C28.781,9.813,27.779,8.698,27.736,8.712z"/> <path style="fill:#CC4B4C;" d="M48.037,56H7.963C7.155,56,6.5,55.345,6.5,54.537V39h43v15.537C49.5,55.345,48.845,56,48.037,56z"/> <g> <path style="fill:#FFFFFF;" d="M17.385,53h-1.641V42.924h2.898c0.428,0,0.852,0.068,1.271,0.205 c0.419,0.137,0.795,0.342,1.128,0.615c0.333,0.273,0.602,0.604,0.807,0.991s0.308,0.822,0.308,1.306 c0,0.511-0.087,0.973-0.26,1.388c-0.173,0.415-0.415,0.764-0.725,1.046c-0.31,0.282-0.684,0.501-1.121,0.656 s-0.921,0.232-1.449,0.232h-1.217V53z M17.385,44.168v3.992h1.504c0.2,0,0.398-0.034,0.595-0.103 c0.196-0.068,0.376-0.18,0.54-0.335c0.164-0.155,0.296-0.371,0.396-0.649c0.1-0.278,0.15-0.622,0.15-1.032 c0-0.164-0.023-0.354-0.068-0.567c-0.046-0.214-0.139-0.419-0.28-0.615c-0.142-0.196-0.34-0.36-0.595-0.492 c-0.255-0.132-0.593-0.198-1.012-0.198H17.385z"/> <path style="fill:#FFFFFF;" d="M32.219,47.682c0,0.829-0.089,1.538-0.267,2.126s-0.403,1.08-0.677,1.477s-0.581,0.709-0.923,0.937 s-0.672,0.398-0.991,0.513c-0.319,0.114-0.611,0.187-0.875,0.219C28.222,52.984,28.026,53,27.898,53h-3.814V42.924h3.035 c0.848,0,1.593,0.135,2.235,0.403s1.176,0.627,1.6,1.073s0.74,0.955,0.95,1.524C32.114,46.494,32.219,47.08,32.219,47.682z M27.352,51.797c1.112,0,1.914-0.355,2.406-1.066s0.738-1.741,0.738-3.09c0-0.419-0.05-0.834-0.15-1.244 c-0.101-0.41-0.294-0.781-0.581-1.114s-0.677-0.602-1.169-0.807s-1.13-0.308-1.914-0.308h-0.957v7.629H27.352z"/> <path style="fill:#FFFFFF;" d="M36.266,44.168v3.172h4.211v1.121h-4.211V53h-1.668V42.924H40.9v1.244H36.266z"/> </g> </g> <g> </g> <g> </g> <g> </g> <g> </g> <g> </g> <g> </g> <g> </g> <g> </g> <g> </g> <g> </g> <g> </g> <g> </g> <g> </g> <g> </g> <g> </g> </svg>';
			 * 				//instead of span with background image
			 * 			}
			 *			return $html;
			 *		}, 10, 2);
			 *
			 * @since 2.0.0
			 * @since 2.0.3 $file_abs_path is added in the $all_files parameter ( $all_files['file_abs_path'] ).
			 *
			 * @param string $icon_html The html of the file icon.
			 * @param string $extension The extension of the current file (pdf, png etc).
			 * @param array $file {
			 * 		Details of the printed file.
			 *
			 * 		@type string $file_name File's name.
			 * 		@type string $file_fake_link File's fake link.
			 * 		@type string $file_real_link File's real (relative) link.
			 * 		@type string $file_full_url File's full URL (with site_url as prefix).
			 * 		@type string $file_abs_path File's absolute URL.
			 * }
			 * @param array $this->globals See property's documentation.
			 */
			$default_file_icon_html = apply_filters('lv_file_icon_html', $default_file_icon_html, $extension, $file, $this->globals);

			$file_icon_html = $file['file_icon_html'] ?? $default_file_icon_html;

			$file_html .= $this->get_file_anchor_html($file, $file_icon_html);
			$file_html .= $this->get_file_actions_html($file);
			$file_html .= '</div>';

			/**
			 * File HTML filter.
			 *
			 * Used to filter the html output of printed file.
			 *
			 * @since 2.0.0
			 * @since 2.0.3 $file_abs_path is added in the $all_files parameter ( $all_files['file_abs_path'] ).
			 *
			 * @param string $file_html The html output of printed file.
			 * @param array $file {
			 * 		Details of the printed file.
			 *
			 * 		@type string $file_name File's name.
			 * 		@type string $file_fake_link File's fake link.
			 * 		@type string $file_real_link File's real (relative) link.
			 * 		@type string $file_full_url File's full URL (with site_url as prefix).
			 * 		@type string $file_abs_path File's absolute URL.
			 * }
			 * @param array $this->globals See property's documentation.
			 */
			$file_html  = apply_filters('lv_file_html', $file_html, $file, $this->globals);

			/**
			 * Before file html action.
			 *
			 * Print any html you want before opening of
			 * div with class library-viewer--file.
			 *
			 * @since 2.0.0
			 * @since 2.0.3 $file_abs_path is added in the $all_files parameter ( $all_files['file_abs_path'] ).
			 *
			 * @param string $file_html The html output of printed file.
			 * @param array $file {
			 * 		Details of the printed file.
			 *
			 * 		@type string $file_name File's name.
			 * 		@type string $file_fake_link File's fake link.
			 * 		@type string $file_real_link File's real (relative) link.
			 * 		@type string $file_full_url File's full URL (with site_url as prefix).
			 * 		@type string $file_abs_path File's absolute URL.
			 * }
			 * @param array $this->globals See property's documentation.
			 */
			do_action('lv_before_file', $file_html, $file, $this->globals);

			echo $file_html;

			/**
			 * After file html action.
			 *
			 * Print any html you want after closing of
			 * div with class library-viewer--file.
			 *
			 * @since 2.0.0
			 * @since 2.0.3 $file_abs_path is added in the $all_files parameter ( $all_files['file_abs_path'] ).
			 *
			 * @param string $file_html The html output of printed file.
			 * @param array $file {
			 * 		Details of the printed file.
			 *
			 * 		@type string $file_name File's name.
			 * 		@type string $file_fake_link File's fake link.
			 * 		@type string $file_real_link File's real (relative) link.
			 * 		@type string $file_full_url File's full URL (with site_url as prefix).
			 * 		@type string $file_abs_path File's absolute URL.
			 * }
			 * @param array $this->globals See property's documentation.
			 */
			do_action('lv_after_file', $file_html, $file, $this->globals);

		}

		$this->all_files_counter = count($all_files);

	}

	/**
	 * Folder text at end method.
	 *
	 * This method prints the message at the
	 * end of current folder's contents,
	 * after the last containing file.
	 *
	 * @since 2.0.0
	 */
	protected function print_folder_text_at_end()
	{
		$real_path = $this->globals['real_path'];

		$include_directory = $real_path . 'include.php';

		$text_at_end = '';

		if ( file_exists($include_directory) ) {
			include $include_directory;
		}

		/**
		 * Folder text at end filter.
		 *
		 * This filter allow us to add or change the text at end of the files,
		 * i.e. the text after the last containing file.
		 *
		 * THE OTHER WAY to add text at end is
		 * adding the php file include.php with a variable $text_at_end.
		 *
		 * @since 2.0.0
		 *
		 * @param string $text_at_end The ending text.
		 * @param array $this->globals See property's documentation.
		 */
		$text_at_end = apply_filters('lv_folder_text_at_end', $text_at_end, $this->globals);

		if ( '' != $text_at_end ) {
			echo '<div class="library-viewer--end-text">';
			echo $text_at_end;
			echo '</div>';
		}
	}

	/**
	 * Returns the html that will be displayed in the shortcode call.
	 *
	 * @since 2.0.0
	 * @since 3.0.0 param $attributes added
	 *
	 * @param array $attributes
	 * @return string
	 */
	public function shortcode_html_contents($attributes)
	{
		$this->init($attributes);
		extract($this->globals);

		$this->enqueue_styles_and_scripts(true);

		ob_start();

		if ( $this->errors_exists_in_current_folder() ) {

			$display_errors = $this->display_errors;

			/**
			 * Display errors filter.
			 *
			 * This filter allow us to filter the error display message.
			 *
			 * @param string $display_errors The error message to display.
			 * @param array $this->globals See property's documentation.
			 *
			 * @since 2.0.8
			 *
			 */
			$display_errors = apply_filters('lv_display_errors', $display_errors, $this->globals);

			echo $display_errors;

		} else {

			$folder_abs_path = $abspath . $real_path;

			/**
			 * Folder was viewed action.
			 *
			 * Do some actions if a folder was accessed/viewed.
			 *
			 * @since 2.0.0
			 *
			 * @param string $real_path The real (relative) path of current folder.
			 * @param string $folder_abs_path The absolute path of current folder.
			 * @param array $this->globals See property's documentation.
			 */
			do_action('lv_folder_was_viewed', $real_path, $folder_abs_path, $this->globals);

			echo '<div class="library-viewer--container" library-viewer-name="'. $this->basename( $this->globals['path'] ) .'">';

			$this->print_error_messages();

			if ( !isset($breadcrumb) || 'yes' === $breadcrumb ) {
				$this->print_breadcrumb();
			}
			$this->print_folder_contents();

			echo '</div>';
		}

		return ob_get_clean();
	}

	/**
	 * @since 2.1.0
	 */
	protected function Library_Viewer_Shortcode__enqueue_styles()
	{
		wp_register_style ('library-viewer--css', plugins_url ( 'assets/library-viewer.css', LIBRARY_VIEWER_FILE_ABSPATH ), [], '2.0.8' );
		wp_enqueue_style('library-viewer--css');
	}

	/**
	 * Method that determines if error exists in current folder.
	 *
	 * @since 2.0.0
	 *
	 * @return bool $errors_exists
	 */
	protected function errors_exists_in_current_folder()
	{
		$errors_exists = false;

		if(
			$this->is_no_acceptable_parameters_exists() ||
			$this->is_shortcode_reused() ||
			!$this->is_current_folder_accessible()
		){
			$errors_exists = true;
		}

		return $errors_exists;
	}

	/**
	 * Method that determines if is no acceptable parameters exists.
	 *
	 * @return bool $boolean
	 */
	protected function is_no_acceptable_parameters_exists()
	{
		$error_contents = '';
		$boolean = false;
		foreach ($this->invalid_parameters as $error) {
			$error_contents .= library_viewer_error('not_acceptable_parameter', $error) . '<br>';
			$boolean = true;
		}

		$this->display_errors = $error_contents;
		return $boolean;
	}

	/**
	 * Method that determines if dir is accessible,
	 * checking the global variable $path.
	 *
	 * @since 2.0.0
	 * @return bool
	 */
	protected function is_dir_accessible($dir)
	{
		if ( 0 === strpos($dir , 'library') ) {
			return true;
		} else {
			return false;
		}
	}

	/**
	 * Method that determines if current folder is accessible.
	 * If not, saves the error in the `display_errors` property.
	 *
	 * @since 2.0.0
	 *
	 * @return bool $is_accessible
	 */
	protected function is_current_folder_accessible()
	{
		extract($this->globals);

		$display_errors = false;

		/**
		 * not allow user requesting to view a folder outside of that we have define.
		 */
		if ( $this->is_dir_accessible($dir) ) {
			if ( '' != $real_path && !file_exists( $real_path ) ) {
				$display_errors = library_viewer_error('folder_not_exists') . $this->get_go_back_button_html();
			} elseif ( false !== strpos($real_path, '/..') ) {
				$display_errors = library_viewer_error('no_access') . $this->get_go_back_button_html();
			} elseif ( $this->is_current_folder_hidden($real_path) ) {
				$display_errors = library_viewer_error('no_access') . $this->get_go_back_button_html();
			}
		} else {
			$display_errors = library_viewer_error('no_access') . $this->get_go_back_button_html();
		}

		if( $display_errors === false ){ // default value
			$maybe_error = $this->have_folder_access();
			if( $maybe_error !== true ){
				$display_errors = $maybe_error;
			}
		}

		if( $display_errors !== false ) {
			$this->display_errors = $display_errors;
		}

		return !$display_errors;
	}

	/**
	 * Method that determines if shortcodes is reused in the current page.
	 *
	 * @since 2.0.9 Now uses global $library_viewer_object
	 *
	 * @return bool $shortcode_reused
	 */
	protected function is_shortcode_reused()
	{
		global $library_viewer_object;
		if( !$library_viewer_object ) return false;

		$previous_globals = $library_viewer_object->get_globals();
		if( !$previous_globals ) return false;

		if( $previous_globals == $this->globals ) return false;


		$this->display_errors = library_viewer_error('shortcode_more_than_1_times', '&#91;library-viewer]');
		return true;
	}

	/**
	 * Method that determines if the given folder is hidden.
	 *
	 * @since 2.0.0
	 *
	 * @param string $folder Folder's name.
	 * @return bool $is_hidden Is folder hidden?
	 */
	protected function is_folder_hidden($folder)
	{
		$folder_name = $this->basename($folder);

		$hidden_folders = $this->globals['hidden_folders'];
		if ( $this->exists_in_array($folder_name, $hidden_folders) ) {
			$is_hidden = true;
		} else {
			$is_hidden = false;
		}
		return $is_hidden;
	}

	/**
	 * Is current folder hidden method.
	 *
	 * This method determines if the folder that you call to view via the URL,
	 * is hidden (contains in their link the string 'hidden-folder').
	 *
	 * @since 2.0.0
	 *
	 * @return bool $is_hidden
	 */
	protected function is_current_folder_hidden($the_real_path)
	{
		extract($this->globals);

		$real_path_parts = explode('/', trim($the_real_path, '/'));
		foreach ($real_path_parts as $real_path_part) {
			if ( $this->exists_in_array($real_path_part, $hidden_folders) ) {
				$is_hidden = true;
				return $is_hidden;
			}
		}

		$is_hidden = false;
		return $is_hidden;
	}

	/**
	 * Method that determines if the given file is hidden.
	 *
	 * @since 2.0.0
	 *
	 * @param $file_name File's name.
	 * @return bool $is_hidden Is file hidden?
	 */
	protected function is_file_hidden($file_name)
	{
		if ( $this->exists_in_array($file_name, $this->globals['hidden_files']) ) {
			$is_hidden = true;
		} else {
			$is_hidden = false;
		}
		return $is_hidden;
	}

	/**
	 * Save shortcode parameters in db method.
	 *
	 * Saves the shortcode parameters if need it.
	 * When it need it?
	 * If saved parameters are different or if not exists for requested URL.
	 *
	 * Structure of `library-viewer-shortcodes` option:
	 *
	 * 		$library_viewer_shortcodes = array(
	 * 			'https://mydomain.gr/library-page' => array(
	 *				'have_file_access' => 'logged_in',
	 * 				'login_page'	   => 'login',
	 * 			),
	 * 			'https://mydomain.gr/libraries/library-page' => array(
	 *				'my_doc_viewer' => 'library-viewer',
	 * 				'login_page'	=> 'login',
	 * 			),
	 * 		);
	 *
	 * @since 2.0.0
	 */
	protected function save_shortcode_parameters_in_db()
	{
		$current_page_url = $this->globals['current_page_url'];

		$library_viewer_shortcodes = get_option('library-viewer-shortcodes');
		if ( !is_array($library_viewer_shortcodes) ) {
			$library_viewer_shortcodes = array();
		}

		if ( !isset($library_viewer_shortcodes[$current_page_url]) || $library_viewer_shortcodes[$current_page_url] != $this->parameters ) {
			$library_viewer_shortcodes[$current_page_url] = $this->parameters;
			update_option('library-viewer-shortcodes', $library_viewer_shortcodes);
		}
	}


	/**
	 * Returns the file anchor for given file according to its file_viewer.
	 *
	 * @since 2.0.0
	 *
	 * @param array $file File details. See `print_containing_files` method.
	 * @return string $anchor_html The file anchor.
	 */
	protected function get_file_anchor_html($file, $icon_html)
	{
		extract($this->globals);

		switch ( $file['file_viewer'] ) {
			case 'library-viewer':
				$anchor_href = $current_page_url . "/$file_identifier/" . $file['file_fake_link'];
				$anchor_html = '<a class="library-viewer--file" rel="noopener" target="_blank" href="' . $anchor_href . '">' . $icon_html . $file['file_name'] . '</a>';
				break;
			case 'default':
				$anchor_href = $file['file_full_url'];
				$anchor_html = '<a class="library-viewer--file default_viewer" target="_blank" href="' . $anchor_href . '">' . $icon_html . $file['file_name'] . '</a>';
				break;
			default:
				$to_be_encoded = true;

				/**
				 * my doc viewer file encoded filter.
				 *
				 * With this filter, we can determine if the file will be appended to `my_doc_viewer` as encoded or not default is true (encoded).
				 *
				 * @since 2.0.0
				 *
				 * @param bool $to_be_encoded Encoded or not?
				 * @param array $file {
				 * 		Details of the printed file.
				 *
				 * 		@type string $file_name File's name.
				 * 		@type string $file_fake_link File's fake link.
				 * 		@type string $file_real_link File's real (relative) link.
				 * 		@type string $file_full_url File's full URL (with site_url as prefix).
				 * }
				 * @param array $this->globals See property's documentation.
				 */
				$to_be_encoded = apply_filters('lv_my_doc_viewer_file_encoded', $to_be_encoded, $file, $this->globals);

				if ( $to_be_encoded ) {
					$file_full_url = urlencode( $file['file_full_url'] );
				} else {
					$file_full_url = $file['file_full_url'];
				}
				$anchor_href = $my_doc_viewer . $file_full_url;
				$anchor_html = '<a class="library-viewer--file custom_viewer" rel="noopener" target="_blank" href="' . $anchor_href . '">' . $icon_html . $file['file_name'] . '</a>';
				break;

		}

		$file['file_anchor_href'] = $anchor_href;

		/**
		 * File anchor html filter.
		 *
		 * With this filter, we can change the a html tag of the printed file.
		 *
		 * @since 2.0.0
		 * @since 2.0.3 The variable $file_anchor_href has been also added in the array $file (2nd parameter).
		 *
		 * @param string $anchor_html The html tag a of a printed file.
		 * @param array $file {
		 * 		Details of the printed file.
		 *
		 * 		@type string $file_name File's name.
		 * 		@type string $file_fake_link File's fake link.
		 * 		@type string $file_real_link File's real (relative) link.
		 * 		@type string $file_full_url File's full URL (with site_url as prefix).
		 * 		@type string $file_anchor_href The href attribute of html anchor.
		 * }
		 * @param array $this->globals See property's documentation.
		 */
		$anchor_html = apply_filters('lv_file_anchor_html', $anchor_html, $file, $this->globals);
		return $anchor_html;
	}

	protected function print_error_messages()
	{
		if ( isset($_GET['library-viewer-error-message']) ) {
			$html  = '<div class="library-viewer--error-message">';
			$html .= wp_kses_post( $_GET['library-viewer-error-message'] );
			$html .= '<svg style="float:right; cursor:pointer; fill:white; width:23px; height:23px;" onclick="this.parentElement.parentElement.removeChild(this.parentElement)" xmlns="http://www.w3.org/2000/svg" height="24px" viewBox="0 0 24 24" width="24px" fill="#000000"><path d="M0 0h24v24H0V0z" fill="none"/><path d="M14.59 8L12 10.59 9.41 8 8 9.41 10.59 12 8 14.59 9.41 16 12 13.41 14.59 16 16 14.59 13.41 12 16 9.41 14.59 8zM12 2C6.47 2 2 6.47 2 12s4.47 10 10 10 10-4.47 10-10S17.53 2 12 2zm0 18c-4.41 0-8-3.59-8-8s3.59-8 8-8 8 3.59 8 8-3.59 8-8 8z"/></svg>';
			$html .= '</div>';

			wp_enqueue_script('library-viewer--removeUrlErrorParameter', plugins_url ( 'assets/removeUrlErrorParameter.js', LIBRARY_VIEWER_FILE_ABSPATH ), array(), '2.0.3', true );

			/**
			 * @see library_viewer_error()
			 */
			$html = apply_filters('lv_error_message', $html, 'library-viewer-error-message');

			echo $html;
			unset($_GET['library-viewer-error-message']);
		}

		if ( isset($_GET['library-viewer-success-message']) ) {
			$html  = '<div class="library-viewer--success-message">';
			$html .= wp_kses_post( $_GET['library-viewer-success-message'] );
			$html .= '<svg style="float:right; cursor:pointer; fill:white; width:23px; height:23px;" onclick="this.parentElement.parentElement.removeChild(this.parentElement)" xmlns="http://www.w3.org/2000/svg" height="24px" viewBox="0 0 24 24" width="24px" fill="#000000"><path d="M0 0h24v24H0V0z" fill="none"/><path d="M14.59 8L12 10.59 9.41 8 8 9.41 10.59 12 8 14.59 9.41 16 12 13.41 14.59 16 16 14.59 13.41 12 16 9.41 14.59 8zM12 2C6.47 2 2 6.47 2 12s4.47 10 10 10 10-4.47 10-10S17.53 2 12 2zm0 18c-4.41 0-8-3.59-8-8s3.59-8 8-8 8 3.59 8 8-3.59 8-8 8z"/></svg>';
			$html .= '</div>';

			wp_enqueue_script('library-viewer--removeUrlSuccessParameter', plugins_url ( 'assets/removeUrlSuccessParameter.js', LIBRARY_VIEWER_FILE_ABSPATH ), array(), '2.0.3', true );

			/**
			 * @see library_viewer_error()
			 */
			$html = apply_filters('lv_error_message', $html, 'library-viewer-success-message');

			echo $html;
			unset($_GET['library-viewer-success-message']);
		}
	}

	/**
	 * Gets the name of a given folder.
	 *
	 * This method returns the name of the folder and
	 * does the replacements of symbols (fake & real) if need it.
	 *
	 * @since 2.0.0
	 * @since 3.0.0 lv_folder_name filter added.
	 *
	 * @param string $folder_fake_link The folder's fake link
	 * @param bool $suppress_filters If suppress filters. Default is false.
	 * @return string $folder_name The folder's name.
	 */
	protected function get_folder_name($folder_fake_link, $suppress_filters = false)
	{
		extract($this->globals);

		/**
		 * @since 1.1.0
		 */
		$folder_real_link = $this->get_folder_real_link($folder_fake_link);

		$folder_name = $this->basename($folder_real_link);

		$folder_abs_path = $abspath . $folder_real_link;

		if( !$suppress_filters ) {
			/**
			 * Folder name filter.
			 *
			 * @param string $folder_name Folder's name.
			 * @param string $folder_fake_link Folder's fake link.
			 * @param string $folder_real_link Folder's real (relative) link.
			 * @param string $folder_abs_path The folder's absolute path.
			 * @param array $this ->globals See property's documentation.
			 * @since 3.0.0
			 *
			 */
			$folder_name = apply_filters('lv_folder_name', $folder_name, $folder_fake_link, $folder_real_link, $folder_abs_path, $this->globals);
		}

		return $folder_name;
	}

	/**
	 * Gets the fake link of a given folder.
	 *
	 * @since 2.0.0
	 * @since 2.0.6.3 Fix bug, if path parameter contains spaces
	 *
	 * @param string $folder_real_link Folder's real (relative) link.
	 * @return string $folder_fake_link Folder's fake link.
	 */
	protected function get_folder_fake_link($folder_real_link)
	{
		extract($this->globals);

		$folder_path = $this->get_encrypted_path($folder_real_link);

		/**
		 * @since 1.1.0
		 */
		$folder_fake_link = str_replace(
			$folder_real_path_symbols,
			$folder_fake_path_symbols,
			$folder_path
		);

		return $folder_fake_link;
	}

	/**
	 * Gets the folder's real (relative) link.
	 *
	 * @return string $folder_real_link Folder's real (relative) link.
	 *
	 */
	protected function get_folder_real_link($folder_fake_link)
	{
		extract($this->globals);

		/**
		 * @since 1.1.0
		 */
		$folder_real_link = str_replace(
			$folder_fake_path_symbols,
			$folder_real_path_symbols,
			$folder_fake_link
		);

		return $this->path_prefix() . $folder_real_link;
	}

	/**
	 * Returns the fake link of a given file.
	 *
	 * @since 2.0.0
	 * @since 2.0.6.3 Fix bug, if path parameter contains spaces
	 *
	 * @param string $file_real_link File's real (relative) link.
	 * @return string $file_fake_link File's fake link.
	 */
	protected function get_file_fake_link($file_real_link)
	{
		extract($this->globals);

		$file_path = $this->get_encrypted_path($file_real_link);

		/**
		 * @since 1.1.0
		 */
		$file_fake_link = str_replace(
			$file_real_path_symbols,
			$file_fake_path_symbols,
			$file_path
		);

		return $file_fake_link;
	}

	/**
	 * Returns encrypted path.
	 *
	 * This is used from Library Viewer Pro using the path parameter.
	 * For Library Viewer no encryption is need it.
	 *
	 * @param string $path
	 * @return string $path
	 */
	protected function get_encrypted_path($path)
	{
		return $path;
	}

	protected function get_dir_name()
	{
		return 'dir';
	}

	/**
	 * Returns path prefix.
	 *
	 * Method that is overridden by Library Viewer Pro.
	 *
	 * @param string $mypath
	 * @return string $mypath
	 */
	protected function path_prefix()
	{
		return '';
	}

	/**
	 * Method that determines who have access to folder's contents.
	 *
	 * Method that is overridden by Library Viewer Pro.
	 *
	 * @since 2.0.7
	 *
	 * @return bool
	 */
	protected function have_folder_access(){
		return true;
	}

	/**
	 * Method that is overridden by File Manager Add-on.
	 *
	 * @since 2.0.3
	 */
	protected function get_folder_actions_html($folder){
		return '';
	}

	/**
	 * Method that is overridden by File Manager Add-on.
	 *
	 * @since 2.0.3
	 */
	protected function get_file_actions_html($file){
		return '';
	}

	/**
	 * Method that is overridden by File Manager Add-on.
	 *
	 * @since 2.0.3
	 */
	protected function get_create_actions_html(){
		return '';
	}

	/** Exists in array checker.
	 *
	 * This method checks if the needle exists somewhere
	 * in at least one of the strings of the array.
	 *
	 * @since 2.0.0
	 *
	 * @param string $string The needle.
	 * @param array $array The haystack.
	 * @return bool $exists_in_array
	 */
	public function exists_in_array($string, $array)
	{
		$exists_in_array = false;

		foreach ($array as $value) {
			if ( false !== strpos($string, $value) ) {
				$exists_in_array = true;
				break;
			}
		}

		return $exists_in_array;
	}

	/**
	 * Converts the given string to an array.
	 *
	 * The string is strings seperated with commas.
	 *
	 * @since 2.0.3
	 *
	 * @param string $string The string.
	 * @return array $array The array of commas-seperated-strings.
	 */
	protected function convert_string_to_array($string)
	{
		if( empty($string) ) return [];

		$array = explode(',', $string);
		$array = array_map(function ($s) {
			return trim($s);
		}, $array);

		return $array;
	}

	protected static function get_full_current_url(){
		return ( is_ssl() ? 'https' : 'http' ) . '://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
	}

	protected static function get_need_to_login_url($login_page){
		return site_url() . '/' . $login_page . '?redirect_to=' . urlencode( self::get_full_current_url() ) ;
	}

	protected static function get_need_to_login_button($login_page){
		return library_viewer_error( 'need_to_login_button', self::get_need_to_login_url( $login_page ) );
	}

	public static function get_go_back_button_html()
	{
		return library_viewer_error('go_back_button', library_viewer_error('go_back'));
	}

	/**
	 * Value to return in cases of all, logged-in & have capability, logged-in & have capability, not logged-in
	 *
	 * @since 2.0.7
	 *
	 * @param array $var_to_check
	 * @param mixed $return_for_all
	 * @param mixed $return_for_have_access
	 * @param mixed $return_for_not_have_access
	 * @param mixed $return_for_need_to_login
	 * @return mixed
	 */
	protected function have_access($var_to_check, $return_for_all, $return_for_have_access, $return_for_not_have_access, $return_for_need_to_login){

		/**
		 * @since 2.0.8 Change condition (from ==['all'] to in_array() )
		 */
		if( in_array('all', $var_to_check) ){

			return $return_for_all;

		}elseif( is_user_logged_in() ){

			foreach($var_to_check as $capability){
				if( 'logged_in' == $capability || current_user_can($capability) ){
					return $return_for_have_access;
				}
			}

			return $return_for_not_have_access;

		}else{

			return $return_for_need_to_login;

		}

	}

	/**
	 * Basename method.
	 *
	 * Because native php basename function doesn't work for non latin characters.
	 *
	 * @since 2.0.0
	 *
	 * @param string $mypath
	 * @return string $basename
	 */
	public function basename($mypath)
	{
		$mypath = rtrim($mypath, '/');
		$parts = explode('/', $mypath);
		$basename = $parts[count($parts) - 1];
		return $basename;
	}

	/**
	 * http build query method.
	 *
	 * Build query so can be appended to url and other get parameters not been lost
	 *
	 * @since 2.0.3
	 *
	 * @param string $folder_fake_link The valkue of dir get parameter
	 * @return string $query_result The built query.
	 */
	protected function http_build_query($folder_fake_link){
		$query = $_GET;
		$query[$this->get_dir_name()] = $folder_fake_link;
		$query_result = http_build_query($query);
		$query_result = str_replace('%2F', '/', $query_result);
		return $query_result;
	}

	public function ltrim($string, $trim) {

		if (mb_substr($string, 0, mb_strlen($trim)) == $trim) {

			$string = mb_substr($string, mb_strlen($trim));

		}

		return $string;

	}

	public function rtrim($string, $trim) {

		if (mb_substr($string, -mb_strlen($trim)) == $trim) {

			return mb_substr($string, 0, -strlen($trim));

		}

		return $string;

	}

	public function trim($string, $trim) {

		$string = $this->ltrim($string, $trim);
		$string = $this->rtrim($string, $trim);

		return $string;

	}


}
