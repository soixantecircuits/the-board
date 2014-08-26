<?php
/**
 *
 * @package   The Board
 * @author    Soixane circuits
 * @license   GPL-2.0+
 */
?>
<table class="group_block">
    <thead>
        <tr>
            <th colspan="" rowspan="" headers="" scope=""><?php echo $group; ?></th>
            <?php //echo term_description( $term, $taxonomy ); ?>
        </tr>
    </thead>
    <tbody>
        <?php
            // print_r($group_match);
            if( sizeof($group_match) > 1 ){
                foreach ($group as $subgroup) {
                    ?>
                        <tr>

                        </tr>
                    <?php
                }
            } else {
                display_members_hierarchically($group);
            }
         ?>
    </tbody>
</table>

<?php 
    function display_members_hierarchically($group){
        $members = array();
        $member_query = new WP_Query( array(
            'post_type' => 'member',
            // 'tax_query' => array(
                // array(
                    // 'taxonomy' => 'groups',
                    // 'terms'    => $group
            //     )
            // )
        ) );
        // print_r($member_query);
        if( $member_query->have_posts() ){
            while ( $member_query->have_posts() ) {
                $member_query->the_post();
                $members[] = array(
                    'hierarchy' => get_post_meta( get_the_ID(), 'tb_hierarchy', true ),
                    'ID'        => get_the_ID()
                );
            }
        }
        if( sizeof($members) ){
            $i = 0;
            while( $i < sizeof($members) ){
                foreach ($members as $member) {
                    if($i == $member['hierarchy']) {
                        echo do_shortcode( '[theboard-show-member id='.$member["ID"].']' );
                    }
                }
                $i++;
            }
        }
    }
 ?>