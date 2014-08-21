<?php
/**
 * The Board.
 *
 * @package   The_Board_Admin
 * @author    Soixante circuits
 * @license   GPL-2.0+
 */

/**
 * Plugin class. This class should ideally be used to work with the
 * administrative side of the WordPress site.
 *
 * If you're interested in introducing public-facing
 * functionality, then refer to `the-board.php`
 *
 * @theboard
 *
 * @package The_Board_Admin
 * @author  Soixante circuits
 */
class The_Board_Admin {

	/**
	 * Instance of this class.
	 *
	 * @since    1.0.0
	 *
	 * @var      object
	 */
	protected static $instance = null;

	/**
	 * Slug of the plugin screen.
	 *
	 * @since    1.0.0
	 *
	 * @var      string
	 */
	protected $plugin_screen_hook_suffix = null;

	/**
	 * Initialize the plugin by loading admin scripts & styles and adding a
	 * settings page and menu.
	 *
	 * @since     1.0.0
	 */
	private function __construct() {

		/*
		 * @theboard :
		 *
		 * - Uncomment following lines if the admin class should only be available for super admins
		 */
		/* if( ! is_super_admin() ) {
			return;
		} */

		/*
		 * Call $plugin_slug from public plugin class.
		 *
		 * @theboard:
		 *
		 */
		$plugin = The_Board::get_instance();
		$this->plugin_slug = $plugin->get_plugin_slug();

		// Load admin style sheet and JavaScript.
		add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_admin_styles' ) );
		add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_admin_scripts' ) );

		// Add the options page and menu item.
		// add_action( 'admin_menu', array( $this, 'add_plugin_admin_menu' ) );

		// Add an action link pointing to the options page.
		$plugin_basename = plugin_basename( plugin_dir_path( realpath( dirname( __FILE__ ) ) ) . $this->plugin_slug . '.php' );
		add_filter( 'plugin_action_links_' . $plugin_basename, array( $this, 'add_action_links' ) );

		/*
		 * Define custom functionality.
		 *
		 * Read more about actions and filters:
		 * http://codex.wordpress.org/Plugin_API#Hooks.2C_Actions_and_Filters
		 */
		add_action( 'init', array( $this, 'tb_member_posttype_init' ), 0);
		add_action( 'init', array( $this, 'tb_member_groups_taxonomies_init' ), 1);
		add_action( 'add_meta_boxes', array( $this, 'tb_metaboxes_init') );
		add_action( 'save_post', array( $this, 'tb_metaboxes_save_datas'), 0, 1 );
		// add_action( 'contextual_help', 'member_contextual_help', 10, 3 );
		add_filter( '@theboard', array( $this, 'filter_method_name' ) );

	}

	/**
	 * Return an instance of this class.
	 *
	 * @since     1.0.0
	 *
	 * @return    object    A single instance of this class.
	 */
	public static function get_instance() {

		/*
		 * @theboard :
		 *
		 * - Uncomment following lines if the admin class should only be available for super admins
		 */
		/* if( ! is_super_admin() ) {
			return;
		} */

		// If the single instance hasn't been set, set it now.
		if ( null == self::$instance ) {
			self::$instance = new self;
		}

		return self::$instance;
	}

	/**
	 * Register and enqueue admin-specific style sheet.
	 *
	 * @theboard:
	 *
	 * - Rename "The_Board" to the name your plugin
	 *
	 * @since     1.0.0
	 *
	 * @return    null    Return early if no settings page is registered.
	 */
	public function enqueue_admin_styles() {

		if ( ! isset( $this->plugin_screen_hook_suffix ) ) {
			return;
		}

		$screen = get_current_screen();
		if ( $this->plugin_screen_hook_suffix == $screen->id ) {
			wp_enqueue_style( $this->plugin_slug .'-admin-styles', plugins_url( 'assets/css/admin.css', __FILE__ ), array(), The_Board::VERSION );
		}

	}

	/**
	 * Register and enqueue admin-specific JavaScript.
	 *
	 * @theboard:
	 *
	 * - Rename "The_Board" to the name your plugin
	 *
	 * @since     1.0.0
	 *
	 * @return    null    Return early if no settings page is registered.
	 */
	public function enqueue_admin_scripts() {

		if ( ! isset( $this->plugin_screen_hook_suffix ) ) {
			return;
		}

		$screen = get_current_screen();
		if ( $this->plugin_screen_hook_suffix == $screen->id ) {
			wp_enqueue_script( $this->plugin_slug . '-admin-script', plugins_url( 'assets/js/admin.js', __FILE__ ), array( 'jquery' ), The_Board::VERSION );
		}

	}

	/**
	 * Register the administration menu for this plugin into the WordPress Dashboard menu.
	 *
	 * @since    1.0.0
	 */
	public function add_plugin_admin_menu() {

		/*
		 * Add a settings page for this plugin to the Settings menu.
		 *
		 * NOTE:  Alternative menu locations are available via WordPress administration menu functions.
		 *
		 *        Administration Menus: http://codex.wordpress.org/Administration_Menus
		 *
		 * @theboard:
		 *
		 * - Change 'Page Title' to the title of your plugin admin page
		 * - Change 'Menu Text' to the text for menu item for the plugin settings page
		 * - Change 'manage_options' to the capability you see fit
		 *   For reference: http://codex.wordpress.org/Roles_and_Capabilities
		 */
		$this->plugin_screen_hook_suffix = add_options_page(
			__( 'The Board', $this->plugin_slug ),
			__( 'The Board', $this->plugin_slug ),
			'manage_options',
			$this->plugin_slug,
			array( $this, 'display_plugin_admin_page' )
		);

	}

	/**
	 * Render the settings page for this plugin.
	 *
	 * @since    1.0.0
	 */
	public function display_plugin_admin_page() {
		include_once( 'views/admin.php' );
	}

	/**
	 * Add settings action link to the plugins page.
	 *
	 * @since    1.0.0
	 */
	public function add_action_links( $links ) {

		return array_merge(
			array(
				'settings' => '<a href="' . admin_url( 'options-general.php?page=' . $this->plugin_slug ) . '">' . __( 'Settings', $this->plugin_slug ) . '</a>'
			),
			$links
		);

	}

	/**
	 * NOTE:     Actions are points in the execution of a page or process
	 *           lifecycle that WordPress fires.
	 *
	 *           Actions:    http://codex.wordpress.org/Plugin_API#Actions
	 *           Reference:  http://codex.wordpress.org/Plugin_API/Action_Reference
	 *
	 * @since    1.0.0
	 */
	public function tb_member_posttype_init() {
		// @theboard: Create the member post type
		$labels = array(
			'name'               => __( 'Members' ),
			'singular_name'      => __( 'Member' ),
			'add_new'            => __( 'Add New' ),
			'add_new_item'       => __( 'Add New Member' ),
			'edit_item'          => __( 'Edit Member' ),
			'new_item'           => __( 'New Member' ),
			'all_items'          => __( 'All Members' ),
			'view_item'          => __( 'View Member' ),
			'search_items'       => __( 'Search Members' ),
			'not_found'          => __( 'No members found' ),
			'not_found_in_trash' => __( 'No members found in the Trash' ),
			'parent_item_colon'  => '',
			'menu_name'          => 'Members'
		);
		$args = array(
			'labels'        => $labels,
			'description'   => __('Structure members'),
			'public'        => true,
			'supports'      => array( 'title' ),
			'has_archive'   => true,
			'taxonomy'		=> 'groups',
			'menu_icon'		=> 'dashicons-groups'
		);
		register_post_type( 'member', $args );
	}

	public function tb_member_groups_taxonomies_init() {

		$labels = array(
			'name'					=> __( 'Groups', 'text-domain' ),
			'singular_name'			=> __( 'Group', 'text-domain' ),
			'search_items'			=> __( 'Search Groups', 'text-domain' ),
			'popular_items'			=> __( 'Popular Groups', 'text-domain' ),
			'all_items'				=> __( 'All Groups', 'text-domain' ),
			'parent_item'			=> __( 'Parent Group', 'text-domain' ),
			'parent_item_colon'		=> __( 'Parent Group', 'text-domain' ),
			'edit_item'				=> __( 'Edit Group', 'text-domain' ),
			'update_item'			=> __( 'Update Group', 'text-domain' ),
			'add_new_item'			=> __( 'Add New Group', 'text-domain' ),
			'new_item_name'			=> __( 'New Group Name', 'text-domain' ),
			'add_or_remove_items'	=> __( 'Add or remove Groups', 'text-domain' ),
			'choose_from_most_used'	=> __( 'Choose from most used text-domain', 'text-domain' ),
			'menu_name'				=> __( 'Group', 'text-domain' )
		);

		$args = array(
			'hierarchical'      => true,
			'label' 			=> __( 'Groups','text-domain' ),
			'labels'            => $labels,
			'show_ui'           => true,
			'capabilities'		=> array(
					'manage_terms'	=> 'manage_categories',
					'edit_terms'	=> 'manage_categories',
					'delete_terms'	=> 'manage_categories',
					'assign_terms'	=> 'manage_categories'
				)
		);

		register_taxonomy( 'groups', 'member', $args );
		register_taxonomy_for_object_type( 'groups', 'member' );
	}

	// DELETE FOLLOWING FUNCTION

	// function add_members_caps_to_admin() {
	// 	$caps = array(
	// 		'read',
	// 		'read_members',
	// 		'read_private_members',
	// 		'edit_members',
	// 		'edit_private_members',
	// 		'edit_published_members',
	// 		'edit_others_members',
	// 		'publish_members',
	// 		'delete_members',
	// 		'delete_private_members',
	// 		'delete_published_members',
	// 		'delete_others_members',
	// 		'manage_categories'
	// 	);
	// 	$roles = array(
	// 		get_role( 'administrator' ),
	// 		get_role( 'editor' ),
	// 		get_role( 'contributor' ),
	// 		get_role( 'author' )
	// 	);
	// 	foreach ($roles as $role) {
	// 		foreach ($caps as $cap) {
	// 			$role->add_cap( $cap );
	// 		}
	// 	}
	// }


	public function tb_metaboxes_init() {
		$prefix = 'board_';
		$tb_fields = array(
			array(
					'label'		=> __('Lastname'),
					'desc'		=> __('Lastname of the member.'),
					'id'		=> $prefix . 'lastname',
					'type'		=> 'text',
					'context'	=> 'normal',
					'priority'	=> 'default'
				),
			array(
					'label'		=> __('Firstname'),
					'desc'		=> __('Firstname of the member.'),
					'id'		=> $prefix . 'firstname',
					'type'		=> 'text',
					'context'	=> 'normal',
					'priority'	=> 'default'
				),
			array(
					'label'		=> __('Post'),
					'desc'		=> __('Post occupied by the member.'),
					'id'		=> $prefix . 'post',
					'type'		=> 'text',
					'context'	=> 'normal',
					'priority'	=> 'default'
				),
			array(
					'label'		=> __('Invert in glossary'),
					'desc'		=> __('If this is checked, member will be sorted by its firstname. "John Smith" would be find at "John" (J) instead of "Smith" (S).'),
					'id'		=> $prefix . 'invert',
					'type'		=> 'checkbox',
					'context'	=> 'side',
					'priority'	=> 'low'
				),
			array(
					'label'		=> __('Email'),
					'desc'		=> __('Email of the member.'),
					'id'		=> $prefix . 'email',
					'type'		=> 'email',
					'context'	=> 'normal',
					'priority'	=> 'default'
				),
			array(
					'label'		=> __('Facebook'),
					'desc'		=> __('URL for the Facebook account of the member.'),
					'id'		=> $prefix . 'facebook',
					'type'		=> 'text',
					'context'	=> 'normal',
					'priority'	=> 'low'
				),
			array(
					'label'		=> __('Twitter'),
					'desc'		=> __('URL for the Twitter account of the member.'),
					'id'		=> $prefix . 'twitter',
					'type'		=> 'text',
					'context'	=> 'normal',
					'priority'	=> 'low'
				),
			array(
					'label'		=> __('Google+'),
					'desc'		=> __('URL for the Google+ account of the member.'),
					'id'		=> $prefix . 'googleplus',
					'type'		=> 'text',
					'context'	=> 'normal',
					'priority'	=> 'low'
				),
			array(
					'label'		=> __('LinkedIn'),
					'desc'		=> __('URL for the LinkedIn account of the member.'),
					'id'		=> $prefix . 'linkedIn',
					'type'		=> 'text',
					'context'	=> 'normal',
					'priority'	=> 'low'
				),
			array(
					'label'		=> __('Skype'),
					'desc'		=> __('URL for the Skype account of the member.'),
					'id'		=> $prefix . 'skype',
					'type'		=> 'text',
					'context'	=> 'normal',
					'priority'	=> 'low'
				),
			array(
					'label'		=> __('Phone'),
					'desc'		=> __('Phone number of the member.'),
					'id'		=> $prefix . 'phone',
					'type'		=> 'tel',
					'context'	=> 'normal',
					'priority'	=> 'low'
				),
			array(
					'label'		=> __('Photo'),
					'desc'		=> __('A nice picture of the member.'),
					'id'		=> $prefix . 'photo',
					'type'		=> 'image',
					'context'	=> 'normal',
					'priority'	=> 'default'
				),
			array(
					'label'		=> __('Custom field'),
					'desc'		=> __('Whatever you think will be useful to know about the member.'),
					'id'		=> $prefix . 'custom',
					'type'		=> 'custom',
					'context'	=> 'normal',
					'priority'	=> 'low'
				)
			);
		foreach ($tb_fields as $field) {
			add_meta_box(
				$field['id'],
				$field['label'],
				array( $this, 'tb_show_metabox' ),
				'member',
				$field['context'],
				$field['priority'],
				$field
			);
		}
	}


	public function tb_show_metabox($post,  $metabox ) {
		$field = $metabox['args'];
		?>
			<input type="hidden" name="<?php echo 'tb_mb_nonce_' . $field['id']; ?>" value="<?php echo wp_create_nonce( basename(__FILE__) ); ?>">
			<p class="howto"><?php echo $field['desc']; ?></p>
		<?php
		switch ( $field['type'] ) {
			case 'text':
				?>
					<input type="text" name="<?php echo $field['id']; ?>" value="<?php // put stored value ?>">
				<?php
				break;
			case 'checkbox':
				?>
					<label><input type="checkbox" name="<?php echo $field['id']; ?>" value="<?php // put stored value ?>">Invert</label>
				<?php
				break;
			case 'email':
				?>
					<input type="email" name="<?php echo $field['id']; ?>" value="<?php // put stored value ?>">
				<?php
				break;
			case 'tel':
				?>
					<input type="tel" name="<?php echo $field['id']; ?>" value="<?php // put stored value ?>">
				<?php
				break;
			case 'image':
				?>
					<input type="file" name="<?php echo $field['id']; ?>" value="<?php // put stored value ?>" accept="image/*">
					<p class="howto"><?php echo __('Extesions accepted are: .jpg, .jpeg, .png and .gif') ?></p>
				<?php
				break;
			case 'custom':
				?>
					<input type="text" name="<?php echo $field['id']; ?>" value="<?php // put stored value ?>">
				<?php
				break;
			default:
				?>
					<input type="text" name="<?php echo $field['id']; ?>" value="<?php // put stored value ?>">
				<?php
				break;
		}
		?>
			<p>
				<label class="selectit"><input type="checkbox" name="<?php echo 'hideit_' . $field['id']; ?>" <?php echo ''; // put stored value ?>>Hide this information</label>
			</p>
		<?php
	}

	public function tb_metaboxes_save_datas(){
		global $post;

		// die(print_r($_POST));
		// die(print_r($tb_fields));

		foreach ($tb_fields as $field) {
			if(isset($_POST['tb_mb_nonce_' . $field['id']])){
				if(!wp_verify_nonce( $_POST['tb_mb_nonce_' . $field['id']], basename(__FILE__)) )
					return;
			} else {
				return;
			}
			if(defined('DOING_AUTOSAVE') && DOING_AUTOSAVE)
				return;
			if('page' !== $_POST['post_type'])
				return;
			if(!current_user_can('edit_page', $post->ID))
				return;
			elseif(!current_user_can('edit_post', $post->ID))
				return;

			$old = get_post_meta( $post->ID, $field['id'], true );
			$new = $_POST[$field['id']];
			if($new && $new != $old)
				update_post_meta( $post->ID, $field['id'], $new );
			elseif('' == $new && $old) {
				delete_post_meta( $post->ID, $field['id'], $old );
			}
		}
	}

	/**
	 * @TODO:	Calling this returns a
	 * 			' Warning: call_user_func_array() expects parameter 1 to be a valid callback, function 'member_contextual_help' not found or invalid function name in /home/debian/sources/apache/wordpress/soixante-laboratory/wp-includes/plugin.php on line 192'
	 *
	 */
	public function member_contextual_help( $contextual_help, $screen_id, $screen ) {
	if ( 'member' == $screen->id ) {
		$contextual_help = include plugin_dir_path( __FILE__ ) . 'views/contextual-help.php';
	} elseif ( 'edit-member' == $screen->id ) {
		$contextual_help = include plugin_dir_path( __FILE__ ) . 'views/contextual-help-edit.php';
	}
		return $contextual_help;
	}

	/**
	 * NOTE:     Filters are points of execution in which WordPress modifies data
	 *           before saving it or sending it to the browser.
	 *
	 *           Filters: http://codex.wordpress.org/Plugin_API#Filters
	 *           Reference:  http://codex.wordpress.org/Plugin_API/Filter_Reference
	 *
	 * @since    1.0.0
	 */
	public function filter_method_name() {
		// @theboard: Define your filter hook callback here
	}

}
