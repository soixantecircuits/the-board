<?php


function set_member_columns($columns) {

  return array(
      'photo' => __('Profile photo', The_Board::get_instance()->get_plugin_slug()),
      'title' => __('Name', The_Board::get_instance()->get_plugin_slug()),
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
      echo '<img width="100" height="100" src="'. get_post_meta( $post_id , 'tb_photo' , true ) . '">';
      break;
    case 'title' :
      echo get_post_meta( $post_id , 'tb_lastname' , true );
      break;
    case 'first_name' :
      echo get_post_meta( $post_id , 'tb_firstname' , true );
      break;
    case 'group' :
      echo get_post_meta( $post_id , 'tb_group' , true );
      break;;
  }
}
add_action( 'manage_posts_custom_column' , 'member_columns', 10, 2 );


add_filter('title_save_pre', 'save_title');
function save_title($title_to_ignore) {
  if ($_POST['post_type']=='member'){
    $my_post_title = $_POST['tb_lastname'];
  }
  return $my_post_title;
}


