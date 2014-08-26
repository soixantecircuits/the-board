<?php

add_action( 'init', 'tb_member_posttype_init', 0);
add_action( 'init', 'tb_member_groups_taxonomies_init', 0);

function tb_member_posttype_init() {
  // @theboard: Create the member post type
  $labels = array(
      'name'               => __( 'Members', 'the-board'),
      'singular_name'      => __( 'Member', 'the-board'),
      'add_new'            => __( 'Add New', 'the-board'),
      'add_new_item'       => __( 'Add New Member', 'the-board'),
      'edit_item'          => __( 'Edit Member', 'the-board'),
      'new_item'           => __( 'New Member', 'the-board'),
      'all_items'          => __( 'All Members', 'the-board'),
      'view_item'          => __( 'View Member', 'the-board'),
      'search_items'       => __( 'Search Members', 'the-board'),
      'not_found'          => __( 'No members found', 'the-board'),
      'not_found_in_trash' => __( 'No members found in the Trash', 'the-board'),
      'parent_item_colon'  => '',
      'menu_name'          => 'Members'
  );
  $args = array(
      'labels'        => $labels,
      'description'   => __('Structure members', 'the-board'),
      'public'        => true,
      'supports'      => false,
      'has_archive'   => true,
      'taxonomy'		=> 'groups',
      'menu_icon'		=> 'dashicons-groups'
  );
  register_post_type( 'member', $args );
}

function tb_member_groups_taxonomies_init() {
  $labels = array(
      'name'              => _x( 'Groups', 'taxonomy general name', 'the-board'),
      'singular_name'     => _x( 'Group', 'taxonomy singular name', 'the-board'),
      'search_items'      => __( 'Search Groups', 'the-board'),
      'all_items'         => __( 'All Groups', 'the-board'),
      'parent_item'       => __( 'Parent Group', 'the-board'),
      'parent_item_colon' => __( 'Parent Group:', 'the-board'),
      'edit_item'         => __( 'Edit Group', 'the-board'),
      'update_item'       => __( 'Update Group', 'the-board'),
      'add_new_item'      => __( 'Add New Group', 'the-board'),
      'new_item_name'     => __( 'New Group Name', 'the-board'),
      'menu_name'         => __( 'Groups', 'the-board')
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