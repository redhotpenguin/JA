<?php // do_action( 'bp_before_member_header' )  ?>

<div id="item-header-avatar">
    <a href="<?php bp_user_link() ?>">
        <?php bp_displayed_user_avatar() ?>
    </a>
</div><!-- #item-header-avatar -->

<div id="item-header-content">

    <h2 class="fn"><a href="<?php bp_displayed_user_link() ?>"><?php bp_displayed_user_fullname() ?></a> <span class="activity"><?php bp_last_activity(bp_displayed_user_id()) ?></span></h2>
    <h3><?php echo bp_get_profile_field_data('field=One-Line Bio') ?></h3>


    <?php if (bp_get_profile_field_data('field=Website')) { ?><p class="website"><?php echo newMakeURL(bp_get_profile_field_data('field=Website')); ?></p><?php } ?>

    <?php
    if (bp_get_profile_field_data('field=Twitter')) {
        $twitter_handle = bp_get_profile_field_data('field=Twitter');
        $pieces = explode('/', $twitter_handle);

        if (sizeof($pieces) == 5) {

            # https://twitter.com/#!/redhotpenguin
            $twitter_handle = $pieces[4];
        } else if (sizeof($pieces) == 4) {

            // http://twitter.com/redhotpenguin
            $twitter_handle = $pieces[3];
        } else if (sizeof($pieces) == 2) {

            // twitter.com/redhotpenguin
            // no http:// on twitter link.
            $twitter_handle = $pieces[1];
        } else {


            $pieces = explode('@', $twitter_handle);
            if (sizeof($pieces) == 2) {
                $twitter_handle = $pieces[1];
            } else {
                // use what they entered
            }
        }
        if ($twitter_handle) {
            $pieces = explode('"', $twitter_handle);
            $twitter_handle = $pieces[0];
        }
        $twitter_url = 'http://twitter.com/' . $twitter_handle;
        ?><p class="twitter"><a href="<?php echo $twitter_url ?>" target="_blank"><?php echo substr($twitter_url, 7); ?></a></p><?php } ?>

    <?php
  
	$other_area = strip_tags( bp_get_profile_field_data('field=Other Areas of Expertise') ); 
	
	$expertise = unserialize( bp_get_profile_field_data('field=Areas of Expertise') );
    
    $looking_for = unserialize( bp_get_profile_field_data("field=I\'m looking for") );

    ?>

    <?php
    if (!empty($expertise) || !empty($other_area)) {
        ?>
        <p class="expertise"><b>Areas of Expertise:</b> 

        <?php
        if (!empty($expertise)) {
            echo implode(', ', $expertise);
        }

        if (!empty($other_area) && !empty($expertise)) {
            echo ', ' . $other_area;
        } elseif (!empty($other_area)) {
            echo $other_area;
        }
        ?>
        </p>
            <?php
        }

        if (!empty($looking_for)) {
            echo '<p class="expertise"><b>Looking for: </b>' . implode(', ', $looking_for) . '</p>';
        }
        ?>






<?php do_action('bp_before_member_header_meta') ?>

    <div id="item-meta">

        <div id="item-buttons">

    <?php do_action('bp_member_header_actions'); ?>

        </div><!-- #item-buttons -->

<?php
/* * *
 * If you'd like to show specific profile fields here use:
 * bp_profile_field_data( 'field=About Me' ); -- Pass the name of the field
 */
?>

        <?php do_action('bp_profile_header_meta') ?>

    </div><!-- #item-meta -->

</div><!-- #item-header-content -->

        <?php do_action('bp_after_member_header') ?>

<?php do_action('template_notices') ?>
