<?php
add_shortcode( 'theboard-show-member', 'tb_get_one_member' );
add_shortcode( 'theboard-show-group', 'tb_get_members_by_group' );
add_shortcode( 'theboard-static-page', 'tb_get_all_members' ); // temporary static shortcode

function tb_get_members_by_group($atts) {
    $return = null;

    extract(shortcode_atts( array(
        'group' => ''
    ), $atts, 'theboard-show-group' ) );


    $path = The_Board::tb_check_path('group');

    // $terms = The_Board::tb_get_terms('groups');
    $terms = get_terms( 'groups' );
    print_r($terms);
    // $group_match = $terms[$group];
    // gérer si le group match pas

    ob_start();
    include( $path );
    $return .= ob_get_contents();
    ob_end_clean();

    return $return;
}

function tb_get_one_member($atts) {
    $return = null;

    extract(shortcode_atts( array(
        'id'    => 0
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

function tb_get_all_members(){
    ob_start();
    include('views/shortcodes/static/static-direction.php');
    include('views/shortcodes/static/static-fond.php');
    include('views/shortcodes/static/static-finance.php');
    $return .= ob_get_contents();
    ob_end_clean();
    return $return;
}