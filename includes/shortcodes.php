<?php
add_shortcode( 'theboard-show-member', 'tb_get_one_member' );
add_shortcode( 'theboard-show-group', 'tb_get_members_by_group' );
add_shortcode( 'theboard', 'tb_get_all_members' );

function tb_get_members_by_group($atts) {
    $return = null;

    extract(shortcode_atts( array(
        'group' => ''
    ), $atts, 'theboard-show-group' ) );

    $path = The_Board::tb_check_path('group');

    $term = get_term( $group, 'groups' );

    if( !empty($term) ){
        ob_start();
        include( $path );
        $return .= ob_get_contents();
        ob_end_clean();
    }

    return $return;
}

function tb_get_one_member($atts) {
    $return = null;

    extract(shortcode_atts( array(
        'id'    => 0 ,
      'current_group' => ''
    ), $atts, 'theboard-show-member' ) );

    $path = The_Board::tb_check_path('member');

    $tb_member_query = null;
    $tb_member_query = new WP_Query( 'post_type=member&p='.$id );

    if( $tb_member_query->have_posts() ) {
        while( $tb_member_query->have_posts() ){
            $tb_member_query->the_post();
            $postmeta = get_post_meta( get_the_ID() );
            ob_start();
            include( $path );
            $return .= ob_get_contents();
            ob_end_clean();
        }
    }
    wp_reset_postdata();
    return $return;
}

function tb_get_all_members($atts){
    extract(shortcode_atts( array(
        'display'    => 0
    ), $atts, 'theboard' ) );

    ob_start();
    $terms = get_terms( 'groups', 'parent=0&hide_empty=&orderby=term_order&order=ASC' );
    foreach ($terms as $term) {
        echo do_shortcode( '[theboard-show-group group='.$term->term_id.']' );
    }
    $return .= ob_get_contents();
    ob_end_clean();
    return $return;
}