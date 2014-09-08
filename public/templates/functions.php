<?php
function display_members_hierarchically($group, $include_children){
  $i = 0;
  $max_cell = 3;
  $member_query = new WP_Query( array(
          'post_type'     => 'member',
          'tax_query'     => array(
              array(
                  'taxonomy'          => 'groups',
                  'terms'             => $group,
                  'include_children'  => $include_children
              )
          ),
          'meta_key'      => 'tb_hierarchy',
          'ordeby'        => 'meta_value meta_value_num',
          'order'         => 'ASC'
      )
  );

  $hierarchy_row = array();

  foreach ($member_query->get_posts() as $member) {
    $rank = get_post_meta( $member->ID, 'tb_hierarchy', true );
    $hierarchy_row[$rank][] = array(
        'id'        => $member->ID,
        'hierarchy' => $rank
    );
  }
  ksort($hierarchy_row);
  $j = 0;
  foreach ($hierarchy_row as $members) {
    $special_class = $j==0? ' first_member':'';

    $total_posts = count($members);
    $i = 0;
    foreach ($members as $member) {
      $i++;

      if( !isset($prev_member) || $prev_member['hierarchy'] !== $member['hierarchy'] ){
        echo '<tr class="member_row'.$special_class.'">';
      }

      if ($include_children){
        if ($j == 0){
          $colspan = 1;
        }
        else{
          if( $total_posts == 1  ){
            echo '<td colspan="1" class="member_cell"></td>';
          }
          elseif ($total_posts == 2 && $i != $total_posts){
            echo '<td colspan="1" class="member_cell"></td>';
          }
          $colspan = 1;
        }
      }
      else{
        if( 0 !== $total_posts % 3 && $i == $total_posts && $total_posts > 1){
          echo '<td colspan="1" class="member_cell"></td>';
        }

        if( $total_posts > 1 )
          $colspan = 1;
        else
          $colspan = 3;
      }



      echo '<td colspan="'.$colspan.'" rowspan="" headers="" class="member_cell">';
      echo do_shortcode( '[theboard-show-member id='.$member['id'].']' );
      echo '</td>';


      if( $j == 0 && $total_posts == 1  ){
        echo '<td colspan="1" class="member_cell"></td>';
        echo '<td colspan="1" class="member_cell"></td>';
      }

      if(0 == $i % $max_cell && $i !== $total_posts){
        echo '</tr>';
        echo '<tr class="member_row'.$special_class.'">';
      }

      $prev_member = $member;
    }
    $j++;
  }
  echo '</tr>';
  wp_reset_postdata();
}

function display_head_members($group, $rowspan){
  $member_query = new WP_Query( array(
          'post_type'     => 'member',
          'tax_query'     => array(
              array(
                  'taxonomy'          => 'groups',
                  'terms'             => $group,
                  'include_children'  => false
              )
          )
      )
  );
  if( $member_query->have_posts() ){
    echo '<td rowspan="'.$rowspan.'" class="member_cell left_column_member">';
    while ( $member_query->have_posts() ) {
      $member_query->the_post();
      echo do_shortcode( '[theboard-show-member id='.get_the_ID().']' );
    }
    echo '</td>';
  }
  wp_reset_postdata();
}