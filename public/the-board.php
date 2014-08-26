<?php
/**
 * The Board.
 *
 * @package   The_Board
 * @author    Soixante circuits
 * @license   GPL-2.0+
 */

/**
 * Plugin class. This class should ideally be used to work with the
 * public-facing side of the WordPress site.
 *
 * If you're interested in introducing administrative or dashboard
 * functionality, then refer to `the-board-admin.php`
 *
 * @package The_Board
 * @author  soixante circuits
 */
class The_Board {

	/**
	 * Plugin version, used for cache-busting of style and script file references.
	 *
	 * @since   1.0.0
	 *
	 * @var     string
	 */
	const VERSION = '1.0.0';

	/**
	 * @theboard - Rename "the-board" to the name of your plugin
	 *
	 * Unique identifier for your plugin.
	 *
	 *
	 * The variable name is used as the text domain when internationalizing strings
	 * of text. Its value should match the Text Domain file header in the main
	 * plugin file.
	 *
	 * @since    1.0.0
	 *
	 * @var      string
	 */
	protected $plugin_slug = 'the-board';

	/**
	 * Instance of this class.
	 *
	 * @since    1.0.0
	 *
	 * @var      object
	 */
	protected static $instance = null;

	/**
	 * Initialize the plugin by setting localization and loading public scripts
	 * and styles.
	 *
	 * @since     1.0.0
	 */
	private function __construct() {

		// Load plugin text domain
		add_action( 'init', array( $this, 'load_plugin_textdomain' ) );

		// Activate plugin when new blog is added
		add_action( 'wpmu_new_blog', array( $this, 'activate_new_site' ) );

		// Load public-facing style sheet and JavaScript.
		add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_styles' ) );
		add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_scripts' ) );

		/* Define custom functionality.
		 * Refer To http://codex.wordpress.org/Plugin_API#Hooks.2C_Actions_and_Filters
		 */
		add_action( 'init', array( $this, 'tb_get_member_shortcode' ) );
		add_filter( '@theboard', array( $this, 'filter_method_name' ) );

	}

	/**
	 * Return the plugin slug.
	 *
	 * @since    1.0.0
	 *
	 * @return    Plugin slug variable.
	 */
	public function get_plugin_slug() {
		return $this->plugin_slug;
	}

	public function get_fields() {
		$prefix = 'tb_';
		return array(
			array(
					'label'		=> __('Lastname', 'the-board'),
					'desc'		=> __('Lastname of the member.', 'the-board'),
					'id'		=> $prefix . 'lastname',
					'type'		=> 'text',
					'context'	=> 'normal',
					'priority'	=> 'default'
				),
			array(
					'label'		=> __('Firstname', 'the-board'),
					'desc'		=> __('Firstname of the member.', 'the-board'),
					'id'		=> $prefix . 'firstname',
					'type'		=> 'text',
					'context'	=> 'normal',
					'priority'	=> 'default'
				),
			array(
					'label'		=> __('Job', 'the-board'),
					'desc'		=> __('Job occupied by the member.', 'the-board'),
					'id'		=> $prefix . 'job',
					'type'		=> 'text',
					'context'	=> 'normal',
					'priority'	=> 'default'
				),
			array(
					'label'		=> __('Invert in glossary', 'the-board'),
					'desc'		=> __('If this is checked, member will be sorted by its firstname. "John Smith" would be find at "John" (J) instead of "Smith" (S).', 'the-board'),
					'id'		=> $prefix . 'invert',
					'type'		=> 'checkbox',
					'context'	=> 'side',
					'priority'	=> 'low'
				),
			array(
					'label'		=> __('Email', 'the-board'),
					'desc'		=> __('Email of the member.', 'the-board'),
					'id'		=> $prefix . 'email',
					'type'		=> 'email',
					'context'	=> 'normal',
					'priority'	=> 'default'
				),
			array(
					'label'		=> __('Contact form', 'the-board'),
					'desc'		=> __('Contact form of the member. Put the ID provided by Contact Form 7.', 'the-board'),
					'id'		=> $prefix . 'contact',
					'type'		=> 'contact',
					'context'	=> 'normal',
					'priority'	=> 'default'
				),
			array(
					'label'		=> __('Facebook', 'the-board'),
					'desc'		=> __('URL for the Facebook account of the member.', 'the-board'),
					'id'		=> $prefix . 'facebook',
					'type'		=> 'text',
					'context'	=> 'normal',
					'priority'	=> 'low'
				),
			array(
					'label'		=> __('Twitter', 'the-board'),
					'desc'		=> __('URL for the Twitter account of the member.', 'the-board'),
					'id'		=> $prefix . 'twitter',
					'type'		=> 'text',
					'context'	=> 'normal',
					'priority'	=> 'low'
				),
			array(
					'label'		=> __('Google+', 'the-board'),
					'desc'		=> __('URL for the Google+ account of the member.', 'the-board'),
					'id'		=> $prefix . 'googleplus',
					'type'		=> 'text',
					'context'	=> 'normal',
					'priority'	=> 'low'
				),
			array(
					'label'		=> __('LinkedIn', 'the-board'),
					'desc'		=> __('URL for the LinkedIn account of the member.', 'the-board'),
					'id'		=> $prefix . 'linkedIn',
					'type'		=> 'text',
					'context'	=> 'normal',
					'priority'	=> 'low'
				),
			array(
					'label'		=> __('Skype', 'the-board'),
					'desc'		=> __('URL for the Skype account of the member.', 'the-board'),
					'id'		=> $prefix . 'skype',
					'type'		=> 'text',
					'context'	=> 'normal',
					'priority'	=> 'low'
				),
			array(
					'label'		=> __('Phone', 'the-board'),
					'desc'		=> __('Phone number of the member.', 'the-board'),
					'id'		=> $prefix . 'phone',
					'type'		=> 'tel',
					'context'	=> 'normal',
					'priority'	=> 'low'
				),
			array(
					'label'		=> __('Photo', 'the-board'),
					'desc'		=> __('A nice picture of the member.', 'the-board'),
					'id'		=> $prefix . 'photo',
					'type'		=> 'image',
					'context'	=> 'normal',
					'priority'	=> 'default'
				),
			array(
					'label'		=> __('Custom field', 'the-board'),
					'desc'		=> __('Whatever you think will be useful to know about the member.', 'the-board'),
					'id'		=> $prefix . 'custom',
					'type'		=> 'custom',
					'context'	=> 'normal',
					'priority'	=> 'low'
				),
			array(
					'label'		=> __('Hierarchy', 'the-board'),
					'desc'		=> __('0 being top level, how high is the member in his group ?', 'the-board'),
					'id'		=> $prefix . 'hierarchy',
					'type'		=> 'text',
					'context'	=> 'side',
					'priority'	=> 'default'
				)
			);
	}

	/**
	 * Return an instance of this class.
	 *
	 * @since     1.0.0
	 *
	 * @return    object    A single instance of this class.
	 */
	public static function get_instance() {

		// If the single instance hasn't been set, set it now.
		if ( null == self::$instance ) {
			self::$instance = new self;
		}

		return self::$instance;
	}

	/**
	 * Fired when the plugin is activated.
	 *
	 * @since    1.0.0
	 *
	 * @param    boolean    $network_wide    True if WPMU superadmin uses
	 *                                       "Network Activate" action, false if
	 *                                       WPMU is disabled or plugin is
	 *                                       activated on an individual blog.
	 */
	public static function activate( $network_wide ) {

		if ( function_exists( 'is_multisite' ) && is_multisite() ) {

			if ( $network_wide  ) {

				// Get all blog ids
				$blog_ids = self::get_blog_ids();

				foreach ( $blog_ids as $blog_id ) {

					switch_to_blog( $blog_id );
					self::single_activate();

					restore_current_blog();
				}

			} else {
				self::single_activate();
			}

		} else {
			self::single_activate();
		}

	}

	/**
	 * Fired when the plugin is deactivated.
	 *
	 * @since    1.0.0
	 *
	 * @param    boolean    $network_wide    True if WPMU superadmin uses
	 *                                       "Network Deactivate" action, false if
	 *                                       WPMU is disabled or plugin is
	 *                                       deactivated on an individual blog.
	 */
	public static function deactivate( $network_wide ) {

		if ( function_exists( 'is_multisite' ) && is_multisite() ) {

			if ( $network_wide ) {

				// Get all blog ids
				$blog_ids = self::get_blog_ids();

				foreach ( $blog_ids as $blog_id ) {

					switch_to_blog( $blog_id );
					self::single_deactivate();

					restore_current_blog();

				}

			} else {
				self::single_deactivate();
			}

		} else {
			self::single_deactivate();
		}

	}

	/**
	 * Fired when a new site is activated with a WPMU environment.
	 *
	 * @since    1.0.0
	 *
	 * @param    int    $blog_id    ID of the new blog.
	 */
	public function activate_new_site( $blog_id ) {

		if ( 1 !== did_action( 'wpmu_new_blog' ) ) {
			return;
		}

		switch_to_blog( $blog_id );
		self::single_activate();
		restore_current_blog();

	}

	/**
	 * Get all blog ids of blogs in the current network that are:
	 * - not archived
	 * - not spam
	 * - not deleted
	 *
	 * @since    1.0.0
	 *
	 * @return   array|false    The blog ids, false if no matches.
	 */
	private static function get_blog_ids() {

		global $wpdb;

		// get an array of blog ids
		$sql = "SELECT blog_id FROM $wpdb->blogs
			WHERE archived = '0' AND spam = '0'
			AND deleted = '0'";

		return $wpdb->get_col( $sql );

	}

	/**
	 * Fired for each blog when the plugin is activated.
	 *
	 * @since    1.0.0
	 */
	private static function single_activate() {
		// @theboard: Define activation functionality here
	}

	/**
	 * Fired for each blog when the plugin is deactivated.
	 *
	 * @since    1.0.0
	 */
	private static function single_deactivate() {
		// @theboard: Define deactivation functionality here
	}

	/**
	 * Load the plugin text domain for translation.
	 *
	 * @since    1.0.0
	 */
	public function load_plugin_textdomain() {

		$domain = $this->plugin_slug;
		$locale = apply_filters( 'plugin_locale', get_locale(), $domain );

		load_textdomain( $domain, trailingslashit( WP_LANG_DIR ) . $domain . '/' . $domain . '-' . $locale . '.mo' );
		load_plugin_textdomain( $domain, FALSE, basename( plugin_dir_path( dirname( __FILE__ ) ) ) . '/languages/' );
	}

	/**
	 * Register and enqueue public-facing style sheet.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_styles() {
		wp_enqueue_style( $this->plugin_slug . '-plugin-styles', plugins_url( 'assets/css/public.css', __FILE__ ), array(), self::VERSION );
	}

	/**
	 * Register and enqueues public-facing JavaScript files.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_scripts() {
		wp_enqueue_script( $this->plugin_slug . '-plugin-script', plugins_url( 'assets/js/public.js', __FILE__ ), array(  ), self::VERSION );
	}

	/**
	 * NOTE:  Actions are points in the execution of a page or process
	 *        lifecycle that WordPress fires.
	 *
	 *        Actions:    http://codex.wordpress.org/Plugin_API#Actions
	 *        Reference:  http://codex.wordpress.org/Plugin_API/Action_Reference
	 *
	 * @since    1.0.0
	 */

	public function tb_get_terms(){
		return $terms = array(
			'Direction' => array(),
			'Pôle levée de fonds et communication' => array(
				'Mécénat / Partenariat / Personnalités',
				'Legs, donations et assurances-vies',
				'Communication',
				'événements'
			),
			'Pôle Administratif et financier / Accompagnement des familles / recherche' => array(
				'Administratif et financier',
				'Accompagnement des familles',
				'Recherche'
			)
		);

		// Need to take a serious look at this. Probably a hook problem
		// $terms = get_terms("groups", array( 'hide_empty' => 0 ));
		// if ( !empty( $terms ) && !is_wp_error( $terms ) ){
		// 	foreach ( $terms as $term ) {
		// 		$return .= $term->name;
		// 	}
		// }
	}

	public function tb_get_member_shortcode(){
		add_shortcode( 'theboard-show-member', array($this, 'tb_get_one_member') );
		add_shortcode( 'theboard-show-group', array($this, 'tb_get_members_by_group') );
		add_shortcode( 'theboard-static-page', array($this, 'tb_get_all_members') );
	}

	public function tb_get_all_members(){
		ob_start();
		include('views/static-fond.php');
		$return .= ob_get_contents();
		ob_end_clean();
		return $return;
	}

	public function tb_get_members_by_group($atts) {
		$return = null;

		extract(shortcode_atts( array(
			'group'	=> ''
		), $atts, 'theboard-show-group' ) );


		$path = $this->tb_check_path('group');

		$terms = $this->tb_get_terms();
		$group_match = $terms[$group];

		ob_start();
		include( $path );
		$return .= ob_get_contents();
		ob_end_clean();

		return $return;
	}

	public function tb_get_one_member($atts) {
		$return = null;

		extract(shortcode_atts( array(
			'id'	=> 0
		), $atts, 'theboard-show-member' ) );

		$path = $this->tb_check_path('member');

		$tb_member_query = null;
		$tb_member_query = new WP_Query( 'post_type=member&p='.$id );

		if( $tb_member_query->have_posts() ) {
			while( $tb_member_query->have_posts() ){
				$tb_member_query->the_post();

				$postmeta = get_post_meta( get_the_ID() );
				ob_start();
				include( $path );
				$return .= ob_get_contents();
				ob_end_clean();
			}
		}

		wp_reset_postdata();

		return $return;
	}

	public function tb_check_path($tb_shortcode_slug){
		$user_theme_template = get_bloginfo("stylesheet_directory") . "/plugins/the-board/templates/".$tb_shortcode_slug;

		if( file_exists($user_theme_template."/styles.css") ){
			// wp_enqueue_style('user-css', $user_theme_template."/styles.css");
			// wp_deregister_style( $this->plugin_slug . '-plugin-style' );
		}

		if( file_exists($user_theme_template."/scripts.js") ){
			// wp_enqueue_script('user-js', $user_theme_template."/scripts.js");
			// wp_deregister_script( $this->plugin_slug . '-plugin-script' );
		}

		if( file_exists($user_theme_template."/".$tb_shortcode_slug.".php") )
			return $path = $user_theme_template."/".$tb_shortcode_slug.".php";
		else
			return $path = plugin_dir_path( __FILE__ ) . 'views/'.$tb_shortcode_slug.'.php';

		if( !isset($path) || !file_exists($path))
			return __('No template found. Sorry.', $this->plugin_slug);
	}

	/**
	 * NOTE:  Filters are points of execution in which WordPress modifies data
	 *        before saving it or sending it to the browser.
	 *
	 *        Filters: http://codex.wordpress.org/Plugin_API#Filters
	 *        Reference:  http://codex.wordpress.org/Plugin_API/Filter_Reference
	 *
	 * @since    1.0.0
	 */
	public function filter_method_name() {
		// @theboard: Define your filter hook callback here
	}

}
