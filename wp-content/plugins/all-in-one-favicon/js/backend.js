/*
 * @package Techotronic
 * @subpackage All In One Favicon
 *
 * @since 1.0
 * @author Arne Franken
 *
 */
jQuery(document).ready(function($) {

    $("td").each(function(index, obj) {
        var $buttonId;
        var $textId;
        $nestedElement = $(obj).children("input.aioFaviconUpload");
        $nestedElement.click(function(){
            $textInput = $(this).siblings(".aioFaviconUrl");
            textInputId = $textInput.attr("id");
            tb_show('',"media-upload.php?type=image&TB_iframe=true");
            return false;
        });
    });
    
    window.send_to_editor = function(html) {
        imgurl = jQuery('img',html).attr('src');
        jQuery("input"+"#"+textInputId).val(imgurl);
        tb_remove();
    }
});