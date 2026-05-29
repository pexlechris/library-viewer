=== Library Viewer ===
Contributors: pexlechris
Plugin Name: Library Viewer
Plugin URI: https://www.pexlechris.dev/library-viewer
Author: Pexle Chris
Author URI: https://www.pexlechris.dev
Tags: FTP, file manager, file list, download manager
Version: 3.3.0
Stable tag: 3.3.0
Requires at least: 3.0.0
Tested up to: 6.8.1
Requires PHP: 7.0
Tested up to PHP: 8.2
License: GPLv2
License URI: http://www.gnu.org/licenses/gpl-2.0.html

A File & Folder Viewer for FTP folders, enabling the display of library contents (folders & files) on the front-end and back-end.

== Description ==

Spoiler:
[LIBRARY VIEWER FOR WOOCOMMERCE ADD-ON](https://www.pexlechris.dev/library-viewer/for-woocommerce/) has been released! Check it ;-)

**New in Version 3.3.0: Admin Library Viewer!**
You can now manage and browse your library directly from your WordPress dashboard. Go to **Media > Library Viewer** to get started.

With Library Viewer, you can display the containing files and the containing folders of a "specific folder" of your (FTP) server to your users.

Whether you want to share documents with your customers on the front-end or manage them internally from the WordPress dashboard, Library Viewer provides a seamless and secure experience.

The **significant difference** from other similar plugins is that:
1. You can allow users to **view that the files exist**, but **cannot open them if they are not logged in** (or if they are not administrators, or authors etc...).
2. You can allow users to view files in a **custom viewer or redirect them** through a RESTful web service of your choice (examples exist below).
3. **Admin Integration:** Administrators can now browse the library directly from the WordPress back-end, making file management easier than ever.

[DEMO](https://www.pexlechris.dev/library-viewer/demo-wp)

For this plugin (the free version), the "specific folder" is the folder "library" of your httpdocs (yoursite.com/library).
If you want to display other folder (and its files) that isn't contained in yoursite.com/library, you need to use the path parameter of [Library Viewer Pro](https://www.pexlechris.dev/library-viewer/pro-wp).

This plugin adds the [library-viewer] shortcode to your WordPress site. Simply add this shortcode to any post, page, or widget to display your library to your users.

== Admin Library Viewer ==

The Admin Library Viewer is a powerful feature introduced in version 3.3.0 that brings the functionality of the library directly into your WordPress dashboard. It is designed for site owners and administrators who need a quick and easy way to browse their library without navigating to the front-end.

**Key Features of the Admin Page:**
*   **Centralized Browsing:** View all files and folders in your "library" folder directly from the **Media > Library Viewer** menu.
*   **Customizable Access:** You can define exactly which user roles or capabilities (e.g., editors, custom roles) have permission to view this admin page.
*   **Flexible Display:** The admin page uses the same powerful engine as the shortcode. You can even customize the display settings specifically for the admin area using the built-in settings tab.
*   **Easy Navigation:** Includes a dedicated "Settings" tab where you can change the page title and manage access permissions without touching any code.

**How to Use:**
1.  Go to your WordPress Dashboard.
2.  Navigate to **Media > Library Viewer**.
3.  Use the **Library** tab to browse your files.
4.  Switch to the **Settings** tab to customize the title or restrict access to specific roles.

== Parameters Documentation ==
&nbsp;&nbsp;[PARAMETERS DOCUMENTATION AND USE CASES](https://www.pexlechris.dev/library-viewer/parameters-wp)

&nbsp;&nbsp;**PARAMETERS OF LIBRARY VIEWER**

* **have_file_access** (have_file_access parameter determines which user have access to view the files.)
* **my_doc_viewer** (my_doc_viewer parameter determines in which viewer the file will be opened.)
* **login_page** (login_page parameter defines the login page that user will be redirected -if need it-, to log in.)

&nbsp;&nbsp;**PARAMETERS OF [LIBRARY VIEWER FOR WOOCOMMERCE](https://www.pexlechris.dev/library-viewer/for-woocommerce/)**

* **have_file_access="customer_with_folder_access"** (The `customer_with_folder_access` value for the `have_file_access` parameter grants access to users who have purchased a product with folder access.)
* **sold_on** (sold_on parameter specifies which product must be purchased in order to gain folder access.)


&nbsp;&nbsp;**PARAMETERS OF [LIBRARY VIEWER PRO](https://www.pexlechris.dev/library-viewer/pro-wp)**

* **path** (path determines which folder to display in the Library on the front end. When we refer to a "folder," we mean the folder's contents, including any subfolders and files within it.)
* **guest_view_access** (guest_view_access determines if guest users can view folder contents. By default, all users can see folder contents, but they cannot open files.)
* **waiting_seconds** (waiting_seconds parameter sets the seconds of user is waiting the redirection to login and see the file (0: for instant redirect).)
* **breadcrumb** (breadcrumb parameter determines if breadcrumb will be displayed in the Library in front-end or not.)
* **hidden_folders** (hidden_folders determines which folders will not be displayed and will not be accessible by Library in the front-end.)
* **shown_folders** (shown_folders parameter determines which folders will be displayed and will be accessible by Library in the front-end.)
* **hidden_files** (hidden_files determines which files will not be displayed and will not be accessible by Library in the front-end.)
* **shown_files** (shown_files parameter determines which files will be displayed and will be accessible by Library in the front-end.)
* **url_suffix** (url_suffix allow you to add a suffix in the URL, so you can use the [library-viewer] shortcode more than one time in the same page.)

&nbsp;&nbsp;**PARAMETERS OF [LIBRARY VIEWER FILE MANAGER ADD-ON](https://www.pexlechris.dev/library-viewer/fm-wp)**

* **delete_folder** (delete_folder parameter determines which user can delete a folder.)
* **delete_file** (delete_file parameter determines which user can delete a file.)
* **rename_folder** (rename_folder parameter determines which user can rename a folder.)
* **rename_file** (rename_folder parameter determines which user can rename a file.)
* **create_folder** (create_folder parameter determines which user can create a folder.)
* **upload_file** (upload_file parameter determines which user can upload a file.)
* **unzip_file** (unzip_file parameter determines which user can unzip a zip file.)
* **download_folder** (download_folder parameter determines which user can download a folder as a zip file.)
* **download_file** (download_file parameter determines which user can download a file.)

&nbsp;
[PARAMETERS DOCUMENTATION AND USE CASES](https://www.pexlechris.dev/library-viewer/parameters-wp)

&nbsp;

== Hooks Documentation ==

From 2.0.0 version and then, there are many hooks that you can customize the functionality of this plugin.
You can read more in [HOOKS DOCUMENTATION](https://www.pexlechris.dev/library-viewer/hooks-wp)
Read also [how to add PHP hooks in your WordPress Site in my blog](https://www.pexlechris.dev/how-to-add-php-hooks-in-your-wordpress-site)



== Other Details ==

* The algorithm does not show in the front-end folders that contains in their name the string "hidden-folder".
  Also does not show .php , .ini files and files that contains in their name the string "hidden-file".
  So if you don't want to display an existing folder or file, you can rename it appropriately!
  In addition, With **Library Viewer Pro**, you can **set the names that you don't (and you do) want to be displayed** in the front-end using appropriate shortcode parameters.
* If you want to add text above the front-end folders or below the front-end files, view more in the FAQ below.
* In addition, with the **[LIBRARY VIEWER PRO](https://www.pexlechris.dev/library-viewer/pro-wp)** you can,
 - customize the URLs of library viewer, with the hooks and the parameters.
 - you can display folders & files of a directory (in FTP) of your choice, **not only library directory** (yoursite.com/library)
 - you can hide the breadcrumb with just a shortcode parameter.
 - you can show/hide the folders and files of your choice.
* Also, with the **[LIBRARY VIEWER FOR WOOCOMMERCE ADD-ON](https://www.pexlechris.dev/library-viewer/for-woocommerce/)** you can,
 - give the ability to you users to **gain folder access** to a Library (to a page with the Library Viewer shortcode installed), just **buying** a virtual/with-folder-access product.
* Finally, with the **[LIBRARY VIEWER FILE MANAGER ADD-ON](https://www.pexlechris.dev/library-viewer/fm-wp)** you can,
 - give the ability to your users to have their own library and to upload and edit files.
 - use the library as file manager for your admins (you may need also Library Viewer Pro, and to restrict the WP page from other users)



== Screenshots ==

1. library folder must be located in the root of your FTP server
2. Not all files and folders are displaying in the front-end Library Viewer because of their special names (hidden-folder, hidden-file, .php etc.)
3. The string-value of the $text_at_beginning variable is displayed between the breadcrumb and the folders, the string-value of the $text_at_end variable is displayed below the folders & files.
4. The settings of the default Library Viewer admin page
5. The displayed library according to the settings from the previous screenshot.
6. With Library Viewer File Manager Add-On, you can give the ability to some of your users to manage the library from the front-end.

== Frequently Asked Questions ==

 = How can I use the new Library Viewer Admin Page? =
 Starting from version 3.3.0, you can access your library directly from the WordPress back-end. Simply navigate to **Media > Library Viewer**. You can browse your files in the "Library" tab and customize the page title or access permissions in the "Settings" tab. This is perfect for administrators who need quick access without leaving the dashboard!

 = Can I display a folder that is outside of my WordPress root directory? =
 Yes, with **[Library Viewer Pro](https://www.pexlechris.dev/library-viewer/pro-wp)** you can display any folder on the same server, even if it is outside your WordPress installation directory (e.g., above the public_html folder), as long as the server's PHP has access to it.
 This can be achieved using the `lv_filter_global_abspath` filter to change the default ABSPATH to the root path of the folder you want to display.
 For more detailed instructions and code examples on how to set this up, please feel free to ask in the **[support forum](https://wordpress.org/support/plugin/library-viewer/)**. As you can see, I usually respond within a few hours to help you out!


 = Can I forbid the direct access in the files of the library? I want only via library files can be accessible. =
 With Library Viewer Pro, you can! See this support topic: [wordpress.org/support/topic/executable-pdf-file](https://wordpress.org/support/topic/executable-pdf-file/)


 = How can I deny users to execute php files in folders of my library? =
 You need to add the following code in the .htaccess file of the folder that you want to deny users execute php files
 `
 <Files *.php>
 deny from all
 </Files>
 `


 = Can I hide an existing folder or file of FTP folder from the front-end library? =
 Yes. Please read carefully the section "Other Details" of plugin.


 = Are there shortcode examples? =
 You can test your own use cases in the [DEMO](https://www.pexlechris.dev/library-viewer/demo-wp)


 = Which Page Builders are compatible with Library Viewer? =
 Library Viewer have been tested with TinyMCE (Classic Editor), Gutenberg, WPBakery, Visual Composer, Elementor and works fine!
 Generally can be used, everywhere that shortcodes are accepted...


 = Library Viewer does not work properly and/or I get some ERRORS. Why? =
 - Check the folders' and files' read permissions (safe choice is to use 644)
 - If you use the plugin **Remove Uppercase Ascents** and a CSS code like *.library-viewer--folder{text-transform: uppercase;}* maybe this cause the problem. The solution in this case is to use instead this CSS code: .library-viewer--folder h3 a{text-transform: uppercase;}
- Check if the file or folder has special characters in its name. Some are not supported as names of folders and files such as %.
 In this case, contact me via [email](mailto:info@pexlechris.dev) or via [support forum](https://wordpress.org/support/plugin/library-viewer/) to find a solution!
 - If you use the plugin **Remove Uppercase Ascents** and a CSS code like *.library-viewer--folder{text-transform: uppercase;}* maybe this cause the problem. The solution in this case is to use instead this CSS code: .library-viewer--folder h3 a{text-transform: uppercase;}
 - For other problems, you can open a support ticket in [support forum](https://wordpress.org/support/plugin/library-viewer/)


 = Can I add my custom text inside a folder of front-end library viewer? =
 Yes. If you want to add text above the front-end folders or below the front-end files, you can create via FTP a file with name "include.php" in the FTP folder that you want texts to be shown in front-end.
 HTML tags are allowed!
 Your texts must be values of php variables ($text_at_beginning , $text_at_end respectively) as you can see below:
 `
 <?php
 $text_at_beginning = "My text above front-end folders";
 $text_at_end = "My text below front-end files";
 ?>
 `
 &nbsp;
 Also, you can use the hooks `lv_folder_text_at_beginning` and `lv_folder_text_at_end` respectively for this scope.


 = How to upload files and create new folders? =
 You can do this via FTP/cPanel or you can buy the **[Library Viewer File Manager Add-on](https://www.pexlechris.dev/library-viewer/fm-wp)** to manage the folder from the front-end.


 = Is Library Viewer' file viewer supports all mime types (file extensions)?
 From 1.1.2, the Library Viewer' file viewer supports all mime types that wordpress supports.
 These that included in the function: wp_get_mime_types()
 If you want to add support for mime types that are not included, use the WP filter: lv_mime_types to include them.
 Read more in [HOOKS DOCUMENTATION](https://www.pexlechris.dev/library-viewer/hooks-wp#lv_mime_types)
 Read also [how to add PHP hooks in your WordPress Site in my blog](https://www.pexlechris.dev/how-to-add-php-hooks-in-your-wordpress-site)


 = I want all files to be downloaded. Is that possible?
 Yes, you need to use the Library Viewer' file viewer (my_doc_viewer="library-viewer") and to add the following hook in your functions.php
 `
 add_filter('lv_mime_types', function(){
	return array();
 });
 `
 Read [how to add PHP hooks in your WordPress Site in my blog](https://www.pexlechris.dev/how-to-add-php-hooks-in-your-wordpress-site)


 = Can I change the colors or the fonts that plugin uses? =
 Yes. But only with plain CSS at the moment. So you can add your custom css from WP customizer (from Additional CSS)


 = I have a proposal for a new functionality of this plugin. Can I suggest it to you? =
 Yes. I need new ideas to improve my plugin. Send it to me via <a href="mailto:info@pexlechris.dev">email</a> or via [support forum](https://wordpress.org/support/plugin/library-viewer/)


== Installation ==

1. Download the plugin from [Official WP Plugin Repository](https://wordpress.org/plugins/library-viewer/)
2. Upload Plugin from your WP Dashboard ( Plugins>Add New>Upload Plugin ) the library-viewer.zip file.
3. Activate the plugin through the 'Plugins' menu in WordPress Dashboard
4. Add to a new or existing page/post (or widget etc.) the shortcodes [library-viewer] with the parameters of your choice.
5. Create the folder library and put files and folders there.
6. To view your library in the backend, navigate to **Media > Library Viewer**.



== Changelog ==
 = 3.3.0 =
* [New Feature]: **Admin Library Viewer!** Display and manage your library directly from the WordPress backend (Media > Library Viewer).
* [New]: **Plain permalinks & preview pages are now supported** in the library-viewer shortcode!
* [New]: Autoload behavior can now be controlled via the filter `lv_autoload_shortcodes`. It is recommended to autoload this option **only if shortcodes are used on every page**.
* [New]: New filter introduced: `lv_should_load_shortcode` to control whether the shortcode should be loaded in the current request.
* [New]: Added public static method `Library_Viewer_Init::get_file_identifier()` to retrieve the LV file identifier.
* [New]: Added public static method `Library_Viewer_Init::should_load_file_viewer()` to determine if the file viewer should be loaded for the current request.
* [New]: New filter introduced: `lv_should_load_file_viewer` to control whether the Library Viewer's file viewer should be loaded.
* [Enhancement]: `load_plugin_textdomain` is now hooked to `init` with priority 1 instead of 10.
* [Enhancement]: Code refactor of class `Library_Viewer_Init`.
* [Enhancement]: Option `library-viewer-shortcodes` **is no longer autoloaded by default**.
* [Enhancement]: Renamed class `Library_Viewer_Plugin_Page` to `Library_Viewer_Plugin_Row` to better reflect that it handles the plugin row (action links & meta links) in the Plugins page.
* [Enhancement]: Updated require path to 'class-library-viewer-plugin-row.php' and instantiation to `new Library_Viewer_Plugin_Row();`
* [Enhancement]: Not save in option `library-viewer-shortcodes` rest api URLs (/wp-json/)
* [Enhancement]: Change the priority/order in which globals and parameters are applied. Filters now run immediately after parameter/global initialization.
* [Bug Fix]: Prevent mobile browsers from appending .html extension by adding fallback Content-Type header for downloads.
* [Deprecated]: Method `Library_Viewer_Init::is_frontend()` method has been deprecated. Use `Library_Viewer_Init::should_load_shortcode()` instead, after init with priority 10.
* [Deprecated]: `lv_is_frontend` filter has been deprecated. You can use filter `lv_should_load_shortcode` to control whether the shortcode should be loaded in the current request.
* [Removed]: `set_file_identifier` callback from the `init` action has been removed.

 = 3.2.0 =
* Tested up to WP: 6.9
* [Security Fix]: XSS fix. Please update now.

 = 3.1.0 =
* Tested up to WP: 6.8.1
* [Bug Fix]: The URL structure has been updated — `dir` GET parameter now displays slashes (`/`) instead of their encoded form (`%2F`).

 = 3.0.4 =
* [Bug Fix]: Avoid some errors when file classes are loaded without loading at current page

 = 3.0.3 =
* [Bug Fix]: Fixed a PHP warning `Undefined array key "abspath"`, which occurred when viewing a file.

 = 3.0.2 =
* Tested up to WP: 6.7.2
* [Bug Fix]: Fixed a bug introduced in version 3.0.0 where a hook was loaded on the default WP login page, causing a blank page in some cases.
* [New]: Added a new static method `Library_Viewer_Init::is_frontend()` to determine when the above hooks should be loaded.

 = 3.0.1 =
* [Bug Fix]: Fixed a bug introduced in version 3.0.0 where a user was not being redirected to the file after logging in via a custom login page when using the `login_page` shortcode parameter.

 = 3.0.0 =
* [New Feature]: If the page contains only one shortcode in its content, the current folder name is now prepended to the document title. The `lv_prepend_document_title` action handles this.
* [New Feature]: From now and then, with Library Viewer Pro you can **display any folder of your server** you want! Previously, only folders of the WP installation could be displayed. For more ask in the support forum!
* [New]: Introduced global variable `$library_viewer_object`, which stores the shortcode object after shortcode execution or when a file is being viewed.
* [New]: New class global abspath. Default value is the WP constant ABSPATH, the absolute path of WordPress installation.
* [New]: `lv_prepend_document_title` filter introduced. Used to prepend the current folder in document title parts.
* [New]: `lv_folder_name` filter introduced. With this filter, you can filter the name of a folder. See more details in docs.
* [New]: `lv_breadcrumb_item_html` filter introduced. With this filter, you can filter html of each breadcrumb item. See more details in docs.
* [New]: Library Viewer inner action (not WP action) `enqueue_scripts` introduced, in order all addons be able to enqueue shortcode scripts more efficiently.
* [New]: Library Viewer inner action (not WP action) `enqueue_styles` introduced, in order all addons be able to enqueue shortcode styles more efficiently.
* [New]: Now styles and scripts are enqueued in WP hook wp_enqueue_scripts with callback shortcode class method `enqueue_styles_and_scripts`!
* [New]: New public method `Library_Viewer_Shortcode::get_single_shortcode_attrs()`
* [New]: New public method `Library_Viewer_Shortcode::get_page_shortcodes_matches()`
* [New]: New public method `Library_Viewer_Shortcode::get_page_shortcodes_counter()`
* [New]: New public method `Library_Viewer_Shortcode::get_current_folder()`
* [New]: New public method `Library_Viewer_Shortcode::get_breadcrumb_items()`
* [New]: New public method `Library_Viewer_Shortcode::get_globals()`
* [Enhancement]: Make shortcode & file classes to be loading **after init hook with priority 100 only in front-end** and not when shortcode is executed.
* [Enhancement]: Change inheritance of `protected` method `get_current_page_url()` to `public`.
* [Bug Fix]: A notice has been added for when Library Viewer is incompatible with Library Viewer for WooCommerce.
* [Deprecated]: `lv_before_breadcrumb_start` action has been deprecated. You can use filter lv_breadcrumb_html to **return** the HTML you want.
* [Deprecated]: `lv_after_breadcrumb_start` action has been deprecated. You can use filter lv_breadcrumb_html to **return** the HTML you want.
* [Deprecated]: `lv_after_breadcrumb_end` action has been deprecated. You can use filter lv_breadcrumb_html to **return** the HTML you want.
* [Deprecated]: `lv_before_breadcrumb_end` action has been deprecated. You can use filter lv_breadcrumb_html to **return** the HTML you want.
* [Removed]: The global variable `LIBRARY_VIEWER_SHORTCODE` has been removed. You can replace it with `$GLOBALS['library_viewer_object']?->get_globals()`.
* [Removed]: Library_Viewer_Init::get_library_viewer_file_identifier() static method removed. Use `apply_filters('lv_file_identifier', 'LV')` to get fil identifier.
* [Removed]: The following deprecated hooks that are deprecated in version 2.0.0 has been removed: `LV__folder_was_viewed`, ``LV__file_was_viewed`, `LV__mime_types`, `wp_get_mime_types`, `LV__array_replace_to__in_filenames`, `LV__array_replace_from__in_foldernames`, `LV__array_replace_from__in_filenames`, `LV__folder_html`, `LV__file_html`
* [Removed]: The protected properties of `Library_Viewer_Shortcode` (and its child classes), `shortcode_class_names`, `file_viewer_class_names`, and `all_class_names` have been removed and replaced by the `class_names` property.

 = 2.0.10 =
* Tested up to WP: 6.7.1
* [Bug Fix]: Resolved a minor issue in Library Viewer Pro when used with Library Viewer for WooCommerce.


 = 2.0.9 =
* [Bug Fix]: Fix translation text domain

 = 2.0.8 =
* Tested up to PHP: 8.2
* Tested up to WP: 6.6.2
* [New]: New filter lv_display_errors to restrict access to a folder. See more details in docs.
* [Enhancement]: Combine all CSS files into a single file to improve the UI for first-time content loading.
* [Enhancement]: Fixed deprecated notices in non-standard environments or command-line scripts for server variables when using PHP 8.2.
* [Enhancement]: Remove $GLOBALS['library_viewer_file_identifier'] and change it with static method Library_Viewer_Init::get_library_viewer_file_identifier()
* [Enhancement]: Update Language pot (translation template) file
* [Enhancement]: Rename Library_Viewer_Shortcode::hook() protected method to Library_Viewer_Shortcode::filter() and introduce Library_Viewer_Shortcode::action() method.
* [CHANGELOG FIX]: lv_restrict_folder_access filter was never included in Library Viewer and its plugins codebase,
instead in Library Viewer Pro introduced **guest_view_access parameter** and **global have_folder_access** that can be filtered with a WP hook



 = 2.0.7 =
* Tested up to WP: 6.6.1
* Tested up to PHP: 8.1
* New Required PHP version: 7.0
* [New]: Ability to change or unset (passing empty value) the default folder icon.
Just add in lv_containing_folders in each folder value for folder_icon_html. See docs of hook lv_containing_folders for more.
* [New]: Ability to change or unset (passing empty value) the default file icon (default file icon is a span with no content).
* [New]: New global have_folder_access. Determines who have access to folder contents. Can be filtered with WP hook with Library Viewer Pro!
* [Enhancement]: New help methods in class Library_Viewer_Shortcode
* [Enhancement]: In function lv_error_message(), need_to_login_button, go_back_button cases have been added in order to give the ability to developer to use them.
* [Enhancement]: In filter lv_error_message, $s and $s2 have been added as filter's parameters, because in some cases these strings are used in order to replace the %s in translation strings.

 = 2.0.6.3 =
* [Bug Fix]: Fix bug of Library Viewer Pro, if path parameter contains spaces.

 = 2.0.6.2 =
* [Bug Fix]: Fix of not playing mp4/mp3 files in some cases.

 = 2.0.6.1 =
* Tested up to WP 6.2
* Potential vulnerability fixed: This could allow a malicious actor to redirect users from one site to the other due to the redirect URL not being validated. Users could be tricked to visiting a legitimate site to then be redirected to a malicious site and cause a phishing incident.
* Potential vulnerability fixed: The plugin did not validate and escape some of its shortcode attributes before outputting them back in a page/post where the shortcode is embed, which could allow users with the contributor role and above to perform Stored Cross-Site Scripting attacks.
* Thanks [Mika](https://www.buymeacoffee.com/mikadminfr) for reporting issues

 = 2.0.6 =
* Tested up to WP 6.1.1
* Required PHP: 5.6
* [Bug Fix]: Fix of logout conflict in some cases.


 = 2.0.5 =
* [New]: 2 new globals values added in the File class. file_folder_real_path, file_folder_abs_path
* [New]: html attribute `library-viewer-name` has been added in the div with class `library-viewer--container`
* [Enhancement]: Better message if a shortcode used more than 1 times in the same page.
* [Bug Fix]: Fix a minor php warning when viewing a file with plugin's file viewer
* [Bug Fix]: Compatibility fixed with Library Viewer Pro


 = 2.0.4 =
* Tested up to WP 5.9.2
* [Bug Fix]: Compatibility fixed with Library Viewer File Manager Add-On
* [Bug Fix]: Load textdomain in order to be able to get translations from wordpress.org

 = 2.0.3 =
* Tested up to WP 5.8.1
* [New]: `lv_filter_global_{$parameter}` filter introduced. With this filter, you can filter the parameters BEFORE the rest globals' initialization.
* [New]: `lv_breadcrumb_html` filter introduced. With this filter, you can filter the html of whole breadcrumb.
* [New]: If `library` folder doesn't exist, will be created automatically when the shortcode will called in the front-end.
* [Bug Fix]: In the $globals array that was passed in the hooks, value `current_viewer` was not existed. Now exists.
* [Enhancement]: /languages/library-viewer.pot language template file has been created.
* [Enhancement]: On the filter `lv_file_anchor_html`, the variable $file_anchor_href has been also added in the array $file (2nd parameter). View hook' documentation for more info.
* [Enhancement]: `.library-viewer--folder h3{margin-top: 0; display: inline-block;}` css has been added.
* [Enhancement]: $file_abs_path is added in the $all_files parameter ( $all_files['file_abs_path'] ) in the parameters of hooks: lv_containing_files, lv_file_icon_html, lv_file_html, lv_before_file, lv_after_file.
* [Enhancement]: File icon <span> element has been moved into the <a> html element.
* [Deprecated]: The filter `lv_shortcode_class_name` has been replaced by `lv_shortcode_class_names`. This is an advanced hook...
* [Deprecated]: The filter `lv_file_viewer_class_name` has been replaced by `lv_file_viewer_class_names`. This is an advanced hook...

 = 2.0.2 =
* [Deprecated]: `breadcrumb` value has been removed from Library Viewer globals parameter of all hooks. From now, there is only in Library Viewer Pro's hooks
* [Bug Fix]: Fix compatibility with Library Viewer Pro 2.0.1

 = 2.0.1 =
* [Bug Fix]: Fix bug of Library Viewer Pro. Files weren't opened...

 = 2.0.0 =
* Tested up to WP 5.7
* [Enhancement]: Add compatibility for symbols #, ? for file names and folder names of your library
* [Enhancement]: Security update: Hidden folders (that have in their name the string 'hidden-folder') and hidden-files (that have in their name the string 'hidden-ile'), now,
  are not accessible, if you know the full path of the hidden folder/file.
* [Enhancement]: Now the file link is being encoded and then is appended to the `my_doc_viewer` parameter. If you don't want to be encoded use `lv_my_doc_viewer_file_encoded` filter.
* [Deprecated]: `library-viewer--current-breadcrumb-item` class removed from breadcrumb current item. Replaced with the CSS rule `.library-viewer--breadcrumb-item:last-of-type`
Hooks:
* [Deprecated]: `LV__folder_was_viewed` action replaced with `lv_folder_was_viewed` action.
* [Deprecated]: `LV__array_replace_to__in_foldernames` filter replaced with `lv_folder_fake_path_symbols` filter.
* [Deprecated]: `LV__array_replace_from__in_foldernames` filter replaced with `lv_folder_real_path_symbols` filter.
* [Deprecated]: `LV__array_replace_to__in_filenames` filter replaced with `lv_file_fake_path_symbols` filter.
* [Deprecated]: `LV__array_replace_from__in_filenames` filter replaced with `lv_file_real_path_symbols` filter.
* [Deprecated]: `LV__folder_html` filter replaced with `lv_folder_html` filter.
* [Deprecated]: `LV__file_html` filter replaced with `lv_file_html` filter.
* [Deprecated]: `LV__file_was_viewed` filter replaced with `lv_file_was_viewed` filter.
* [New]: `lv_file_identifier` filter introduced. With this you can change the '/LV/' that is the part of URL of a file.
* [New]: `lv_before_breadcrumb_start` action introduced.
* [New]: `lv_after_breadcrumb_start` action introduced.
* [New]: `lv_breadcrumb_folder_delimiter_html` action introduced. You can change the delimiter of folders of breadcrumb.
* [New]: `lv_breadcrumb_items` action introduced. With this filter, you can alter the breadcrumb items, for example the folder name and folder fake link.
* [New]: `lv_before_breadcrumb_end` action introduced.
* [New]: `lv_after_breadcrumb_end` action introduced.
* [New]: `lv_empty_folder_html` filter introduced. If the current folder contains neither files nor folders, an equivalent message will be displayed an with filter. With this filter you can change it.
* [New]: `lv_folder_text_at_beginning` filter introduced. This filter allow us to add or change the text at beginning of the folder, i.e. the text before the first containing folder.
* [New]: `lv_containing_folders` filter introduced. Containing folders of current folder filter.
* [New]: `lv_folder_icon_html` filter introduced. Used to filter the html of folder icon.
* [New]: `lv_folder_html` filter introduced. Used to filter the html output of printed folder.
* [New]: `lv_before_folder` action introduced.
* [New]: `lv_after_folder` action introduced.
* [New]: `lv_containing_files` filter introduced. Containing files of current folder filter.
* [New]: `lv_file_icon_html` filter introduced. Used to set a file icon using php.
* [New]: `lv_file_html` filter introduced. Used to filter the html output of printed file.
* [New]: `lv_before_file` action introduced.
* [New]: `lv_after_file` action introduced.
* [New]: `lv_folder_text_at_end` filter introduced. This filter allow us to add or change the text at end of the folder, i.e. the text after the last containing file.
* [New]: `lv_folder_was_viewed` action introduced. Do some actions if a folder was accessed/viewed.
* [New]: `lv_file_was_viewed` action introduced. Do some actions if a file was accessed/viewed.
* [New]: Filter `lv_my_doc_viewer_file_encoded` introduced. With this filter you can determine if the file will be appended to `my_doc_viewer` as encoded or not default is true (encoded).
* [New]: Filter `lv_mime_types` introduced. If you want to add support for mime types that are not included, use this filter.

 = 1.2.3 =
* Tested up to WP 5.6
* [Enhancement]: In filter `LV__folder_html` introduced the $attributes parameter
* [New]: filter `LV__file_html` introduced

 = 1.2.2 =
* Tested up to WP 5.5.3
* [Enhancement]: Change Library Viewer Pro URL in plugins' page on dashboard

 = 1.2.1 =
* [Bug Fix]: False Positive: shortcode [library-viewer] seams to be used more than 1 times in the same page, but not

 = 1.2.0 =
* Tested up to WP 5.5.1
* [New]: LV__folder_was_viewed wordpress action was added in the code
* [Enhancement]: From 1.2.0, the shortcode settings are saved in database, not in files. Also, the folder /wp-content/uploads/library-viewer will be deleted!  
* [Bug Fix]: Now Library Viewer' shortcode is supported in the homepage too
* [New]: library-viewer has been added to the available values that my_doc_viewer can get
* [Bug Fix in PRO]: The shortcode [library-viewer] cannot be used more than 1 times in the same page. This feature is available in Library Viewer Pro

 = 1.1.2 =
* LV__mime_types wordpress filter was added in the code
* LV__file_was_viewed wordpress action was added in the code
* Tested up to WP 5.4.2

 = 1.1.1 =
* Some errors has been fixed!

 = 1.1.0 =
* now is possible to restrict users from open files by a **capability** using the have_file_access parameter
* php die() replaced by wp_die() for more pretty messages
* enhancement in code
* delete folder library-viewer of your uploads folder on uninstall
* now you can more easily add an icon in the front of a file using CSS

 = 1.0.7 =
*	Library Viewer has been tested up to WP 5.3.2
*	PHP Notices fixed


= 1.0.6 =
*	Folders icons NOW are printed by css background-image attribute
*	Compatibility with sites that exist in a subdirectory fixed


 = 1.0.5 =
*	SECURITY PATCH (Please update NOW)


 = 1.0.3 =
*	Library Viewer has been tested up to WP 5.2.3
*	readme file was translated in Greek
*	Compatibility with Visual Composer have been tested and works fine
*	Instruction to fix the conflict with Remove Uppercase Ascents Plugin added in FAQ
*	Go Back button have been added in error messages
 
 
 = 1.0.2 =
 
*	Library Viewer has been tested up to WP 5.2.2
*	Link notice for Library Viewer Pro has been added in the backend (WP Plugins Page) 
*	Plugin URI has been fixed
*	A screenshot has been added in the Official WP Page of Library Viewer Plugin
*	Minor typo fixes in the readme file and Official WP Page of Library Viewer Plugin


 = 1.0.1 =
 
*	Compatibility have added for most common special characters(**+** , **&** , **'** , **.**)
*	Redirect waiting time to login is now 5 seconds (if you want to change this you need to buy the [Library Viewer Pro](https://www.pexlechris.dev/library-viewer/pro-wp))
*	The ability of encryption of the real path of your folder (with hash technique) moved to [Library Viewer Pro](https://www.pexlechris.dev/library-viewer/pro-wp)


 = 1.0.0 =
*	Initial Release.
