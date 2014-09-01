<?php

add_action( 'init', 'tb_member_posttype_init', 0);
add_action( 'init', 'tb_member_groups_taxonomies_init', 0);

function tb_member_posttype_init() {
  // @theboard: Create the member post type
  $labels = array(
      'name'               => __( 'Members', MEMBERS_PLUGIN_BASENAME),
      'singular_name'      => __( 'Member', MEMBERS_PLUGIN_BASENAME),
      'add_new'            => __( 'Add New', MEMBERS_PLUGIN_BASENAME),
      'add_new_item'       => __( 'Add New Member', MEMBERS_PLUGIN_BASENAME),
      'edit_item'          => __( 'Edit Member', MEMBERS_PLUGIN_BASENAME),
      'new_item'           => __( 'New Member', MEMBERS_PLUGIN_BASENAME),
      'all_items'          => __( 'All Members', MEMBERS_PLUGIN_BASENAME),
      'view_item'          => __( 'View Member', MEMBERS_PLUGIN_BASENAME),
      'search_items'       => __( 'Search Members', MEMBERS_PLUGIN_BASENAME),
      'not_found'          => __( 'No members found', MEMBERS_PLUGIN_BASENAME),
      'not_found_in_trash' => __( 'No members found in the Trash', MEMBERS_PLUGIN_BASENAME),
      'parent_item_colon'  => '',
      'menu_name'          => 'Members'
  );
  $args = array(
      'labels'        => $labels,
      'description'   => __('Structure members', MEMBERS_PLUGIN_BASENAME),
      'public'        => true,
      'supports'      => false,
      'has_archive'   => true,
      'taxonomy'		=> 'groups',
      'menu_icon'		=> plugins_url( '/assets/icon_the-board-16x16.png' , dirname(__FILE__) )
  );
  register_post_type( 'member', $args );
}

function tb_member_groups_taxonomies_init() {
  $labels = array(
      'name'              => _x( 'Groups', 'taxonomy general name', MEMBERS_PLUGIN_BASENAME),
      'singular_name'     => _x( 'Group', 'taxonomy singular name', MEMBERS_PLUGIN_BASENAME),
      'search_items'      => __( 'Search Groups', MEMBERS_PLUGIN_BASENAME),
      'all_items'         => __( 'All Groups', MEMBERS_PLUGIN_BASENAME),
      'parent_item'       => __( 'Parent Group', MEMBERS_PLUGIN_BASENAME),
      'parent_item_colon' => __( 'Parent Group:', MEMBERS_PLUGIN_BASENAME),
      'edit_item'         => __( 'Edit Group', MEMBERS_PLUGIN_BASENAME),
      'update_item'       => __( 'Update Group', MEMBERS_PLUGIN_BASENAME),
      'add_new_item'      => __( 'Add New Group', MEMBERS_PLUGIN_BASENAME),
      'new_item_name'     => __( 'New Group Name', MEMBERS_PLUGIN_BASENAME),
      'menu_name'         => __( 'Groups', MEMBERS_PLUGIN_BASENAME)
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

