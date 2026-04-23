<?php

class Library_Viewer_Admin_Page {

    protected string $parent_menu_slug;
    protected string $menu_slug;
    protected array $manage_caps;
    protected string $default_title;
    protected string $option_name;

    protected array $sanitizers = [
        'page_title'            => 'wp_kses_post',
        'library_access_caps'   => 'sanitize_text_field',
        'shortcode'             => 'sanitize_textarea_field',
    ];

    /**
	 * Library_Viewer_Admin_Page constructor.
	 *
	 * This function calls the hooks.
	 *
	 * @since 3.3.0
	 */
	public function __construct($parent_menu_slug, $menu_slug, $title = null, $manage_caps = null)
	{
        $this->parent_menu_slug = $parent_menu_slug;
        $this->menu_slug        = $menu_slug;
        $this->default_title    = $title ?: __('Library Viewer', 'library-viewer');
        $this->manage_caps      = $manage_caps ?: ['manage_options'];

        $this->option_name = 'lv_admin_page_' . str_replace('.php', '_', $this->parent_menu_slug) . str_replace('-', '_', $menu_slug) . '_options';

        add_filter('lv_should_load_shortcode', [$this, 'allow_shortcode_load_at_this_page'], 20);
        add_action('admin_menu', [$this, 'register_admin_page']);
        add_action('admin_init', [$this, 'register_settings']);
	}

    public function allow_shortcode_load_at_this_page($should_load)
    {
        global $pagenow;
        $page = $_GET['page'] ?? '';

        if( $pagenow === $this->parent_menu_slug && $page === $this->menu_slug ){
            return true;
        }

        return $should_load;
    }

	public function register_admin_page(){

        add_submenu_page(
            $this->get_parent_menu_slug(),
            $this->get_page_title(),
            $this->get_menu_title(),
            $this->current_user_can_access() ? 'read' : 'do_not_allow',
            $this->get_menu_slug(),
            [$this, 'render_admin_page_contents'],
        );

	}

    /**
     * Register settings, sections and fields.
     */
    public function register_settings()
    {
        // 1. Register the setting (saved as an array for efficiency)
        register_setting( $this->menu_slug . '_group', $this->option_name, [
            'sanitize_callback' => [$this, 'sanitize_settings']
        ]);

        // 2. Add a section
        add_settings_section(
            $this->menu_slug . '_section',
            __('General Settings', 'library-viewer'),
            null, // No callback needed for section header
            $this->menu_slug
        );

        add_settings_field(
            'lv_page_title',
            __('Page Title', 'library-viewer'),
            [$this, 'render_page_title_field'],
            $this->menu_slug,
            $this->menu_slug . '_section'
        );

        add_settings_field(
            'lv_library_access_caps',
            __('Who can access the library (roles or capabilities, comma-separated)', 'library-viewer'),
            [$this, 'render_library_access_caps_field'],
            $this->menu_slug,
            $this->menu_slug . '_section'
        );

        add_settings_field(
            'lv_shortcode',
            __('Library to display (Paste shortcode here)', 'library-viewer'),
            [$this, 'render_shortcode_field'],
            $this->menu_slug,
            $this->menu_slug . '_section'
        );
    }


    /**
     * Sanitize logic for the options array.
     */
    public function sanitize_settings( $all_inputs )
    {
        foreach ($all_inputs as $key => $val){

            $callback = $this->sanitizers[$key] ?? null;
            $all_inputs[$key] = $callback && is_callable($callback)
                ? call_user_func($callback, $val)
                : sanitize_textarea_field($val);
        }

        return $all_inputs;
    }

    /**
     * HTML for the Title field.
     */
    public function render_page_title_field()
    {
        $description = __('HTML is allowed here.', 'library-viewer');
        echo $this->get_field_html('page_title', $this->default_title, $description);
    }

    public function render_library_access_caps_field()
    {
        $placeholder = implode(', ', $this->manage_caps) . ', my_custom_cap, etc...';
        $description = sprintf( __('Default: %s.', 'library-viewer'), implode(', ', $this->manage_caps) );
        echo $this->get_field_html('library_access_caps', $placeholder, $description);
    }
    public function render_shortcode_field()
    {
        $placeholder = '[library-viewer'."\n".'my_doc_viewer="default"]';
        $description = sprintf( __( __('Default: %s. Paste the shortcode you want to use, exactly as you would in frontend pages.', 'library-viewer'), 'library-viewer'), '[library-viewer]' );
        echo $this->get_field_html('shortcode', $placeholder, $description, 'textarea');
    }


    protected function get_field_html($field, $placeholder = '', $description = '', $type = 'text')
    {
        $name  = $this->option_name . '[' . $field . ']';
        $value = $this->get_option($field);

        // Αν θέλουμε textarea αντί για input
        if ($type === 'textarea') {
            $html = '<textarea name="' . esc_attr($name) . '" placeholder="' . esc_attr($placeholder) . '" class="regular-text" rows="20">' . esc_textarea($value) . '</textarea>';
        } else {
            $html = '<input type="' . esc_attr($type) . '" name="' . esc_attr($name) . '" value="' . esc_attr($value) . '" placeholder="' . esc_attr($placeholder) . '" class="regular-text">';
        }

        if ($description) {
            $html .= '<p class="description">' . $description . '</p>';
        }

        return $html;
    }


	public function render_admin_page_contents()
    {
        $current_tab = isset($_GET['tab']) ? sanitize_key($_GET['tab']) : 'library';

		?>
		<div class="wrap library-viewer-admin-page">
			<h1><?php echo esc_html( get_admin_page_title() ); ?></h1>
            <?php
            $this->render_navigation($current_tab);

            $method_name = "maybe_render_tab_$current_tab";
            if ( method_exists( $this, $method_name ) ) {
                $this->$method_name();
            }
            ?>
		</div>
		<?php
	}

    protected function render_navigation($curr_tab)
    {
        if( ! $this->current_user_can_access_settings() ){
            return;
        }

        $active_styles = 'color: black; font-weight: 500;';
        $parent_slug = $this->get_parent_menu_slug();
        $slug = $this->get_menu_slug();
        ?>
        <div class="library-viewer-admin-page__nav" style="margin: 10px 0 20px 0;">
            <a href="<?php echo esc_url( admin_url("$parent_slug?page=$slug") ); ?>" style="text-decoration: none; <?php if ($curr_tab === 'library') echo $active_styles; ?>">
                <?php _e('Library', 'library-viewer'); ?></a>

            <span class="library-viewer-admin-page__separator"> | </span>

            <a href="<?php echo esc_url( admin_url("$parent_slug?page=$slug&tab=settings") ); ?>" style="text-decoration: none; <?php if ($curr_tab === 'settings') echo $active_styles; ?>">
                <?php _e('Settings', 'library-viewer'); ?></a>
        </div>
        <?php
    }

    protected function maybe_render_tab_library()
    {
        if( ! $this->current_user_can_access_library() ){
            return;
        }

        ?>
        <div class="library-viewer-admin-page__section library-viewer-admin-page__section--library">
            <?php
            $shortcode = $this->get_option('shortcode') ?: '[library-viewer]';
            echo do_shortcode($shortcode);
            ?>
        </div>
        <?php
    }

    protected function maybe_render_tab_settings()
    {
        if( ! $this->current_user_can_access_settings() ){
            return;
        }
        ?>
        <div class="library-viewer-admin-page__section library-viewer-admin-page__section--settings">
            <form action="options.php" method="post">
                <?php
                // Output security fields, hidden inputs etc.
                settings_fields( $this->menu_slug . '_group' );
                // Output the sections and their fields
                do_settings_sections( $this->menu_slug );
                // Output the save button
                submit_button();
                ?>
            </form>
        </div>
        <?php
    }




    /** GETTERS & HELPFUL METHODS */

    protected function get_option($option, $default_value = '')
    {
        $options = get_option( $this->option_name );
        return $options[$option] ?? $default_value;
    }
    protected function get_parent_menu_slug(){
        return $this->parent_menu_slug;
    }

    protected function get_menu_slug(){
        return $this->menu_slug;
    }

    protected function get_title(){
        return $this->get_option('page_title') ?: $this->default_title;
    }

    protected function get_page_title(){
        return $this->get_title();
    }

    protected function get_menu_title(){
        return $this->get_title();
    }

    protected function get_library_access_caps(): array{
        $caps = $this->get_settings_access_caps();

        $library_access_saved_caps = $this->get_option('library_access_caps');

        if($library_access_saved_caps){
            $caps = array_merge( array_values($caps), explode(',', $library_access_saved_caps) );
            $caps = array_unique($caps);
        }

        return $caps;
    }

    protected function get_settings_access_caps(): array{
        return $this->manage_caps;
    }



    protected function current_user_can_access(): bool
    {
        return $this->current_user_can_access_library() || $this->current_user_can_access_settings();
    }

    protected function current_user_can_access_library(): bool
    {
        return $this->current_user_has_any_cap( $this->get_library_access_caps() );
    }

    protected function current_user_can_access_settings(): bool
    {
        return $this->current_user_has_any_cap( $this->get_settings_access_caps() );
    }

    protected function current_user_has_any_cap( array $caps ): bool
    {
        foreach ( $caps as $cap ) {
            if ( current_user_can( $cap ) ) {
                return true;
            }
        }

        return false;
    }



}
