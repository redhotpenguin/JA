jQuery(document).ready( function($) {
	var DUPE_MSG = '<b>You have already said that.</b>';
	var ERROR_MSG = '<b>An error has occured.</b>';
	var NO_MORE_COMMENTS_MSG = 'No More Comments';
	var WRITE_COMMENT_MSG = 'Write a comment...';
	var LOADING_ICON = '<img src="/wp-content/plugins/comment_flow/img/ajax-loader.gif" />';
	var commentForm = $('#commentform');
	var loadingIcon = $( LOADING_ICON );
	var commentList = $('ol.commentlist');
	var infoBox = $('<div id="cflow-info"> </div>');
	var errorBox = $('<div id="cflow-error"> </div>');
	var cflow_more_wheel = $('.cflow_more_wheel');
	var cflow_more_msg = $('.cflow_more_msg');
	var show_all_comments_btn = $('.show_all_comments');
	var comment_form_textarea = $('#comment');
	var cflow_counter = $('.cflow_counter');
	var mutex_comment_is_loading = false;
	var openForm = false;
	var bottom_reply_link = $("#bottom_reply_link");

	infoBox.html( loadingIcon );
	infoBox.hide();

	comment_form_textarea.val( WRITE_COMMENT_MSG );

	infoBox.insertBefore($('#comment_clearfix'));
	errorBox.insertBefore($('#comment_clearfix'));

	add_reply_btn_to_children();
	add_logout_btn_to_children();
	
	// BIND EVENTS
	commentForm.submit( submit_comment );
	comment_form_textarea.click( reset_comment_form );
	comment_form_textarea.keydown( reset_comment_form );
	$(window).scroll( handle_comments_on_scroll );
	bottom_reply_link.click( open_main_form );
	show_all_comments_btn.click( load_all_comments );
	$('#cancel-comment-reply-link').click(function(){ errorBox.html(''); infoBox.hide(); });
	$('.comment-reply-link').click(function(){ errorBox.html(''); });

	anchor_cookie = getCookie('anchor_to_comment');
	if( anchor_cookie && anchor_cookie != '#' && anchor_cookie != 'undefined' ){
		document.location.hash = "comment-"+anchor_cookie;
		openForm = true;
		more_comments( true );
		setCookie('anchor_to_comment', '#', -1);
	}
	else if( window.location.hash != '' ){
		more_comments( true );
		unbind_comments_on_scroll();
	}
	
	function submit_comment( ev ){
		infoBox.show();
		errorBox.html('');
		var formUrl=commentForm.attr('action');
		
		post = $.ajax({
		  async: true,
		  type: 'POST',
		  url: formUrl,
		  data: commentForm.serialize(),
		  success: submit_comment_callback,
		  timeout: 20000,
		  error: function(){
			comment_posting_error( ERROR_MSG );
		  }
		});	

		return false;
	} 
	
	function comment_posting_error( MSG ){
		errorBox.html( MSG )
		comment_form_textarea.val( WRITE_COMMENT_MSG );
		infoBox.hide();
		return false;
	}
	
	function submit_comment_callback( data, textStatus ){
		if( data == 'dup' ){
			comment_posting_error( DUPE_MSG );
			return false;
		}
			
		else if( data == null){
			comment_posting_error( ERROR_MSG );
			return false;
		}	
		
		comment_form_textarea.val( WRITE_COMMENT_MSG);
		if(  data.cpid  > 0){ //if the comment is a child
			if( $("#comment-"+data.cpid).find('.children').length != 0){
				$("#comment-"+data.cpid).find('.children').append( data.comment );
			}
			else{
			 ul_children = $("#comment-"+data.cpid).append('<ul class="children"></ul>').find('ul');
			 ul_children.append(data.comment);
			}
			
			if ( !($.browser.msie  && parseInt($.browser.version) == 8) ) 
				add_reply_btn_to_children( $(data.comment).attr('id') );
		}
		
		else{ // comment is not a child (new thread)
		
			if(	commentList.length == 0 ){ // no comments posted, need to create a container first 
				commentList = $('<div id="comments"><h3>One Response:</h3><ol class="commentlist"> </ol></div>').insertAfter("#post-"+cflow.pid).find('ol.commentlist');
			}

			commentList.prepend(data.comment);
		}
		
		
		comment_n = parseInt( cflow_counter.text() );
		cflow_counter.text( comment_n + 1);
		$('.custom_comments_reloaded').remove();
		infoBox.hide();
		errorBox.html('');

	}

	function handle_comments_on_scroll(){
		if( is_near_bottom( 3 ) == true && !mutex_comment_is_loading ){
			mutex_comment_is_loading = true;
			more_comments();
		}

	}
	
	function unbind_comments_on_scroll(){
		$(window).unbind("scroll");
	}
	
	function is_near_bottom( factor ) {
	  var documentHeight = $(document).height(); 
	  var scrollPosition = $(window).height() + $(window).scrollTop(); 
	 
	  virtualHeight = parseInt( documentHeight / factor) ;
	  return ( (scrollPosition == documentHeight ) || ( scrollPosition >= documentHeight - virtualHeight ) );
	} 
	
	function more_comments( all, is_async ){
		if( all != true)
			all = false;
			
		if( is_async != false)
			is_async = true;
			
		var lastTopCommentId =  parseInt( $('.commentlist > li.comment').last().attr('id').replace('comment-','') );
		$.ajaxSetup( { async: is_async } )	
		
		var data = {
			action: 'cflow_more_c',
			pid : cflow.pid,
			lastComment : lastTopCommentId,
			loadAll : all
		};
	
		cflow_more_wheel.show();
		jQuery.post(cflow.ajaxurl, data, more_comments_callback );
	}

	function more_comments_callback( response, status, xhr ){
		commentList.append(response);
		 
		cflow_more_wheel.hide();

		if( window.location.hash != '' ){ // paged is called with a hashtag #comment-x
			
			unbind_comments_on_scroll();
			if( openForm ){
				$(window.location.hash).find('.comment-reply-link').click();
			}
			
			else
				window.location = window.location; // refresh position
		}	
		else if(response == ''){ // no more comments to load
			unbind_comments_on_scroll();
			cflow_more_msg.html( NO_MORE_COMMENTS_MSG );
			cflow_more_msg.fadeIn( 666 );
		}
		
		bottom_reply_link.fadeIn( 666 );
		add_reply_btn_to_children();
		add_logout_btn_to_children();
		mutex_comment_is_loading = false;
	}

	function load_all_comments(){
		lastCommentId = $('.commentlist  li.comment').last().attr('id');
		more_comments(true);
		window.location.hash = "#" + lastCommentId;
		unbind_comments_on_scroll();
		bottom_reply_link.fadeIn( 666 );
		return false;
	}
	
	function reset_comment_form(){	
		if( ($(this).val()).replace(/ /g,'').toLowerCase() == WRITE_COMMENT_MSG.replace(/ /g,'').toLowerCase() ){
			$(this).val('');	
		}
	}
		

function add_reply_btn_to_children( comment_id ){
	if( comment_id != undefined ){
			comment = $('#'+ comment_id);
			parent_comment = comment.parent().parent( 'li.comment' ).first(); 
			reply = parent_comment.find('.comment-content').first().find('.comment-reply-link');
			reply.clone().prependTo( comment.find('.comment-options') );
		}
	
	else{
		$('.commentlist > li.comment .comment-reply-link').each(function() {
			var reply = $(this);
			var lastChildren = reply.closest('.comment').find('.children  .comment');
	
			if( lastChildren.find('.comment-options').find('.comment-reply-link').length  ==  0  ){
				reply.clone().prependTo(lastChildren.find('.comment-options'));
			}
			});
		}
	}
	
	function add_logout_btn_to_children(  ){
		$('.commentlist > li.comment .login-reply-link').each(function() {
			var reply = $(this);
			var lastChildren = reply.closest('.comment').find('.children > .comment');
			if( lastChildren.find('.comment-options').find('.login-reply-link').length == 0 ){
			reply.clone().prependTo(lastChildren.find('.comment-options'));
			}
		});	
	}
	
	function open_main_form(){
		$('#cancel-comment-reply-link').click();
		window.location.hash = "comments";
		window.location = window.location;
	}
	
	function setCookie( c_name,value,exdays ){
		var exdate=new Date();
		exdate.setDate(exdate.getDate() + exdays);
		var c_value=escape(value) + ((exdays==null) ? "" : "; ");
		document.cookie=c_name + "=" + c_value;
	}

	function getCookie( c_name ) {
		var i,x,y,ARRcookies=document.cookie.split(";");
		for (i=0;i<ARRcookies.length;i++)
		{
		  x=ARRcookies[i].substr(0,ARRcookies[i].indexOf("="));
		  y=ARRcookies[i].substr(ARRcookies[i].indexOf("=")+1);
		  x=x.replace(/^\s+|\s+$/g,"");
		  if (x==c_name)
			{
			return unescape(y);
			}
		  }
	}

});