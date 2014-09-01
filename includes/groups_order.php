<?php
function group_order_page() {
  $plugin = The_Board::get_instance();
  add_submenu_page('edit.php?post_type=member', __('Groups Order', $plugin->get_plugin_slug()), __('Groups Order', $plugin->get_plugin_slug()), 'manage_categories', 'order-groups', 'groups_order');
}

function group_order_css() {
  if(isset($_GET['page'])){
    $pos_page = $_GET['page'];
    $pos_args = 'order-groups';
    $pos = strpos($pos_page, $pos_args);
    if ( $pos === false ) {} else {
      wp_enqueue_style('order-page', plugins_url('css/order-page.css', __FILE__), 'screen');
    }
  }
}
function group_order_js_libs() {
  if(isset($_GET['page'])){
    $pos_page = $_GET['page'];
    $pos_args = 'order-groups';
    $pos = strpos($pos_page,$pos_args);
    if ( $pos === false ) {} else {
      wp_enqueue_script('jquery');
      wp_enqueue_script('jquery-ui-core');
      wp_enqueue_script('jquery-ui-sortable');
    }
  }
}

add_action('admin_menu', 'group_order_page');
add_action('admin_print_styles', 'group_order_css');
add_action('admin_print_scripts', 'group_order_js_libs');

function groups_order(){
  $plugin = The_Board::get_instance();
  $parent_ID = 0;
  $tax = 'groups';
  if (isset($_POST['go-sub-posts'])) {
    $parent_ID = intval($_POST['sub-posts']);
  }
  elseif (isset($_POST['hidden-parent-id'])) {
    $parent_ID = intval($_POST['hidden-parent-id']);
  }
  if (isset($_POST['return-sub-posts'])) {
    $parent_term = get_term($_POST['hidden-parent-id'], $tax);
    $parent_ID = intval($parent_term->parent);
  }
  $message = "";
  if (isset($_POST['order-submit'])) {
    group_order_update_order();
  }
  ?>
  <div class='wrap'>
    <?php screen_icon($plugin->get_plugin_slug()); ?>
    <h2><?php _e('Groups Order', $plugin->get_plugin_slug()); ?></h2>
    <form name="custom-order-form" method="post" action="">
      <?php
      $tax = 'groups';
      $args = array(
          'orderby' => 'term_order',
          'order' => 'ASC',
          'hide_empty' => false,
          'parent' => $parent_ID
      );
      $terms = get_terms( $tax, $args );
      if ( $terms ) {
        ?>
        <div id="poststuff" class="metabox-holder">
          <div class="widget order-widget">
            <h3 class="widget-top"><?php _e('Groups', $plugin->get_plugin_slug()) ?> | <small><?php _e('Order the groups by dragging and dropping them into the desired order.', $plugin->get_plugin_slug()) ?></small></h3>
            <div class="misc-pub-section">
              <ul id="custom-order-list">
                <?php foreach ( $terms as $term ) : ?>

                  <li id="id_<?php echo $term->term_id; ?>" class="lineitem"><?php echo $term->name; ?></li>
                <?php endforeach; ?>
              </ul>
            </div>
            <div class="misc-pub-section misc-pub-section-last">
              <?php if ($parent_ID != 0) { ?>
                <input type="submit" class="button" style="float:left" id="return-sub-posts" name="return-sub-posts" value="<?php _e('Return to Parent Group', $plugin->get_plugin_slug()); ?>" />
              <?php } ?>
              <div id="publishing-action">
                <img src="<?php echo esc_url( admin_url( 'images/wpspin_light.gif' ) ); ?>" id="custom-loading" style="display:none" alt="" />
                <input type="submit" name="order-submit" id="order-submit" class="button-primary" value="<?php _e('Update Order', $plugin->get_plugin_slug()) ?>" />
              </div>
              <div class="clear"></div>
            </div>
            <input type="hidden" id="hidden-custom-order" name="hidden-custom-order" />
            <input type="hidden" id="hidden-parent-id" name="hidden-parent-id" value="<?php echo $parent_ID; ?>" />
          </div>

          <?php $dropdown = group_order_sub_query( $terms, $tax ); if( !empty($dropdown) ) { ?>
            <div class="widget order-widget">
              <h3 class="widget-top"><?php _e('Sub-Groups', $plugin->get_plugin_slug()); ?> | <small><?php _e('Choose a group from the drop down to order its sub-groups.', $plugin->get_plugin_slug()); ?></small></h3>
              <div class="misc-pub-section misc-pub-section-last">
                <select id="sub-posts" name="sub-posts">
                  <?php echo $dropdown; ?>
                </select>
                <input type="submit" name="go-sub-posts" class="button" id="go-sub-posts" value="<?php _e('Order Sub-groups', $plugin->get_plugin_slug()) ?>" />
              </div>
            </div>
          <?php } ?>
        </div>
      <?php } else { ?>
        <p><?php _e('No terms found', $plugin->get_plugin_slug()); ?></p>
      <?php } ?>
    </form>

  </div>
  <?php if ( $terms ) { ?>
    <script type="text/javascript">
      // <![CDATA[
      jQuery(document).ready(function($) {
        $("#custom-loading").hide();
        $("#order-submit").click(function() {
          orderSubmit();
        });
      });
      function group_orderAddLoadEvent(){
        jQuery("#custom-order-list").sortable({
          placeholder: "sortable-placeholder",
          revert: false,
          tolerance: "pointer"
        });
      };
      addLoadEvent(group_orderAddLoadEvent);
      function orderSubmit() {
        var newOrder = jQuery("#custom-order-list").sortable("toArray");
        jQuery("#custom-loading").show();
        jQuery("#hidden-custom-order").val(newOrder);
        return true;
      }
      // ]]>
    </script>
  <?php }
}

function group_order_update_order() {
  $plugin = The_Board::get_instance();
  if (isset($_POST['hidden-custom-order']) && $_POST['hidden-custom-order'] != "") {
    global $wpdb;
    $new_order = $_POST['hidden-custom-order'];
    $IDs = explode(",", $new_order);
    $result = count($IDs);
    for($i = 0; $i < $result; $i++) {
      $str = str_replace("id_", "", $IDs[$i]);
      $wpdb->query("UPDATE $wpdb->terms SET term_order = '$i' WHERE term_id ='$str'");
    }
    echo '<div id="message" class="updated fade"><p>'. __('Order updated successfully.', $plugin->get_plugin_slug()).'</p></div>';
  } else {
    echo '<div id="message" class="error fade"><p>'. __('An error occured, order has not been saved.', $plugin->get_plugin_slug()).'</p></div>';
  }
}
function group_order_sub_query( $terms, $tax ) {
  $options = '';
  foreach ( $terms as $term ) :
    $subterms = get_term_children( $term->term_id, $tax );
    if ( $subterms ) {
      $options .= '<option value="' . $term->term_id . '">' . $term->name . '</option>';
    }
  endforeach;
  return $options;
}
function group_order_apply_order_filter($orderby, $args) {
  global $group_order_settings;
  $options = $group_order_settings;
  if ( isset( $args['taxonomy'] ) ) {
    $taxonomy = $args['taxonomy'];
  } else {
    $taxonomy = 'category';
  }
  if ( $args['orderby'] == 'term_order' ) {
    return 't.term_order';
  } elseif ( $options[$taxonomy] == 1 && !isset($_GET['orderby']) ) {
    return 't.term_order';
  } else {
    return $orderby;
  }
}
add_filter('get_terms_orderby', 'group_order_apply_order_filter', 10, 2);