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
    $this->tb_language_call();
    global $wpdb;
    $init_query = $wpdb->query("SHOW COLUMNS FROM $wpdb->terms LIKE 'term_order'");
    if ($init_query == 0) {	$wpdb->query("ALTER TABLE $wpdb->terms ADD `term_order` INT( 4 ) NULL DEFAULT '0'"); }
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
		$this->tb_fields_groups = $plugin->get_fields_groups();
    $this->tb_fields = $plugin->get_fields();
    $this->to_fields_total = $this->tb_fields;
    foreach ($this->tb_fields_groups as $field_group) {
      foreach ($field_group['fields'] as $field){
        array_push($this->to_fields_total, $field);
      }
    }
		// Load admin style sheet and JavaScript.
		add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_admin_styles' ) );
		add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_admin_scripts' ) );

		// Add the options page and menu item.
		// add_action( 'admin_menu', array( $this, 'add_plugin_admin_menu' ) );

		// Add an action link pointing to the options page.
		$plugin_basename = plugin_basename( plugin_dir_path( realpath( dirname( __FILE__ ) ) ) . $this->plugin_slug . '.php' );
		add_filter( 'after_setup_theme' . $plugin_basename, array( $this, 'add_action_links' ) );

		/*
		 * Define custom functionality.
		 *
		 * Read more about actions and filters:
		 * http://codex.wordpress.org/Plugin_API#Hooks.2C_Actions_and_Filters
		 */


		add_action( 'add_meta_boxes', array( $this, 'tb_metaboxes_init') );

		add_action( 'save_post', array( $this, 'tb_metaboxes_save_datas'), 0, 1 );

		add_action( 'init', array( $this, 'tb_register_sizes'), 0, 1 );
		// add_action( 'contextual_help', 'member_contextual_help', 10, 3 );
		add_filter( 'post_updated_messages', array( $this, 'tb_update_message' ) );

    add_action('edit_form_top', array( $this, 'shortcode_on_the_top'));
	}


  public function shortcode_on_the_top( $post )
  {
    if ($post->post_type == 'member'){
      echo '<div id="member-shortcode-holder">';
      echo '<h3 id="member-shortcode-title"><span>'. __('Use the shortcode below to display this member', MEMBERS_PLUGIN_BASENAME).'</span></h3>';
      echo "<input type='text' id='member-shortcode'"." value='[theboard-show-member id=".$post->ID."]' readonly>";
      echo '</div>';
    }
  }

	public function tb_language_call() {
		load_plugin_textdomain(The_Board::get_instance()->get_plugin_slug(), false, basename(plugin_dir_path( dirname( __FILE__ ) ) ) . '/languages/');
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
			wp_enqueue_style( $this->plugin_slug .'-chosen-styles', plugins_url( 'assets/css/chosen.min.css', __FILE__ ), array(), The_Board::VERSION );
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
        if ( $screen->id == 'member' || $screen->id == 'edit-member' ) {
			wp_enqueue_script( $this->plugin_slug . '-chosen', plugins_url( 'assets/js/chosen.jquery.min.js', __FILE__ ), array( 'jquery' ), The_Board::VERSION );
			wp_enqueue_script( $this->plugin_slug . '-admin-script', plugins_url( 'assets/js/admin.js', __FILE__ ), array( 'jquery',$this->plugin_slug . '-chosen' ), The_Board::VERSION );
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
				'settings' => '<a href="' . admin_url( 'options-general.php?page=' . $this->plugin_slug ) . '">' . __( 'Settings', MEMBERS_PLUGIN_BASENAME ) . '</a>'
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


	public function tb_register_sizes() {
		add_image_size( 'tb_crop-120', 120, 120, true );
		add_image_size( 'tb_crop-256', 256, 256, true );
		add_image_size( 'tb_width-120', 120);
		add_image_size( 'tb_width-640', 640 );
	}

	public function tb_metaboxes_init() {
		foreach ($this->tb_fields_groups as $field_group) {
      add_meta_box(
          $field_group['id'],
          $field_group['label'],
          array( $this, 'tb_show_metabox' ),
          'member',
          $field_group['context'],
          $field_group['priority'],
          $field_group
      );
    }
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
    if (isset($metabox['args']['fields'])){
      $fields = $metabox['args']['fields'];
    }
    else{
      $fields = array($metabox['args']);
    }

    foreach ($fields as $field){
      $meta_value = get_post_meta( $post->ID, $field['id'], true );
      $meta_hide = get_post_meta( $post->ID, 'hideit_' . $field['id'], true );

      if('tb_hierarchy' == $field['id'] && !is_numeric($meta_value) )
        $meta_value = 0;
      ?>
      <div class="tb_field <?php echo $field['id']?>-container">
        <?php
        if (isset($metabox['args']['fields'])){
          ?>
          <div class="<?php echo $field['id']?>-icon"></div>
          <h4 class="<?php echo $field['id']?>-title"><?php echo $field['label'];?></h4>
        <?php
        }
        ?>
        <input type="hidden" name="<?php echo 'tb_mb_nonce_' . $field['id']; ?>" value="<?php echo wp_create_nonce( basename(__FILE__) ); ?>">
        <?php
        switch ( $field['type'] ) {
          case 'text':
            ?>
            <input type="text" name="<?php echo $field['id']; ?>" id="<?php echo $field['id'] . '_input'; ?>" value="<?php echo $meta_value; ?>" >
            <?php
            break;
          case 'lastname':
            ?>
            <input type="text" name="<?php echo $field['id']; ?>" id="<?php echo $field['id'] . '_input'; ?>" value="<?php echo $meta_value; ?>" >
            <input type="text" name="post_title" id="<?php echo $field['id'] . '_input'; ?>" value="<?php echo $meta_value; ?>" hidden>
            <?php
            break;
          case 'number':
            ?>
            <input type="number" name="<?php echo $field['id']; ?>" id="<?php echo $field['id'] . '_input'; ?>" value="<?php echo $meta_value; ?>" <?php if('tb_hierarchy' == $field['id']) echo 'required'; ?> min="0" >
            <?php
            break;
          case 'checkbox':
            ?>
            <label><input type="checkbox" name="<?php echo $field['id']; ?>" id="<?php echo $field['id'] . '_input'; ?>" <?php if(!empty($meta_value)) echo 'checked'; ?> >Invert</label>
            <?php
            break;
          case 'email':
            ?>
            <input type="email" name="<?php echo $field['id']; ?>" id="<?php echo $field['id'] . '_input'; ?>" value="<?php echo $meta_value; ?>">
            <?php
            break;
          case 'contact':
            $args = array (
                'post_type'              => 'wpcf7_contact_form',
                'posts_per_page'         => '-1',
                'order'									 => 'desc'
            );
            $contactForms = get_posts( $args );
            $isempty = empty($meta_value) ? 'selected' : null;
            ?>
            <?php
            if ( count($contactForms) > 0 ) {?>
              <select name="<?php echo $field['id']; ?>" id="<?php echo $field['id'] . '_input_list'; ?>" class="chosen-select">
                <option disabled <?php echo $isempty; ?> ><?php _e('Choose a contact in the list below', MEMBERS_PLUGIN_BASENAME); ?></option>
                <option value=""><?php _e('None', MEMBERS_PLUGIN_BASENAME); ?></option>
                <?php
                foreach ( $contactForms as $contactForm ) : setup_postdata( $contactForm );
                  $selected = $contactForm->ID == $meta_value ? 'selected' : null;
                  echo '<option value="'.$contactForm->ID.'" '.$selected.' >' . $contactForm->post_title . '</option>';
                endforeach;
                wp_reset_postdata();
                ?>
                Contact Form
              </select>
            <?php
            } else {
              $url = 'http://wordpress.org/plugins/contact-form-7/';?>
              <p class="howto"><?php printf(__('We recommend you use Contact form 7. Never heard of it ? Check it out <a href="%s" target="%s">here</a>', MEMBERS_PLUGIN_BASENAME), esc_url( $url ), '_blank'); ?></p>
              <?php
            }
            ?>
            <?php
            break;
          case 'tel':
            ?>
            <input type="tel" name="<?php echo $field['id']; ?>" id="<?php echo $field['id'] . '_input'; ?>" value="<?php echo $meta_value; ?>">
            <?php
            break;
          case 'image':
            wp_enqueue_media();
            if ($meta_value!=''){
              ?>
              <input type="button" value="<?php echo __('Upload Image', MEMBERS_PLUGIN_BASENAME); ?>" class="button tb_image_uploader_button to-hide" style="display: none">
              <input type="text" name="<?php echo $field['id']; ?>" id="<?php echo $field['id'] . '_input'; ?>" value="<?php echo $meta_value; ?>" hidden>
              <div class="profile-photo-holder">
                <img id="profile_photo" src="<?php echo $meta_value; ?>" alt="Profile photo"/>
                <input type="button" value="<?php echo __('Upload Image', MEMBERS_PLUGIN_BASENAME); ?>" class="button upload-profile-photo tb_image_uploader_button">
                <div  class="tb_image_delete_button"></div>
              </div>
            <?php
            }
            else{
              ?>
              <input type="button" value="<?php echo __('Upload Image', MEMBERS_PLUGIN_BASENAME); ?>" class="button tb_image_uploader_button to-hide">
              <input type="text" name="<?php echo $field['id']; ?>" id="<?php echo $field['id'] . '_input'; ?>" value="<?php echo $meta_value; ?>" hidden>
              <div class="profile-photo-holder" style="display: none">
                <img id="profile_photo" src="<?php echo $meta_value; ?>" alt="Profile photo"/>
                <input type="button" value="<?php echo __('Upload Image', MEMBERS_PLUGIN_BASENAME); ?>" class="button upload-profile-photo tb_image_uploader_button">
                <div  class="tb_image_delete_button"></div>
              </div>
            <?php
            }
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
        } ?>
        <p class="howto"><?php echo $field['desc']; ?></p>
        <?php if($field['type'] != 'checkbox') {
          ?>
          <p>
            <label class="selectit"><input type="checkbox" name="<?php echo 'hideit_' . $field['id']; ?>" <?php if(!empty($meta_hide)) echo 'checked'; ?>><?php echo __('Hide this information', MEMBERS_PLUGIN_BASENAME);?></label>
          </p>
        <?php
        }

        ?>
      </div>
    <?php
    }
	}

	public function tb_metaboxes_save_datas(){
		global $post;

		if(!isset($post) || !isset($_POST['post_type']))
			return;

		if( !wp_is_post_revision( $post->ID ) ){
			if('member' !== $_POST['post_type'])
				return;
			if(!current_user_can('edit_page', $post->ID))
				return;
			elseif(!current_user_can('edit_post', $post->ID))
				return;

			$old_title = $_POST['post_title'];

			$new_ln = $_POST['tb_lastname'];
			$new_fn = $_POST['tb_firstname'];

			$old_ln = get_post_meta( $post->ID, 'tb_lastname', true );
			$old_fn = get_post_meta( $post->ID, 'tb_firstname', true );

			if( $new_ln != $old_ln || $new_fn != $old_fn || empty($_POST['post_title']) ){
				if( !empty($new_ln) && !empty($new_fn) ) {
					$_POST['post_title'] = $new_ln;
				} elseif ( !empty($new_fn) ) {
					$_POST['post_title'] = $new_fn;
				} elseif ( !empty($new_ln) ) {
					$_POST['post_title'] = $new_ln;
				} else {
					$_POST['post_title'] = __('John Doe (name not provided)', MEMBERS_PLUGIN_BASENAME);
				}
			}
			// We need to remove and recall save_post action in order to avoid an infinite loop.
			// See http://codex.wordpress.org/Function_Reference/wp_update_post for more details
			if( $_POST['post_title'] !== $old_title ){
				remove_action( 'save_post', array($this, 'tb_metaboxes_save_datas') );
				wp_update_post( $_POST );
				add_action( 'save_post', array($this, 'tb_metaboxes_save_datas') );
			}
		}

		foreach ($this->to_fields_total as $field) {
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
			$new_content = isset($_POST[$field['id']])? $_POST[$field['id']] : '';

			if( isset($new_content) && $new_content != $old_content)
				update_post_meta( $post->ID, $field['id'], $new_content );
			elseif('' == $new_content && $old_content)
				delete_post_meta( $post->ID, $field['id'], $old_content );

			if(isset($_POST['hideit_' . $field['id']])){
				$old_hidden = get_post_meta( $post->ID, 'hideit_' . $field['id'], true );
				$new_hidden = $_POST['hideit_' . $field['id']];

				if(isset($new_hidden) && $new_hidden != '')
					update_post_meta( $post->ID, 'hideit_' . $field['id'], $new_hidden );
				elseif($new_hidden == '' && $old_hidden)
					delete_post_meta( $post->ID, 'hideit_' . $field['id'], $old_hidden );
			}
		}
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
