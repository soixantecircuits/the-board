<?php
/**
 *
 * @package   The Board
 * @author    Soixane circuits
 * @license   GPL-2.0+
 */
?>
<table class="member_block">
    <tbody>
        <?php if( isset($postmeta['tb_photo']) && !isset($postmeta['hideit_tb_photo']) ) { ?>
            <tr class="picture_row">
                <td colspan="5" rowspan="" headers="">
                    <?php $attachments = get_children( 'post_type=attachment&post_mime_type=image' );
                    $attachment_array = array_values($attachments);
                    foreach ($attachment_array as $attachment) {
                        if( $postmeta['tb_photo'][0] == $attachment->guid ){
                            $image_id = $attachment->ID;
                            break;
                        }
                    }
                    if( $image_id )
                        echo wp_get_attachment_image( $image_id, 'tb_crop-120' ); ?>
                </td>
            </tr>
        <?php } ?>
        <?php if( isset($postmeta['tb_lastname']) || isset($postmeta['tb_firstname']) ) { ?>
            <tr class="name_row">
                <td colspan="5" rowspan="" headers="">
                        <?php $fullname = null;
                        if( isset($postmeta['tb_lastname']) || isset($postmeta['tb_firstname']) ) {
                            if(isset($postmeta['tb_lastname']) && !isset($postmeta['hideit_tb_lastname'])){
                                $fullname .= $postmeta['tb_lastname'][0];
                            }
                            if(isset($postmeta['tb_lastname']) && isset($postmeta['tb_firstname'])){
                                $fullname .= ' ';
                            }
                            if(isset($postmeta['tb_firstname']) && !isset($postmeta['hideit_tb_firstname'])){
                                $fullname .= $postmeta['tb_firstname'][0];
                            }
                        } ?>
                        <?php echo $fullname; ?>
                </td>
            </tr>
        <?php } ?>
        <?php if( isset($postmeta['tb_job']) && !isset($postmeta['hideit_tb_job']) ) { ?>
            <tr class="job_row">
                <td colspan="5" rowspan="" headers="">
                    <?php echo $postmeta['tb_job'][0]; ?>
                </td>
            </tr>
        <?php } ?>
        <?php if( isset($postmeta['tb_contact']) || isset($postmeta['tb_email']) || isset($postmeta['tb_phone']) ) { ?>
            <tr class="contact_row">
                <?php if( isset($postmeta['tb_contact']) && !isset($postmeta['hideit_tb_contact'])) { ?>
                    <tr>
                        <td colspan="5" rowspan="" headers="">
                            <?php echo do_shortcode('[modal name="'.__('Contacter', $this->plugin_slug).' '.$fullname.'" class="line" title="'.__('Contacter', $this->plugin_slug).' '.$fullname.'" width="320px"][contact-form-7 id="'.$postmeta['tb_contact'][0].'"][/modal]'); ?>
                        </td>
                    </tr>
                <?php } ?>
                <?php if( isset($postmeta['tb_email']) && !isset($postmeta['hideit_tb_email'])) { ?>
                    <tr>
                        <td colspan="5" rowspan="" headers="">
                            <a href="mailto:<?php echo $postmeta['tb_email'][0]; ?>"><?php echo $postmeta['tb_email'][0]; ?></a>
                        </td>
                    </tr>
                <?php } ?>
                <?php if( isset($postmeta['tb_phone']) && !isset($postmeta['hideit_tb_phone'])) { ?>
                    <tr>
                        <td colspan="5" rowspan="" headers="">
                            <?php echo $postmeta['tb_phone'][0]; ?>
                        </td>
                    </tr>
                <?php } ?>
            </tr>
        <?php } ?>
        <!--<?php if( isset($postmeta['tb_facebook']) || isset($postmeta['tb_twitter']) || isset($postmeta['tb_googleplus']) || isset($postmeta['tb_linkedin']) || isset($postmeta['tb_skype']) ) { ?>
            <tr class="social_row">
                <?php if( isset($postmeta['tb_facebook']) && !isset($postmeta['hideit_tb_facebook'])) { ?>
                    <td colspan="" rowspan="" headers="">
                        <?php echo $postmeta['tb_facebook'][0]; ?>
                    </td>
                <?php } ?>
                <?php if( isset($postmeta['tb_twitter']) && !isset($postmeta['hideit_tb_twitter']) ) { ?>
                    <td colspan="" rowspan="" headers="">
                        <?php echo $postmeta['tb_twitter'][0]; ?>
                    </td>
                <?php } ?>
                <?php if( isset($postmeta['tb_googleplus']) && !isset($postmeta['hideit_tb_googleplus'])) { ?>
                    <td colspan="" rowspan="" headers="">
                        <?php echo $postmeta['tb_googleplus'][0]; ?>
                    </td>
                <?php } ?>
                <?php if( isset($postmeta['tb_linkedin']) && !isset($postmeta['hideit_tb_linkedin'])) { ?>
                    <td colspan="" rowspan="" headers="">
                        <?php echo $postmeta['tb_linkedin'][0]; ?>
                    </td>
                <?php } ?>
                <?php if( isset($postmeta['tb_skype']) && !isset($postmeta['hideit_tb_skype'])) { ?>
                    <td colspan="" rowspan="" headers="">
                        <?php echo $postmeta['tb_skype'][0]; ?>
                    </td>
                <?php } ?>
            </tr>
        <?php } ?>
        <?php if( isset($postmeta['tb_custom']) && !isset($postmeta['hideit_tb_custom'])) { ?>
            <tr class="custom_row">
                <td colspan="5" rowspan="" headers="">
                    <?php echo $postmeta['tb_custom'][0]; ?>
                </td>
          </tr>
        <?php } ?> -->
    </tbody>
</div>