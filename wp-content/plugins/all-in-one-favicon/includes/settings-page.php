<?php
/**
 * @package Techotronic
 * @subpackage All in one Favicon
 *
 * @since 1.0
 * @author Arne Franken
 *
 *
 */
?>
<div class="wrap">
    <div>
    <?php screen_icon(); ?>
    <h2><?php echo AIOFAVICON_NAME . ' ' . __('Settings', AIOFAVICON_TEXTDOMAIN); ?></h2>
    <br class="clear"/>

    <?php settings_fields(AIOFAVICON_SETTINGSNAME); ?>

    <div class="postbox-container" style="width: 69%;">
    <div id="poststuff">
        <div id="aio-favicon-settings" class="postbox">
            <h3 id="settings"><?php _e('Settings', AIOFAVICON_TEXTDOMAIN); ?></h3>

            <div class="inside">
                <form name="aio-favicon-settings-update" method="post" action="admin-post.php">
                <?php if (function_exists('wp_nonce_field') === true) wp_nonce_field('aio-favicon-settings-form'); ?>
                
                <table class="form-table">
                    <tr>
                        <th scope="row">
                            <label for="<?php echo AIOFAVICON_SETTINGSNAME ?>-frontendICO"><?php printf(__('%1$s ICO', AIOFAVICON_TEXTDOMAIN), __('Frontend', AIOFAVICON_TEXTDOMAIN)); ?>:</label>
                        </th>
                        <td>
                            <input id="<?php echo AIOFAVICON_SETTINGSNAME ?>-frontendICO" class="aioFaviconUrl" type="text" size="50" name="<?php echo AIOFAVICON_SETTINGSNAME ?>[frontendICO]" value="<?php echo $this->aioFaviconSettings['frontendICO'] ?>" />
                            <input id="<?php echo AIOFAVICON_SETTINGSNAME ?>-frontendICO_button" class="button aioFaviconUpload" type="button" value="<?php echo htmlspecialchars(__('Upload Favicon',AIOFAVICON_TEXTDOMAIN)) ?>" />
                            <br /><?php _e('Enter a URL or upload a Favicon.',AIOFAVICON_TEXTDOMAIN) ?>
                        </td>
                    </tr>
                    <tr>
                        <th scope="row">
                            <label for="<?php echo AIOFAVICON_SETTINGSNAME ?>-frontendPNG"><?php printf(__('%1$s PNG', AIOFAVICON_TEXTDOMAIN), __('Frontend', AIOFAVICON_TEXTDOMAIN)); ?>:</label>
                        </th>
                        <td>
                            <input id="<?php echo AIOFAVICON_SETTINGSNAME ?>-frontendPNG" class="aioFaviconUrl" type="text" size="50" name="<?php echo AIOFAVICON_SETTINGSNAME ?>[frontendPNG]" value="<?php echo $this->aioFaviconSettings['frontendPNG'] ?>" />
                            <input id="<?php echo AIOFAVICON_SETTINGSNAME ?>-frontendPNG_button" class="button aioFaviconUpload" type="button" value="<?php echo htmlspecialchars(__('Upload Favicon',AIOFAVICON_TEXTDOMAIN)) ?>" />
                            <br /><?php _e('Enter a URL or upload a Favicon.',AIOFAVICON_TEXTDOMAIN) ?>
                        </td>
                    </tr>
                    <tr>
                        <th scope="row">
                            <label for="<?php echo AIOFAVICON_SETTINGSNAME ?>-frontendGIF"><?php printf(__('%1$s GIF', AIOFAVICON_TEXTDOMAIN), __('Frontend', AIOFAVICON_TEXTDOMAIN)); ?>:</label>
                        </th>
                        <td>
                            <input id="<?php echo AIOFAVICON_SETTINGSNAME ?>-frontendGIF" class="aioFaviconUrl" type="text" size="50" name="<?php echo AIOFAVICON_SETTINGSNAME ?>[frontendGIF]" value="<?php echo $this->aioFaviconSettings['frontendGIF'] ?>" />
                            <input id="<?php echo AIOFAVICON_SETTINGSNAME ?>-frontendGIF_button" class="button aioFaviconUpload" type="button" value="<?php echo htmlspecialchars(__('Upload Favicon',AIOFAVICON_TEXTDOMAIN)) ?>" />
                            <br /><?php _e('Enter a URL or upload a Favicon.',AIOFAVICON_TEXTDOMAIN) ?>
                        </td>
                    </tr>
                    <tr>
                        <th scope="row">
                            <label for="<?php echo AIOFAVICON_SETTINGSNAME ?>-frontendApple"><?php printf(__('%1$s Apple touch icon', AIOFAVICON_TEXTDOMAIN), __('Frontend', AIOFAVICON_TEXTDOMAIN)); ?>:</label>
                        </th>
                        <td>
                            <input id="<?php echo AIOFAVICON_SETTINGSNAME ?>-frontendApple" class="aioFaviconUrl" type="text" size="50" name="<?php echo AIOFAVICON_SETTINGSNAME ?>[frontendApple]" value="<?php echo $this->aioFaviconSettings['frontendApple'] ?>" />
                            <input id="<?php echo AIOFAVICON_SETTINGSNAME ?>-frontendApple_button" class="button aioFaviconUpload" type="button" value="<?php echo htmlspecialchars(__('Upload Favicon',AIOFAVICON_TEXTDOMAIN)) ?>" />
                            <br /><?php _e('Enter a URL or upload a Favicon.',AIOFAVICON_TEXTDOMAIN) ?>
                        </td>
                    </tr>

                    <tr>
                        <th scope="row">
                            <label for="<?php echo AIOFAVICON_SETTINGSNAME ?>-backendICO"><?php printf(__('%1$s ICO', AIOFAVICON_TEXTDOMAIN), __('Backend', AIOFAVICON_TEXTDOMAIN)); ?>:</label>
                        </th>
                        <td>
                            <input id="<?php echo AIOFAVICON_SETTINGSNAME ?>-backendICO" class="aioFaviconUrl" type="text" size="50" name="<?php echo AIOFAVICON_SETTINGSNAME ?>[backendICO]" value="<?php echo $this->aioFaviconSettings['backendICO'] ?>" />
                            <input id="<?php echo AIOFAVICON_SETTINGSNAME ?>-backendICO_button" class="button aioFaviconUpload" type="button" value="<?php echo htmlspecialchars(__('Upload Favicon',AIOFAVICON_TEXTDOMAIN)) ?>" />
                            <br /><?php _e('Enter a URL or upload a Favicon.',AIOFAVICON_TEXTDOMAIN) ?>
                        </td>
                    </tr>
                    <tr>
                        <th scope="row">
                            <label for="<?php echo AIOFAVICON_SETTINGSNAME ?>-backendPNG"><?php printf(__('%1$s PNG', AIOFAVICON_TEXTDOMAIN), __('Backend', AIOFAVICON_TEXTDOMAIN)); ?>:</label>
                        </th>
                        <td>
                            <input id="<?php echo AIOFAVICON_SETTINGSNAME ?>-backendPNG" class="aioFaviconUrl" type="text" size="50" name="<?php echo AIOFAVICON_SETTINGSNAME ?>[backendPNG]" value="<?php echo $this->aioFaviconSettings['backendPNG'] ?>" />
                            <input id="<?php echo AIOFAVICON_SETTINGSNAME ?>-backendPNG_button" class="button aioFaviconUpload" type="button" value="<?php echo htmlspecialchars(__('Upload Favicon',AIOFAVICON_TEXTDOMAIN)) ?>" />
                            <br /><?php _e('Enter a URL or upload a Favicon.',AIOFAVICON_TEXTDOMAIN) ?>
                        </td>
                    </tr>
                    <tr>
                        <th scope="row">
                            <label for="<?php echo AIOFAVICON_SETTINGSNAME ?>-backendGIF"><?php printf(__('%1$s GIF', AIOFAVICON_TEXTDOMAIN), __('Backend', AIOFAVICON_TEXTDOMAIN)); ?>:</label>
                        </th>
                        <td>
                            <input id="<?php echo AIOFAVICON_SETTINGSNAME ?>-backendGIF" class="aioFaviconUrl" type="text" size="50" name="<?php echo AIOFAVICON_SETTINGSNAME ?>[backendGIF]" value="<?php echo $this->aioFaviconSettings['backendGIF'] ?>" />
                            <input id="<?php echo AIOFAVICON_SETTINGSNAME ?>-backendGIF_button" class="button aioFaviconUpload" type="button" value="<?php echo htmlspecialchars(__('Upload Favicon',AIOFAVICON_TEXTDOMAIN)) ?>" />
                            <br /><?php _e('Enter a URL or upload a Favicon.',AIOFAVICON_TEXTDOMAIN) ?>
                        </td>
                    </tr>
                    <tr>
                        <th scope="row">
                            <label for="<?php echo AIOFAVICON_SETTINGSNAME ?>-backendApple"><?php printf(__('%1$s Apple touch icon', AIOFAVICON_TEXTDOMAIN), __('Backend', AIOFAVICON_TEXTDOMAIN)); ?>:</label>
                        </th>
                        <td>
                            <input id="<?php echo AIOFAVICON_SETTINGSNAME ?>-backendApple" class="aioFaviconUrl" type="text" size="50" name="<?php echo AIOFAVICON_SETTINGSNAME ?>[backendApple]" value="<?php echo $this->aioFaviconSettings['backendApple'] ?>" />
                            <input id="<?php echo AIOFAVICON_SETTINGSNAME ?>-backendApple_button" class="button aioFaviconUpload" type="button" value="<?php echo htmlspecialchars(__('Upload Favicon',AIOFAVICON_TEXTDOMAIN)) ?>" />
                            <br /><?php _e('Enter a URL or upload a Favicon.',AIOFAVICON_TEXTDOMAIN) ?>
                        </td>
                    </tr>
                    <tr>
                    <th scope="row">
                        <label for="<?php echo AIOFAVICON_SETTINGSNAME ?>-removeLinkFromMetaBox"><?php _e('Remove link from Meta-box', AIOFAVICON_TEXTDOMAIN); ?>:</label>
                    </th>
                    <td>
                        <input type="checkbox" name="<?php echo AIOFAVICON_SETTINGSNAME ?>[removeLinkFromMetaBox]" id="<?php echo AIOFAVICON_SETTINGSNAME ?>-removeLinkFromMetaBox" value="true" <?php echo ($this->aioFaviconSettings['removeLinkFromMetaBox']) ? 'checked="checked"' : '';?>/>
                        <br/><?php _e('Remove the link to the developers site from the WordPress meta-box.', AIOFAVICON_TEXTDOMAIN); ?>
                    </td>
                </tr>
                </table>
                <p class="submit">
                    <input type="hidden" name="action" value="aioFaviconUpdateSettings"/>
                    <input type="submit" name="aioFaviconUpdateSettings" class="button-primary" value="<?php _e('Save Changes') ?>"/>
                </p>
                </form>
            </div>
        </div>
    </div>

    <div id="poststuff">
        <div id="aio-favicon-delete_settings" class="postbox">
            <h3 id="delete_options"><?php _e('Delete Settings', AIOFAVICON_TEXTDOMAIN) ?></h3>

            <div class="inside">
                <p><?php _e('Check the box and click this button to delete settings of this plugin.', AIOFAVICON_TEXTDOMAIN); ?></p>

                <form name="delete_settings" method="post" action="admin-post.php">
                <?php if (function_exists('wp_nonce_field') === true) wp_nonce_field('aio-favicon-delete_settings-form'); ?>
                    <p id="submitbutton">
                    <input type="hidden" name="action" value="aioFaviconDeleteSettings"/>
                    <input type="submit" name="aioFaviconDeleteSettings" value="<?php _e('Delete Settings', AIOFAVICON_TEXTDOMAIN); ?> &raquo;" class="button-secondary"/>
                    <input type="checkbox" name="delete_settings-true"/>
                </p>
                </form>
            </div>
        </div>
    </div>

    <div id="poststuff">
        <div id="aio-favicon-tips" class="postbox">
            <h3 id="tips"><?php _e('Tips', AIOFAVICON_TEXTDOMAIN) ?></h3>

            <div class="inside">
              <table class="form-table">
                <tr>
                    <th scope="row">
                        <label for="faviconWikipedia"><?php _e('Favicon wikipedia entry', AIOFAVICON_TEXTDOMAIN); ?>:</label>
                    </th>
                    <td id="faviconWikipedia">
                        <?php _e('<a href="http://en.wikipedia.org/wiki/Favicon">Wikipedia</a> offers much information about favicon types and sizes.',AIOFAVICON_TEXTDOMAIN) ?>
                    </td>
                </tr>
                <tr>
                    <th scope="row">
                        <label for="faviconGenerator"><?php _e('Favicon generator', AIOFAVICON_TEXTDOMAIN); ?>:</label>
                    </th>
                    <td id="faviconGenerator">
                        <?php _e('<a href="http://www.html-kit.com/favicon/">HTML Kit</a> provides a favicon generator that is very easy to use.',AIOFAVICON_TEXTDOMAIN) ?>
                    </td>
                </tr>
                <tr>
                    <th scope="row">
                        <label for="faviconValidator"><?php _e('Favicon validator', AIOFAVICON_TEXTDOMAIN); ?>:</label>
                    </th>
                    <td id="faviconValidator">
                        <?php _e('<a href="http://www.html-kit.com/favicon/validator">HTML Kit</a> provides a favicon validator that tells you whether your favicon is working and if it is compatible to all browsers.',AIOFAVICON_TEXTDOMAIN) ?>
                    </td>
                </tr>
                <tr>
                    <th scope="row">
                        <label for="faviconAppleHowto"><?php _e('Apple Touch Icon Howto', AIOFAVICON_TEXTDOMAIN); ?>:</label>
                    </th>
                    <td id="faviconAppleHowto">
                        <?php _e('<a href="http://developer.apple.com/safari/library/documentation/internetweb/conceptual/iPhoneWebAppHIG/MetricsLayout/MetricsLayout.html">Apple</a> provides a howto on how to create a PNG to use as an Apple Touch Icon.',AIOFAVICON_TEXTDOMAIN) ?>
                    </td>
                </tr>
              </table>
            </div>
        </div>
    </div>
    </div>
    <div class="postbox-container" style="width: 29%;">
    <div id="poststuff">
        <div id="jquery-colorbox-topdonations" class="postbox">
            <h3 id="topdonations"><?php _e('Top donations', AIOFAVICON_TEXTDOMAIN) ?></h3>

            <div class="inside">
                <?php echo $this->getRemoteContent(AIOFAVICON_TOPDONATEURL); ?>
            </div>
        </div>
    </div>
    <div id="poststuff">
        <div id="jquery-colorbox-latestdonations" class="postbox">
            <h3 id="latestdonations"><?php _e('Latest donations', AIOFAVICON_TEXTDOMAIN) ?></h3>

            <div class="inside">
                <?php echo $this->getRemoteContent(AIOFAVICON_LATESTDONATEURL); ?>
            </div>
        </div>
    </div>
    <div id="poststuff">
        <div id="jquery-colorbox-donate" class="postbox">
            <h3 id="donate"><?php _e('Donate', AIOFAVICON_TEXTDOMAIN) ?></h3>

            <div class="inside">
                <p>
                    <?php _e('If you would like to make a small (or large) contribution towards future development please consider making a donation.', AIOFAVICON_TEXTDOMAIN) ?>
                </p>
                <form action="https://www.paypal.com/cgi-bin/webscr" method="post">
                    <input type="hidden" name="cmd" value="_xclick" />
                    <input type="hidden" name="business" value="G75G3Z6PQWXXQ" />
                    <input type="hidden" name="item_name" value="<?php _e('Techotronic Development Support' , AIOFAVICON_TEXTDOMAIN); ?>" />
                    <input type="hidden" name="item_number" value="jQuery Colorbox"/>
                    <input type="hidden" name="no_shipping" value="0"/>
                    <input type="hidden" name="no_note" value="0"/>
                    <input type="hidden" name="cn" value="<?php _e("Please enter the URL you'd like me to link to in the donors lists", AIOFAVICON_TEXTDOMAIN); ?>." />
                    <input type="hidden" name="return" value="<?php $this->getReturnLocation(); ?>" />
                    <input type="hidden" name="cbt" value="<?php _e('Return to Your Dashboard' , AIOFAVICON_TEXTDOMAIN); ?>" />
                    <input type="hidden" name="currency_code" value="USD"/>
                    <input type="hidden" name="lc" value="US"/>
                    <input type="hidden" name="bn" value="PP-DonationsBF"/>
                    <label for="preset-amounts"><?php _e('Select Preset Amount', AIOFAVICON_TEXTDOMAIN); echo ": "; ?></label>
                    <select name="amount" id="preset-amounts">
                        <option value="10">10</option>
                        <option value="20" selected>20</option>
                        <option value="30">30</option>
                        <option value="40">40</option>
                        <option value="50">50</option>
                        <option value="100">100</option>
                    </select><span><?php _e('USD',AIOFAVICON_TEXTDOMAIN) ?></span>
                    <br /><br /><?php _e('Or', AIOFAVICON_TEXTDOMAIN); ?><br /><br />
                    <label for="custom-amounts"><?php _e('Enter Custom Amount', AIOFAVICON_TEXTDOMAIN); echo ": "; ?></label>
                    <input type="text" name="amount" size="4" id="custom-amounts"/>
                    <span><?php _e('USD',AIOFAVICON_TEXTDOMAIN) ?></span>
                    <br /><br />
                    <input type="submit" value="<?php _e('Submit',AIOFAVICON_TEXTDOMAIN) ?>" class="button-secondary"/>
                </form>
            </div>
        </div>
    </div>
    <div id="poststuff">
        <div id="jquery-colorbox-translation" class="postbox">
            <h3 id="translation"><?php _e('Translation', AIOFAVICON_TEXTDOMAIN) ?></h3>

            <div class="inside">
                <p><?php _e('The english translation was done by <a href="http://www.techotronic.de">Arne Franken</a>.', AIOFAVICON_TEXTDOMAIN); ?></p>
            </div>
        </div>
    </div>
    </div>
    </div>
    <div class="clear">
        <p>
            <br/>&copy; Copyright 2010 - <?php echo date("Y"); ?> <a href="http://www.techotronic.de">Arne Franken</a>
        </p>
    </div>

</div>