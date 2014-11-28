<?php
function set_member_columns($columns) {
  return array(
      'cb'          => __('Bulk actions', The_Board::get_instance()->get_plugin_slug()),
      'tb_photo'       => __('Profile photo', The_Board::get_instance()->get_plugin_slug()),
      'title'       => __('Last name', The_Board::get_instance()->get_plugin_slug()),
      'tb_first_name'  => __('First name', The_Board::get_instance()->get_plugin_slug()),
      'tb_shortcode'   => __('Shortcode', The_Board::get_instance()->get_plugin_slug()),
      'tb_group'       => __('Group', The_Board::get_instance()->get_plugin_slug()),
      'tb_hierarchy'   => __('Hierarchy', The_Board::get_instance()->get_plugin_slug()),
      'tb_order'   => __('Order', The_Board::get_instance()->get_plugin_slug())
  );
}
add_filter('manage_member_posts_columns' , 'set_member_columns');


function member_columns( $column, $post_id ) {
  switch ( $column ) {
    case 'tb_photo' :
      $image = get_post_meta( $post_id , 'tb_photo' , true );
      if( empty( $image ) ) {
        $image = plugins_url( '/assets/replace.jpg' , dirname(__FILE__) );
      }
      echo '<div class="tb_photo-container-list">';
      echo '<img src="'. $image . '">';
      echo '</div>';
      break;
    case 'title' :
      echo get_post_meta( $post_id , 'tb_lastname' , true );
      break;
    case 'tb_first_name' :
      echo get_post_meta( $post_id , 'tb_firstname' , true );
      break;
    case 'tb_shortcode' :
      echo "<input type='text' readonly value='[theboard-show-member id=".$post_id."]'>";
      break;
    case 'tb_hierarchy' :
      echo get_post_meta( $post_id , 'tb_hierarchy' , true );
      break;
    case 'tb_order' :
      echo get_post_meta( $post_id , 'tb_order' , true );
      break;
    case 'tb_group' :
      echo get_groups( $post_id);
      break;
  }
}
add_action( 'manage_posts_custom_column' , 'member_columns', 10, 2 );

function get_groups($id){
  $groups = wp_get_post_terms( $id, 'groups' );
  $groups_string = '';
  foreach ($groups as $group){
    if ($groups_string!='')
      $groups_string.=', '.$group->name;
    else
      $groups_string.=$group->name;
  }
  return $groups_string;
}

function tb_quickedit($column, $post_type){
  switch ($column) {
    case 'hierarchy':
    ?>
      <fieldset class="inline-edit-col-right">
        <div class="inline-edit-col">
          <label for="tb_hierarchy" class="inline-edit-status alignleft">
            <span><?php _e('Hierarchy', The_Board::get_instance()->get_plugin_slug()); ?></span>
            <input type="number" name="tb_hierarchy" min="0" id="tb_hierarchy">
          </label>
        </div>
      </fieldset>
    <?php
      break;
    case 'order':
      ?>
      <fieldset class="inline-edit-col-right">
        <div class="inline-edit-col">
          <label for="tb_order" class="inline-edit-status alignleft">
            <span><?php _e('Order', The_Board::get_instance()->get_plugin_slug()); ?></span>
            <input type="number" name="tb_order" min="0" id="tb_order">
          </label>
        </div>
      </fieldset>
      <?php
      break;
  }

}
add_action('quick_edit_custom_box', 'tb_quickedit', 1, 2);

function tb_save_quickedit($post_id){
  if ( defined('DOING_AUTOSAVE') && DOING_AUTOSAVE )
      return $post_id;
  if ( !isset($_POST) ){
    if ( 'member' == $_POST['post_type'] ) {
      if ( !current_user_can( 'edit_page', $post_id ) )
          return $post_id;
    }
  } else {
      if ( !current_user_can( 'edit_post', $post_id ) )
      return $post_id;
  }

  $post = get_post( $post_id );
  if (isset($_POST['tb_hierarchy']) && ($post->post_type != 'revision')) {
      $hierarchy = esc_attr($_POST['tb_hierarchy']);
      update_post_meta( $post_id, 'tb_hierarchy', $hierarchy);
  }
  if (isset($_POST['tb_order']) && ($post->post_type != 'revision')) {
    $order = esc_attr($_POST['tb_order']);
    update_post_meta( $post_id, 'tb_order', $order);
  }
  if( isset($hierarchy) )
    return $hierarchy;
  else if( isset($order) )
    return $order;
  else
    return $post_id;
}
add_action( 'save_post', 'tb_save_quickedit' );

function tb_quickedit_js(){
  global $current_screen;
  if (($current_screen->id != 'edit-member') || ($current_screen->post_type != 'member')) return;
  ?>
    <script>
      function quickedit_hierarchy(hierarchy) {
        inlineEditPost.revert();
        document.getElementById('tb_hierarchy').value = hierarchy;
      }
      function quickedit_order(order) {
        inlineEditPost.revert();
        document.getElementById('tb_order').value = order;
      }
    </script>
  <?php
}
add_action('admin_footer', 'tb_quickedit_js');

function tb_expand_quick_edit_link($actions, $post) {
    if( 'member' !== get_post_type( $post->ID ) )
      return $actions;

    $hierarchy = get_post_meta( $post->ID, 'tb_hierarchy', TRUE);
    $order = get_post_meta( $post->ID, 'tb_order', TRUE);
    $actions['inline hide-if-no-js'] = '<a href="#" class="editinline" title="';
    $actions['inline hide-if-no-js'] .= esc_attr( __( 'Edit this item inline' ) ) . '" ';
    $actions['inline hide-if-no-js'] .= " onclick=\"quickedit_hierarchy('{$hierarchy}'); quickedit_order('{$order}');\">";
    $actions['inline hide-if-no-js'] .= __( 'Quick&nbsp;Edit' );
    $actions['inline hide-if-no-js'] .= '</a>';
    return $actions;
}
add_filter('post_row_actions', 'tb_expand_quick_edit_link', 10, 2);
