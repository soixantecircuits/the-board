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
        </tr>
    </thead>
    <tbody>
        <?php
            if( sizeof($group) > 1 ){
                foreach ($group as $subgroup) {
                    ?>
                        <tr>

                        </tr>
                    <?php
                }
            } else {
                $member_query = new WP_Query( 'post_type=member&term='.$group );
                if( $member_query->have_posts() ){
                    while ( $member_query->have_posts() ) {
                        $member_query->the_post();
                        echo do_shortcode( '[theboard-show-member id='.get_the_ID().']' );
                    }
                }
            }
         ?>
    </tbody>
</table>