function containerSelect(id) {
	containerUnselect();
	if(document.selection) {
	   var range = document.body.createTextRange();
	   range.moveToElementText(id);
	   range.select();
	   }
	else if(window.getSelection) {
	   var range = document.createRange();
	   range.selectNode(id);
	   window.getSelection().addRange(range);
	   }
}
function containerUnselect() {
	if (document.selection) { document.selection.empty(); }
	else if (window.getSelection) { window.getSelection().removeAllRanges(); }
}
jQuery(document).ready(function($) {
	
	$('.checkbox_with_all').find('input').click(function(event) {
		if($(this).hasClass('e2t_all_input')) {
			if( $(this).is(":checked") ) {
				$(this).closest('.checkbox_with_all').find('input.e2t_input').attr('checked', false)			
			}
		}
		if($(this).hasClass('e2t_input')) {
			$(this).closest('.checkbox_with_all').find('input.e2t_all_input').attr('checked', false)
		}		
	});
	
	$("a.button-secondary").click(function(event){ // function launched when submiting form
		
		event.preventDefault(); //disable default behavior
		
		$("#pre_holder").show();
		
		var $form = $( "#export-to-text-form" ) //sets variable for the form
		
		$("#pre").html('<strong>Loading...</strong>'); //sets text while loading in pre
		
		var author_ready = new Array(); //prepers data for authors
		$.each($( 'input[name="author[]"]:checked' ), function() {
			author_ready.push($(this).val());
		});
		
		var ptype_ready = new Array(); //prepers data for post type(ptype)
		$.each($( 'input[name="ptype[]"]:checked' ), function() {
			ptype_ready.push($(this).val());
		});
		
		var post_status_ready = new Array(); //prepers data for post status
		$.each($( 'input[name="post_status[]"]:checked' ), function() {
			post_status_ready.push($(this).val());
		});		
		
		var taxonomies_ready = {}; //picks taxonomies and loops trough data
		$.each($( 'input[name*="taxonomy"]' ), function() {
			var taxonomy_name = $(this).attr("name").replace('][]','');
			var taxonomy_name = taxonomy_name.replace('taxonomy[','');
			
			if( taxonomy_name in taxonomies_ready == false ) {//if taxonomy not checked, data is picked
				taxonomies_ready[taxonomy_name] = {};
				var counter = 0;
				$.each($( 'input[name="taxonomy['+taxonomy_name+'][]"]:checked' ), function() {
					taxonomies_ready[taxonomy_name][counter] = $(this).val();
					counter = counter+1;
				});				
			}			
		});
		
		var data_filter_ready = new Array(); //prepers data for pdata filter
		$.each($( 'input[name="data_filter[]"]:checked' ), function() {
			data_filter_ready.push($(this).val());
		});	
			
		var data = { //looks for and sets all variables used for export
			action: 'sre2t_ajax',
			sdate: $form.find( 'select[name="sdate"]' ).val(),
			edate: $form.find( 'select[name="edate"]' ).val(),
			author: author_ready,
			post_type: ptype_ready,
			taxonomy: taxonomies_ready,
			post_status: post_status_ready,
			cfname: $form.find( 'input[name="cfname"]' ).val(),
			cfvalue: $form.find( 'input[name="cfvalue"]' ).val(),
			data_filter: data_filter_ready,
			download: 0
		};
		
		$.post(ajaxurl, data, function(data){ //post data to specified action trough special WP ajax page
			$("#pre").html(data); //displays data in pre
		});

	});
	
	$("#pre_close").click(function(event){
		event.preventDefault(); //disable default behavior
		$("#pre_holder").hide();
	});
});