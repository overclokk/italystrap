<?php namespace ItalyStrap\Admin;

use ItalyStrap\Core as Core;

use WP_Customize_Color_Control;
use	WP_Customize_Media_Control;
use	Textarea_Custom_Control;

/**
 * Contains methods for customizing the theme customization screen.
 *
 * @todo https://codex.wordpress.org/Function_Reference/header_textcolor
 * @todo https://github.com/overclokk/wordpress-theme-customizer-custom-controls
 *
 * @link http://codex.wordpress.org/Theme_Customization_API
 * @link https://developer.wordpress.org/themes/advanced-topics/customizer-api/
 * @since ItalyStrap 1.0
 */
class Customizer{

	/**
	 * $capability
	 *
	 * @var string
	 */
	private $capability = 'edit_theme_options';

	/**
	 * Variable with all CSS
	 *
	 * @var string
	 */
	private $style = '';

	/**
	 * ItalyStrap option panel page name
	 *
	 * @var string
	 */
	private $panel = 'italystrap_options_page';

	/**
	 * The default text for colophon
	 *
	 * @var string
	 */
	private $colophon_default_text = '';

	/**
	 * Init the class
	 */
	function __construct() {

		$this->colophon_default_text = apply_filters( 'italystrap_colophon_default_text', Core\colophon_default_text() );

		// /**
		//  * Setup the Theme Customizer settings and controls...
		//  */
		// add_action( 'customize_register' , array( $this, 'register_init' ) );

		// // Enqueue live preview javascript in Theme Customizer admin screen.
		// add_action( 'customize_preview_init' , array( $this, 'live_preview' ) );

		// // Output custom CSS to live site.
		// add_action( 'wp_head' , array( $this, 'css_output' ), 11 );

		// /**
		//  * Add link to Theme Options in case ItalyStrap plugin is active
		//  */
		// if ( defined( 'ITALYSTRAP_PLUGIN' ) ) {
		// 	add_action( 'admin_menu', array( $this, 'add_link_to_theme_option_page' ) );
		// }

		// /**
		//  * Add new voice to theme menu
		//  */
		// add_action( 'admin_menu', array( $this, 'add_appearance_menu' ) );

	}

	/**
	 * Function for adding link to Theme Options in case ItalyStrap plugin is active
	 *
	 * @link http://snippets.webaware.com.au/snippets/add-an-external-link-to-the-wordpress-admin-menu/
	 * @link https://developer.wordpress.org/themes/advanced-topics/customizer-api/#focusing
	 * autofocus[panel|section|control]=ID
	 */
	public function add_link_to_theme_option_page() {

		global $submenu;
		/**
		 * Link to customizer
		 *
		 * @link http://wptheming.com/2015/01/link-to-customizer-sections/
		 * @var string
		 */
		$url = admin_url( 'customize.php?autofocus[panel]=italystrap_options_page' );
		$submenu['italystrap-dashboard'][] = array(
			__( 'Theme Options', 'ItalyStrap' ),
			$this->capability,
			$url,
		);
	}

	/**
	 * Add new menu in theme.php
	 */
	public function add_appearance_menu() {

		/**
		 * Add theme page
		 *
		 * @link https://codex.wordpress.org/Function_Reference/add_theme_page
		 */
		add_theme_page(
			__( 'ItalyStrap Theme Info', 'ItalyStrap' ),// $page_title <title></title>
			__( 'ItalyStrap Theme Info', 'ItalyStrap' ),// $menu_title.
			$this->capability,							// $capability.
			'italystrap-theme-info',					// $menu_slug.
			array( $this, 'callback_function' )			// $function.
		);

	}

	/**
	 * Add WordPress standard form for options page
	 */
	public function callback_function() {

		if ( ! current_user_can( $this->capability ) ) {
			wp_die( esc_attr__( 'You do not have sufficient permissions to access this page.', 'ItalyStrap' ) );
		}

		?>

			<div class="wrap">
				<h2>
					<span class="dashicons dashicons-admin-settings" style="font-size:32px;margin-right:15px"></span> ItalyStrap panel
				</h2>
				<form action='options.php' method='post'>
					
					<?php
					settings_fields( 'italystrap_theme_options_group' );
					do_settings_sections( 'italystrap_theme_options_group' );
					submit_button();
					?>
					
				</form>
			</div>

		<?php

	}

	/**
	 * This hooks into 'customize_register' (available as of WP 3.4) and allows
	 * you to add new sections and controls to the Theme Customize screen.
	 *
	 * Note: To enable instant preview, we have to actually write a bit of custom
	 * javascript. See live_preview() for more.
	 *
	 * @see add_action('customize_register',$func)
	 * @param  object $wp_customize The cutomizer object.
	 * @link http://ottopress.com/2012/how-to-leverage-the-theme-customizer-in-your-own-themes/
	 * @since ItalyStrap 1.0
	 */
	public function register_init( $wp_customize ) {

		/**
		 * Changing Customizer Color Sections Titles
		 */
		$wp_customize->get_section( 'colors' )->title = __( 'Theme Colors', 'ItalyStrap' );

		/**
		 * 2. Register new settings to the WP database...
		 */
		$wp_customize->add_setting(
			'link_textcolor', // No need to use a SERIALIZED name, as `theme_mod` settings already live under one db record.
			array(
				'default' => '#337ab7', // Default setting/value to save.
				'type' => 'theme_mod', // Is this an 'option' or a 'theme_mod'?
				'capability' => $this->capability, // Optional. Special permissions for accessing this setting.
				'transport' => 'postMessage', // What triggers a refresh of the setting? 'refresh' or 'postMessage' (instant)?
				'sanitize_callback' => 'sanitize_hex_color',
				)
		);

		/**
		 * 3. Finally, we define the control itself (which links a setting to a section and renders the HTML controls)...
		 */
		$wp_customize->add_control(
			new WP_Customize_Color_Control( // Instantiate the color control class
				$wp_customize, // Pass the $wp_customize object (required).
				'italystrap_link_textcolor', // Set a unique ID for the control.
				array(
					'label' => __( 'Link Color', 'ItalyStrap' ), // Admin-visible name of the control.
					'section' => 'colors', // ID of the section this control should render in (can be one of yours, or a WordPress default section).
					'settings' => 'link_textcolor', // Which setting to load and manipulate (serialized is okay).
					'priority' => 10, // Determines the order this control appears in for the specified section.
				)
			)
		);

		/**
		 * Hx font color
		 */
		$wp_customize->add_setting(
			'hx_textcolor',
			array(
				'default' => '#333',
				'type' => 'theme_mod',
				'capability' => $this->capability,
				'transport' => 'postMessage',
				'sanitize_callback' => 'sanitize_hex_color',
			)
		);

		$wp_customize->add_control(
			new WP_Customize_Color_Control(
				$wp_customize,
				'italystrap_hx_textcolor',
				array(
					'label' => __( 'Heading Color', 'ItalyStrap' ),
					'section' => 'colors',
					'settings' => 'hx_textcolor',
					'priority' => 10,
				)
			)
		);

		/**
		 * Add new panel for ItalyStrap theme options
		 */
		$wp_customize->add_panel( $this->panel,
			array(
				'title' => __( 'Theme Options', 'ItalyStrap' ),
				'description' => 'add_panel', // Include html tags such as <p>.
				'priority' => 160, // Mixed with top-level-section hierarchy.
			)
		);

		/**
		 * Define a new section for theme image options
		 */
		$wp_customize->add_section(
			'italystrap_image_options',
			array(
				'title' => __( 'Theme Image Options', 'ItalyStrap' ), // Visible title of section.
				'panel' => $this->panel,
				'capability' => $this->capability,
				'description' => __( 'Allows you to customize settings for ItalyStrap.', 'ItalyStrap' ),
			)
		);

		/**
		 * Register new settings to the WP database...
		 */
		$wp_customize->add_setting(
			'logo',
			array(
				'default' => ITALYSTRAP_PARENT_PATH . '/img/italystrap-logo.jpg',
				'type' => 'theme_mod',
				'capability' => $this->capability,
				'transport' => 'postMessage',
				'sanitize_callback' => 'sanitize_text_field',
			)
		);

		$wp_customize->add_control(
			new WP_Customize_Media_Control(
				$wp_customize,
				'italystrap_logo',
				array(
					'label' => __( 'Your Logo', 'ItalyStrap' ),
					'description' => __( 'Insert here your logo', 'ItalyStrap' ),
					'section' => 'italystrap_image_options',
					'settings' => 'logo',
					'priority' => 10,
				)
			)
		);

		/**
		 * Setting for navbar logo image
		 */
		$wp_customize->add_setting(
			'navbar_logo_image',
			array(
				// 'default' => ITALYSTRAP_PARENT_PATH . '/img/italystrap-navbar_logo_image.jpg',
				'default' => '',
				'type' => 'theme_mod',
				'capability' => $this->capability,
				'transport' => 'postMessage',
				'sanitize_callback' => 'sanitize_text_field',
			)
		);

		$wp_customize->add_control(
			new WP_Customize_Media_Control(
				$wp_customize,
				'italystrap_navbar_logo_image',
				array(
					'label' => __( 'Your logo brand for nav menu', 'ItalyStrap' ),
					'description' => __( 'Insert here your logo brand for nav menu', 'ItalyStrap' ),
					'section' => 'italystrap_image_options',
					'settings' => 'navbar_logo_image',
					'priority' => 10,
				)
			)
		);

		/**
		 * Display navbar brand name with navbar logo image
		 */
		$wp_customize->add_setting(
			'display_navbar_logo_image',
			array(
				'default' => '',
				'type' => 'theme_mod',
				'capability' => $this->capability,
				'transport' => 'postMessage',
				'sanitize_callback' => 'sanitize_text_field',
			)
		);
		$wp_customize->add_control(
			'italystrap_display_navbar_logo_image',
			array(
				'settings' => 'display_navbar_logo_image',
				'label'    => __( 'Display navbar brand name with navbar logo image', 'ItalyStrap' ),
				'section'  => 'italystrap_image_options',
				'type'     => 'checkbox',
			)
		);

		/**
		 * Set a default image to use in:
		 * the_thumbnail
		 */
		$wp_customize->add_setting(
			'default_image',
			array(
				'default' => ITALYSTRAP_PARENT_PATH . '/img/italystrap-default-image.png',
				'type' => 'theme_mod',
				'capability' => $this->capability,
				'transport' => 'postMessage',
				'sanitize_callback' => 'sanitize_text_field',
			)
		);

		$wp_customize->add_control(
			new WP_Customize_Media_Control(
				$wp_customize,
				'italystrap_default_image',
				array(
					'label' => __( 'Default Image', 'ItalyStrap' ),
					'description' => __( 'Upload an image for the default image used for social sharing (must be at least 1200x600px for Facebook), it will also be displayed if no feautured image will be added in your content page/post if the theme supports this feature.', 'ItalyStrap' ),
					'section' => 'italystrap_image_options',
					'settings' => 'default_image',
					'priority' => 10,
				)
			)
		);

		$wp_customize->add_setting(
			'default_404',
			array(
				'default' => ITALYSTRAP_PARENT_PATH . '/img/404.jpg',
				'type' => 'theme_mod',
				'capability' => $this->capability,
				'transport' => 'postMessage',
				'sanitize_callback' => 'sanitize_text_field',
			)
		);

		$wp_customize->add_control(
			new WP_Customize_Media_Control(
				$wp_customize,
				'italystrap_default_404',
				array(
					'label' => __( 'Default 404 Image', 'ItalyStrap' ),
					'description' => __( 'This is a default 404 image, it will be displayed in 404 page (must be at least weight 848px)', 'ItalyStrap' ),
					'section' => 'italystrap_image_options',
					'settings' => 'default_404',
					'priority' => 10,
				)
			)
		);

		/**
		 * NEW SECTION
		 */

		/**
		 * Define a new section for cusom CSS
		 */
		$wp_customize->add_section( 'custom_css',
			array(
				'title' => __( 'Custom CSS' ),
				'description' => __( 'Add custom CSS here' ),
				'panel' => $this->panel, // Not typically needed.
				'priority' => 160,
				'capability' => $this->capability,
				'theme_supports' => '', // Rarely needed.
			)
		);

		/**
		 * Add a textarea control for custom css
		 */
		$wp_customize->add_setting(
			'custom_css',
			array(
				'default'        => '',
				'type' => 'theme_mod',
				'capability' => $this->capability,
				'transport' => 'postMessage',
				'sanitize_callback' => 'sanitize_text_field',
			)
		);

		$wp_customize->add_control(
			new Textarea_Custom_Control(
				$wp_customize,
				'custom_css',
				array(
					'label'   => __( 'Custom CSS', 'ItalyStrap' ),
					'description' => __( '', 'ItalyStrap' ),
					'section' => 'custom_css',
					'settings'   => 'custom_css',
					'priority' => 10,
				)
			)
		);

		/**
		 * Define a new section for typography
		 */
		$wp_customize->add_section( 'typography',
			array(
				'title' => __( 'Typography', 'ItalyStrap' ),
				'description' => __( 'Chose typography style', 'ItalyStrap' ),
				'panel' => $this->panel, // Not typically needed.
				'priority' => 160,
				'capability' => $this->capability,
				'theme_supports' => '', // Rarely needed.
			)
		);

		/**
		 * Add a textarea control for custom css
		 */
		$wp_customize->add_setting(
			'typography',
			array(
				'default'        => '',
				'type' => 'theme_mod',
				'capability' => $this->capability,
				'transport' => 'postMessage',
				'sanitize_callback' => 'sanitize_text_field',
			)
		);

		$wp_customize->add_control(
			new Google_Font_Dropdown_Custom_Control(
				$wp_customize,
				'typography',
				array(
					'label'   => __( 'Typography', 'ItalyStrap' ),
					'description' => __( 'Chose typography style', 'ItalyStrap' ),
					'section' => 'typography',
					'settings'   => 'typography',
					'priority' => 10,
				)
			)
		);

		/**
		 * Define a new section for Footer colophon
		 */
		$wp_customize->add_section( 'colophon',
			array(
				'title' => __( 'Footer\'s Colophon' ),
				'description' => __( 'Add text for footer\'s colophon here' ),
				'panel' => $this->panel, // Not typically needed.
				'priority' => 160,
				'capability' => $this->capability,
				'theme_supports' => '', // Rarely needed.
			)
		);

		/**
		 * Add a textarea control for Colophon
		 */
		$wp_customize->add_setting(
			'colophon',
			array(
				'default'        => $this->colophon_default_text,
				'type' => 'theme_mod',
				'capability' => $this->capability,
				'transport' => 'postMessage',
				'sanitize_callback' => 'wp_kses_post',
			)
		);

		$wp_customize->add_control(
			new Textarea_Custom_Control(
				$wp_customize,
				'colophon',
				array(
					'label'   => __( 'Footer\'s Colophon', 'ItalyStrap' ),
					'description' => __( '', 'ItalyStrap' ),
					'section' => 'colophon',
					'settings'   => 'colophon',
					'priority' => 10,
				)
			)
		);

		/**
		 * Let's make some stuff use live preview JS...
		 */
		$wp_customize->get_setting( 'blogname' )->transport = 'postMessage';
		$wp_customize->get_setting( 'blogdescription' )->transport = 'postMessage';
		$wp_customize->get_setting( 'header_textcolor' )->transport = 'postMessage';
		$wp_customize->get_setting( 'background_color' )->transport = 'postMessage';

		// Hide core sections/controls when they aren't used on the current page.
		// $wp_customize->get_section( 'header_image' )->active_callback = 'is_front_page';
		// $wp_customize->get_control( 'blogdescription' )->active_callback = 'is_front_page';
		$this->set_theme_mod_from_options();

	}

	/**
	 * Retrieves the attachment ID from the file URL
	 *
	 * @link https://pippinsplugins.com/retrieve-attachment-id-from-image-url/
	 * @param  string $image_url The src of the image.
	 * @return int               Return the ID of the image
	 */
	private function pippin_get_image_id( $image_url ) {

		$attachment = wp_cache_get( 'get_image_id' . $image_url );

		if ( false === $attachment ) {

			global $wpdb;
			$attachment = $wpdb->get_col( $wpdb->prepare( "SELECT ID FROM $wpdb->posts WHERE guid='%s';", $image_url ) );
			wp_cache_set( 'get_image_id' . $image_url, $attachment );
		}

		$attachment[0] = ( isset( $attachment[0] ) ) ? $attachment[0] : null;

		return absint( $attachment[0] );

	}

	/**
	 * Esporto le opzioni precedentemente registrate in options e le carico nelle opzioni del tema
	 */
	public function set_theme_mod_from_options() {

		/**
		 * Rendo globale la variabile delle opzioni
		 */
		global $italystrap_options;

		if ( ! $italystrap_options ) {
			return;
		}

		foreach ( $italystrap_options as $key => $value ) {

			if ( ! get_theme_mod( $key ) && preg_match( '#png|jpg|gif#is', $italystrap_options[ $key ] ) ) {

				set_theme_mod( $key, $this->pippin_get_image_id( $italystrap_options[ $key ] ) );
			} elseif ( ! get_theme_mod( $key ) ) {

				set_theme_mod( $key, $italystrap_options[ $key ] );

			}

			/**
			 * Test
			 * var_dump( $key . ' => ' . get_theme_mod( $key ) );
			 * remove_theme_mod( $key );
			 */

		}

		/**
		 * Test
		 * remove_theme_mod( 'colophon' );
		 * var_dump(get_theme_mods());
		 */
	}


	/**
	 * This outputs the javascript needed to automate the live settings preview.
	 * Also keep in mind that this function isn't necessary unless your settings.
	 * are using 'transport'=>'postMessage' instead of the default 'transport'
	 * => 'refresh'
	 *
	 * Used by hook: 'customize_preview_init'
	 *
	 * @see add_action('customize_preview_init',$func)
	 * @since ItalyStrap 1.0
	 */
	public function live_preview() {
		wp_enqueue_script(
			'italystrap-theme-customizer', // Give the script a unique ID
			ITALYSTRAP_PARENT_PATH . '/admin/js/theme-customizer.min.js', // Define the path to the JS file
			array( 'jquery', 'customize-preview' ), // Define dependencies.
			null, // Define a version (optional).
			true // Specify whether to put in footer (leave this true).
		);
	}

	/**
	 * Default custom background callback.
	 *
	 * @since 3.0.0
	 * @access protected
	 */
	public function custom_background_cb() {

		/**
		 * $background is the saved custom image, or the default image.
		 *
		 * @var string
		 */
		$background = set_url_scheme( get_background_image() );

		/**
		 * $color is the saved custom color.
		 * A default has to be specified in style.css. It will not be printed here.
		 *
		 * @var string
		 */
		$color = get_background_color();

		if ( get_theme_support( 'custom-background', 'default-color' ) === $color ) {
			$color = false;
		}

		if ( ! $background && ! $color ) {
			return;
		}

		$style = $color ? 'background-color:#' . $color . ';' : '';

		if ( $background ) {
			$image = 'background-image:url(' . $background . ');';

			$repeat = get_theme_mod( 'background_repeat', get_theme_support( 'custom-background', 'default-repeat' ) );

			if ( ! in_array( $repeat, array( 'no-repeat', 'repeat-x', 'repeat-y', 'repeat' ), true ) ) {
				$repeat = 'repeat';
			}

			$repeat = 'background-repeat:' . $repeat . ';';

			$position = get_theme_mod( 'background_position_x', get_theme_support( 'custom-background', 'default-position-x' ) );

			if ( ! in_array( $position, array( 'center', 'right', 'left' ), true ) ) {
				$position = 'left';
			}

			$position = 'background-position: top ' . $position . ';';

			$attachment = get_theme_mod( 'background_attachment', get_theme_support( 'custom-background', 'default-attachment' ) );

			if ( ! in_array( $attachment, array( 'fixed', 'scroll' ), true ) ) {
				$attachment = 'scroll';
			}

			$attachment = 'background-attachment: ' . $attachment . ';';

			$style .= $image . $repeat . $position . $attachment;

		}

		$this->style .= 'body.custom-background{' . trim( $style ) . '}';

	}

	/**
	 * This will generate a line of CSS for use in header or footer output.
	 * If the setting ($mod_name) has no defined value, the CSS will not be output.
	 *
	 * @uses get_theme_mod()
	 * @param string $selector CSS selector.
	 * @param string $property The name of the CSS *property* to modify.
	 * @param string $mod_name The name of the 'theme_mod' option to fetch.
	 * @param string $prefix Optional. Anything that needs to be output before the CSS property.
	 * @param string $postfix Optional. Anything that needs to be output after the CSS property.
	 * @param bool   $echo Optional. Whether to print directly to the page (default: true).
	 * @return string Returns a single line of CSS with selectors, property and value.
	 * @since ItalyStrap 1.0
	 */
	public function generate_css( $selector, $property, $mod_name, $prefix = '', $postfix = '', $echo = true ) {

		/**
		 * Get theme mod by mod_name
		 *
		 * @var string
		 */
		$mod = get_theme_mod( $mod_name );

		/**
		 * If mod is empty return
		 */
		if ( empty( $mod ) ) {
			return;
		}

		/**
		 * CSS style from customizer
		 *
		 * @var string
		 */
		$return = $selector . '{' . $property . ':' . $prefix . $mod . $postfix . ';}';

		return $return;

	}

	/**
	 * This will output the custom WordPress settings to the live theme's WP head.
	 *
	 * Used by hook: 'wp_head'
	 *
	 * @see add_action('wp_head',$func)
	 * @see add_action('wp_footer',$func)
	 * @since ItalyStrap 1.0
	 */
	public function css_output() {

		global $italystrap_theme_mods;

		/**
		 * Custom CSS section on customizer page
		 *
		 * @var string
		 */
		$custom_css = ( isset( $italystrap_theme_mods['custom_css'] ) ) ? $italystrap_theme_mods['custom_css'] : '' ;

		$this->style .= $this->generate_css( '#site-title a', 'color', 'header_textcolor', '#' );

		$this->style .= $this->generate_css( 'h1, h2, h3, h4, h5, h6, .h1, .h2, .h3, .h4, .h5, .h6, .heading', 'color', 'hx_textcolor' );
		/**
		 * $css .= $this->generate_css('body.custom-background', 'background-color', 'background_color', '#');
		 */
		$this->style .= $this->generate_css( 'a', 'color', 'link_textcolor' );
		/**
		 * $css .= $this->generate_css('.widget-title,.footer-widget-title', 'border-bottom-color', 'link_textcolor');
		 */

		$this->style .= $custom_css;

		$this->style .= apply_filters( 'italystrap_css_output', $this->style );

		echo '<style type="text/css" id="custom-background-css">' . esc_attr( $this->minify_output( $this->style ) ) . '</style>';

	}

	/**
	 * Minify the CSS output
	 *
	 * @param  string $css The CSS output.
	 * @return string      The CSS minified
	 */
	public function minify_output( $css ) {

		return $css = str_replace(
			array(
				"\n",
				"\r",
				"\t",
				'&amp;nbsp;',
				),
			'',
			$css
		);
	}
}

/**
 * Fallback function for custom background.
 */
function italystrap_custom_background_cb() {

	global $italystrap_customizer;

	if ( ! $italystrap_customizer ) {
		$italystrap_customizer = new Customizer;
	}

	$italystrap_customizer->custom_background_cb();

}