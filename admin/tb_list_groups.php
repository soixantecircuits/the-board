<?php

function set_groups_columns($columns) {

  return array(
        'cb'    => '<input type="checkbox">',
        'Name' => __('Name', The_Board::get_instance()->get_plugin_slug()),
        'shortcode' => __('shortcode', The_Board::get_instance()->get_plugin_slug()),
        'description' => __('Description', The_Board::get_instance()->get_plugin_slug()),
        'posts' => __('Posts', The_Board::get_instance()->get_plugin_slug()),
  );
}
add_filter('manage_edit-groups_columns' , 'set_groups_columns');


function groups_columns( $return, $column, $group_id ) {
    $group = get_term( $group_id, 'groups' );
  switch ( $column ) {
    case 'shortcode' :
      $return = "<input type=\"text\" value=\"[theboard-show-group group=$group->term_id]\" readonly>";
      break;
  }
  return $return;
}
add_action( 'manage_groups_custom_column' , 'groups_columns', 10, 3 );