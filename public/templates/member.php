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
        <?php if( !isset($postmeta['hideit_tb_photo']) ) { ?>
            <tr class="picture_row">
                <td colspan="5" rowspan="" headers="">
                <?php
                  if( !isset( $postmeta['tb_photo'][0] ) ) {
                    $image = plugin_dir_url(__FILE__).'assets/replace.jpg';
                  } else {
                    $image = $postmeta['tb_photo'][0];
                  }
                 ?>
                <img src="<?php echo $image; ?>" width="72" height="72" alt="">
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
                            if( isset($postmeta['tb_invert']) && isset($postmeta['tb_lastname']) && isset($postmeta['tb_firstname']) ){
                                $fullname = $postmeta['tb_firstname'][0] . ' ' . $postmeta['tb_lastname'][0];
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
                            <div id="contact-pop">
                                <a href="javascript:void(0);" onclick="hide('contact-pop');" title="Close"><?php _e('Close this window', The_Board::get_instance()->get_plugin_slug()); ?></a>
                                <?php echo do_shortcode('[contact-form-7 id="'.$postmeta['tb_contact'][0].'"]'); ?>
                            </div>
                            <a href="javascript:void(0);" onclick="pop('contact-pop');" title="<?php echo $fullname; ?>"><?php echo __('Contacter', The_Board::get_instance()->get_plugin_slug() ).' '.$fullname; ?></a>
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
        <?php if( isset($postmeta['tb_facebook']) || isset($postmeta['tb_twitter']) || isset($postmeta['tb_googleplus']) || isset($postmeta['tb_linkedin']) || isset($postmeta['tb_skype']) ) { ?>
            <tr class="social_row">
                <td colspan="4">
                    <?php if( isset($postmeta['tb_facebook']) && !isset($postmeta['hideit_tb_facebook'])) { ?>
                        <div class="social_div">
                            <a href="http://facebook.com/<?php echo $postmeta['tb_facebook'][0]; ?>" title="facebook" class="tb_facebook-icon" target="_blank"></a>
                        </div>
                    <?php } ?>
                    <?php if( isset($postmeta['tb_twitter']) && !isset($postmeta['hideit_tb_twitter']) ) { ?>
                        <div class="social_div">
                            <a href="http://twitter.com/<?php echo $postmeta['tb_twitter'][0]; ?>" title="twitter" class="tb_twitter-icon" target="_blank"></a>
                        </div>
                    <?php } ?>
                    <?php if( isset($postmeta['tb_googleplus']) && !isset($postmeta['hideit_tb_googleplus'])) { ?>
                        <div class="social_div">
                            <a href="http://plus.google.com/<?php echo $postmeta['tb_googleplus'][0]; ?>/posts" title="googleplus" class="tb_googleplus-icon" target="_blank"></a>
                        </div>
                    <?php } ?>
                    <?php if( isset($postmeta['tb_linkedin']) && !isset($postmeta['hideit_tb_linkedin'])) { ?>
                        <div class="social_div">
                            <a href="http://linkedin.com/<?php echo $postmeta['tb_linkedin'][0]; ?>" title="linkedin" class="tb_linkedin-icon" target="_blank"></a>
                        </div>
                    <?php } ?>
                    <?php if( isset($postmeta['tb_skype']) && !isset($postmeta['hideit_tb_skype'])) { ?>
                        <div class="social_div">
                            <a href="http://skype.com/<?php echo $postmeta['tb_skype'][0]; ?>" title="skype" class="tb_skype-icon" target="_blank"></a>
                        </div>
                    <?php } ?>
                </td>
            </tr>
        <?php } ?>
        <?php if( isset($postmeta['tb_custom']) && !isset($postmeta['hideit_tb_custom'])) { ?>
            <tr class="custom_row">
                <td colspan="5" rowspan="" headers="">
                    <?php echo $postmeta['tb_custom'][0]; ?>
                </td>
          </tr>
        <?php } ?>
    </tbody>
</table>