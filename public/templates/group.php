<?php
/**
 *
 * @package   The Board
 * @author    Soixane circuits
 * @license   GPL-2.0+
 */
?>
<?php
    include_once 'functions.php';
 ?>
<table class="group_block">
    <thead>
        <tr>
            <th colspan="3" class="group_header"><?php echo $term->name; ?></th>
        </tr>
    </thead>
    <tbody>
        <tr>
            <?php
            $term_children = get_terms( 'groups', array( 'child_of' => $term->term_id, 'orderby' => 'term_order', 'order' => 'ASC' ) );
                if( sizeof($term_children) ){
                    ?>
                    <?php
                        display_members_hierarchically($term->term_id, false);
                    ?>
                    <td colspan="" class="member_cell right_column_members">
                    <?php
                        foreach ($term_children as $child => $child_id) {
                            $subgroup = get_term( $child_id, 'groups' );
                            $term_posts = get_posts( array(
                                    'post_type'     => 'member',
                                    'tax_query'     => array(
                                        array(
                                            'taxonomy'  => 'groups',
                                            'terms'     => $subgroup->term_id,
                                        )
                                    ),
                                )
                            );
                            if( empty($term_posts) ){
                                wp_reset_postdata();
                            } else {
                                wp_reset_postdata();
                                ?>
                                    <table class="subgroup_block">
                                        <thead>
                                            <tr>
                                                <th colspan="3" rowspan="" headers="" scope=""><?php echo $subgroup->name; ?></th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                                <?php display_members_hierarchically($subgroup->term_id, true); ?>
                                        </tbody>
                                    </table>
                                <?php
                            }
                        }
                    ?></td><?php
                } else {
                    display_members_hierarchically($group, false);
                }
            ?>
        </tr>
    </tbody>
</table>

