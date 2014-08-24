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

		$this->tb_fields = $plugin->get_fields();

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

		add_action( 'plugins_loaded', array( $this, 'tb_language_call' ), 1);

		add_action( 'add_meta_boxes', array( $this, 'tb_metaboxes_init') );
		add_action( 'save_post', array( $this, 'tb_metaboxes_save_datas'), 0, 1 );

		add_action( 'init', array( $this, 'tb_register_sizes'), 0, 1 );
		// add_action( 'contextual_help', 'member_contextual_help', 10, 3 );
		add_filter( 'post_updated_messages', array( $this, 'tb_update_message' ) );

	}

	public function tb_language_call() {
		load_plugin_textdomain($this->plugin_slug, false, basename(plugin_dir_path( dirname( __FILE__ ) ) ) . '/languages/');
		//error_log(basename( plugin_dir_path( dirname( __FILE__ ) ) ) . '/languages/');
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

		if ( ! isset( $this->plugin_slug ) ) {
			return;
		}


		$screen = get_current_screen();
		if ( $screen->id == 'member' ) {
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

		if ( ! isset( $this->plugin_slug ) ) {
			return;
		}

		$screen = get_current_screen();
		if ( $screen->id == 'member' ) {
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
			'name'               => __( 'Members', $this->plugin_slug),
			'singular_name'      => __( 'Member', $this->plugin_slug),
			'add_new'            => __( 'Add New', $this->plugin_slug),
			'add_new_item'       => __( 'Add New Member', $this->plugin_slug),
			'edit_item'          => __( 'Edit Member', $this->plugin_slug),
			'new_item'           => __( 'New Member', $this->plugin_slug),
			'all_items'          => __( 'All Members', $this->plugin_slug),
			'view_item'          => __( 'View Member', $this->plugin_slug),
			'search_items'       => __( 'Search Members', $this->plugin_slug),
			'not_found'          => __( 'No members found', $this->plugin_slug),
			'not_found_in_trash' => __( 'No members found in the Trash', $this->plugin_slug),
			'parent_item_colon'  => '',
			'menu_name'          => 'Members'
		);
		$args = array(
			'labels'        => $labels,
			'description'   => __('Structure members', $this->plugin_slug),
			'public'        => true,
			'supports'      => false,
			'has_archive'   => true,
			'taxonomy'		=> 'groups',
			'menu_icon'		=> 'dashicons-groups'
		);
		register_post_type( 'member', $args );
	}

	public function tb_register_sizes() {
		add_image_size( 'tb_crop-256', 256, 256, true );
		add_image_size( 'tb_width-120', 120);
		add_image_size( 'tb_width-640', 640 );
	}

	public function tb_metaboxes_init() {
		foreach ($this->tb_fields as $field) {
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
		$meta_value = get_post_meta( $post->ID, $field['id'], true );
		$meta_hide = get_post_meta( $post->ID, 'hideit_' . $field['id'], true );
		?>
			<input type="hidden" name="<?php echo 'tb_mb_nonce_' . $field['id']; ?>" value="<?php echo wp_create_nonce( basename(__FILE__) ); ?>">
			<p class="howto"><?php echo $field['desc']; ?></p>
		<?php
		switch ( $field['type'] ) {
			case 'text':
				?>
					<input type="text" name="<?php echo $field['id']; ?>" id="<?php echo $field['id'] . '_input'; ?>" value="<?php echo $meta_value; ?>">
				<?php
				break;
			case 'checkbox':
				?>
					<label><input type="checkbox" name="<?php echo $field['id']; ?>" id="<?php echo $field['id'] . '_input'; ?>" value="<?php echo $meta_value; ?>">Invert</label>
				<?php
				break;
			case 'email':
				?>
					<input type="email" name="<?php echo $field['id']; ?>" id="<?php echo $field['id'] . '_input'; ?>" value="<?php echo $meta_value; ?>">
				<?php
				break;
			case 'tel':
				?>
					<input type="tel" name="<?php echo $field['id']; ?>" id="<?php echo $field['id'] . '_input'; ?>" value="<?php echo $meta_value; ?>">
				<?php
				break;
			case 'image':
				wp_enqueue_media();
				?>
					<input type="text" name="<?php echo $field['id']; ?>" id="<?php echo $field['id'] . '_input'; ?>" value="<?php echo $meta_value; ?>">
					<input type="button" value="<?php echo __('Upload Image', $this->plugin_slug); ?>" class="button" id="tb_image_uploader_button">
				<?php
				break;
			case 'custom':
				?>
					<input type="text" name="<?php echo $field['id']; ?>" id="<?php echo $field['id'] . '_input'; ?>" value="<?php echo $meta_value; ?>">
				<?php
				break;
			default:
				?>
					<input type="text" name="<?php echo $field['id']; ?>" id="<?php echo $field['id'] . '_input'; ?>" value="<?php echo $meta_value; ?>">
				<?php
				break;
		}
		if($field['type'] != 'checkbox') {
			?>
				<p>
					<label class="selectit"><input type="checkbox" name="<?php echo 'hideit_' . $field['id']; ?>" <?php if(!empty($meta_hide)) echo 'checked'; ?>>Hide this information</label>
				</p>
			<?php
		}
	}

	public function tb_metaboxes_save_datas(){
		global $post;

		if( !wp_is_post_revision( $post->ID ) ){
			$old_title = $_POST['post_title'];

			$new_ln = $_POST['tb_lastname'];
			$new_fn = $_POST['tb_firstname'];

			$old_ln = get_post_meta( $post->ID, 'tb_lastname', true );
			$old_fn = get_post_meta( $post->ID, 'tb_firstname', true );

			if( $new_ln != $old_ln || $new_fn != $old_fn ){
				if( !empty($new_ln) && !empty($new_fn) ) {
					$_POST['post_title'] = $new_ln . ' ' . $new_fn;
				} elseif ( !empty($new_fn) ) {
					$_POST['post_title'] = $new_fn;
				} elseif ( !empty($new_ln) ) {
					$_POST['post_title'] = $new_ln;
				} else {
					$_POST['post_title'] = __('John Doe (name not provided)', $this->plugin_slug);
				}
			} elseif ( empty($_POST['post_title']) ) {
				$_POST['post_title'] = __('John Doe (name not provided)', $this->plugin_slug);
				// wp_die($new_fn.' vs. '.$old_fn.'<br>'.$new_ln.' vs. '.$old_ln.'<br>'.$_POST['post_title']);
			}
			// We need to remove and recall save_post action in order to avoid an infinite loop.
			// See http://codex.wordpress.org/Function_Reference/wp_update_post for more details
			if( $_POST['post_title'] !== $old_title ){
				remove_action( 'save_post', array($this, 'tb_metaboxes_save_datas') );
				wp_update_post( $_POST );
				add_action( 'save_post', array($this, 'tb_metaboxes_save_datas') );
			}
		}

		foreach ($this->tb_fields as $field) {
			if(isset($_POST['tb_mb_nonce_' . $field['id']])){
				if(!wp_verify_nonce( $_POST['tb_mb_nonce_' . $field['id']], basename(__FILE__)) )
					return;
			} else {
				return;
			}
			if(defined('DOING_AUTOSAVE') && DOING_AUTOSAVE)
				return;
			if('member' !== $_POST['post_type'])
				return;
			if(!current_user_can('edit_page', $post->ID))
				return;
			elseif(!current_user_can('edit_post', $post->ID))
				return;

			$old_content = get_post_meta( $post->ID, $field['id'], true );
			$new_content = $_POST[$field['id']];

			if($new_content && $new_content != $old_content)
				update_post_meta( $post->ID, $field['id'], $new_content );
			elseif('' == $new_content && $old_content)
				delete_post_meta( $post->ID, $field['id'], $old_content );

			$old_hidden = get_post_meta( $post->ID, 'hideit_' . $field['id'], true );
			$new_hidden = $_POST['hideit_' . $field['id']];


			if(isset($new_hidden) && $new_hidden != '')
				update_post_meta( $post->ID, 'hideit_' . $field['id'], $new_hidden );
			elseif($new_hidden == '' && $old_hidden)
				delete_post_meta( $post->ID, 'hideit_' . $field['id'], $old_hidden );
		}
	}

	public function tb_member_groups_taxonomies_init() {
		$labels = array(
			'name'              => _x( 'Groups', 'taxonomy general name', $this->plugin_slug),
			'singular_name'     => _x( 'Group', 'taxonomy singular name', $this->plugin_slug),
			'search_items'      => __( 'Search Groups', $this->plugin_slug),
			'all_items'         => __( 'All Groups', $this->plugin_slug),
			'parent_item'       => __( 'Parent Group', $this->plugin_slug),
			'parent_item_colon' => __( 'Parent Group:', $this->plugin_slug),
			'edit_item'         => __( 'Edit Group', $this->plugin_slug),
			'update_item'       => __( 'Update Group', $this->plugin_slug),
			'add_new_item'      => __( 'Add New Group', $this->plugin_slug),
			'new_item_name'     => __( 'New Group Name', $this->plugin_slug),
			'menu_name'         => __( 'Groups', $this->plugin_slug)
		);

		$args = array(
			'hierarchical'      => true,
			'labels'            => $labels,
			'show_ui'           => true,
			'show_admin_column' => true,
			'query_var'         => true,
			'rewrite'           => array( 'slug' => 'group' )
		);

		register_taxonomy( 'groups', array( 'member' ), $args );
	}

	public function tb_update_message(){
		// See http://wp-bytes.com/function/2013/02/changing-post-updated-messages/
		global $post, $post_ID;
		$posttype = get_post_type( $post_ID );

		$obj = get_post_type_object( $posttype );
		$singular = $obj->labels->singular_name;

		$messages[$posttype] = array(
			0 	=> '', // Unused. Messages start at index 1.
			1 	=> sprintf( __($singular.' updated. <a href="%s">View '.strtolower($singular).'</a>'), esc_url( get_permalink($post_ID) ) ),
			2 	=> __('Custom field updated.'),
			3 	=> __('Custom field deleted.'),
			4 	=> __($singular.' updated.'),
			5 	=> isset($_GET['revision']) ? sprintf( __($singular.' restored to revision from %s'), wp_post_revision_title( (int) $_GET['revision'], false ) ) : false,
			6 	=> sprintf( __($singular.' published. <a href="%s">View '.strtolower($singular).'</a>'), esc_url( get_permalink($post_ID) ) ),
			7 	=> __('Page saved.'),
			8 	=> sprintf( __($singular.' submitted. <a target="_blank" href="%s">Preview '.strtolower($singular).'</a>'), esc_url( add_query_arg( 'preview', 'true', get_permalink($post_ID) ) ) ),
			9 	=> sprintf( __($singular.' scheduled for: <strong>%1$s</strong>. <a target="_blank" href="%2$s">Preview '.strtolower($singular).'</a>'), date_i18n( __( 'M j, Y @ G:i' ), strtotime( $post->post_date ) ), esc_url( get_permalink($post_ID) ) ),
			10 	=> sprintf( __($singular.' draft updated. <a target="_blank" href="%s">Preview '.strtolower($singular).'</a>'), esc_url( add_query_arg( 'preview', 'true', get_permalink($post_ID) ) ) ),
		);
		return $messages;
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
