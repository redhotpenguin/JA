jQuery(document).ready(function($){
    $(".pull-this-mark").each(function(index) {
        var pullQuote = $(this).html();
        var pullId = $(this).attr("id").substr(14);
        $("#pull-this-show"+pullId).html(pullQuote).show();
     });
     $(".pull-this-show:odd").addClass('quote-left');
});
