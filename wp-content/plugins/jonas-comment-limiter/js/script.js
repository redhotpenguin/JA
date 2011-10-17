
(function($){ 
     $.fn.extend({  
         limit: function(limit,element,msg_span, msg) {
			
			var interval, f;
			var self = $(this);
					
			$(this).focus(function(){
				interval = window.setInterval(substring,100);
			});
			
			$(this).blur(function(){
				clearInterval(interval);
				substring();
			});
			//$(msg_span).text('');
			substringFunction = "function substring(){ var val = $(self).val();var length = val.length; if(length > limit){$(self).val($(self).val().substring(0,limit));$(msg_span).text(msg); }";
			if(typeof element != 'undefined'){
				substringFunction += "if($(element).html() != limit-length){$(element).html((limit-length<=0)?'0':limit-length);}"
				substringFunction += "if($(element).html() > 0) $(msg_span).text(''); ";
				substringFunction += "}";
			}
			eval(substringFunction);
			
			
			
			substring();
			
        } 
    }); 
})(jQuery);