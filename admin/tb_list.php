<?php
function set_member_columns($columns) {

  return array(
      'photo' => __('Profile photo', The_Board::get_instance()->get_plugin_slug()),
      'title' => __('Last name', The_Board::get_instance()->get_plugin_slug()),
      'first_name' => __('First name', The_Board::get_instance()->get_plugin_slug()),
      'shortcode' => __('Shortcode', The_Board::get_instance()->get_plugin_slug()),
      'group' => __('Group', The_Board::get_instance()->get_plugin_slug()),
      'date' => __('Date', The_Board::get_instance()->get_plugin_slug()),
      'author' => __('Author', The_Board::get_instance()->get_plugin_slug()),

  );
}
add_filter('manage_member_posts_columns' , 'set_member_columns');


function member_columns( $column, $post_id ) {
  switch ( $column ) {
    case 'photo' :
      $image = get_post_meta( $post_id , 'tb_photo' , true );
      if( empty( $image ) ) {
        $image = plugins_url( '/assets/replace.jpg' , dirname(__FILE__) );
      } 
      echo '<img width="72" height="72" src="'. $image . '">';
      break;
    case 'title' :
      echo get_post_meta( $post_id , 'tb_lastname' , true );
      break;
    case 'first_name' :
      echo get_post_meta( $post_id , 'tb_firstname' , true );
      break;
    case 'shortcode' :
      echo "[theboard-show-member id=".$post_id."]";
      break;
    case 'group' :
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


//add_action('do_meta_boxes', 'move_meta_box');
//function move_meta_box()
//{
//  remove_meta_box('groupsdiv', 'member', 'side');
//  add_meta_box( "groupsdiv", __('Groups', 'the-board'), 'wp_nav_menu_item_taxonomy_meta_box', 'member', 'side', 'default');
//}