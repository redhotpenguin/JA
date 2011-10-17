/*
| --------------------------------------------------------
| File        : srp-init.php
| Version     : 1.9
| Description : Custom js init file.
| Project     : Special Recent Posts plugin for Wordpress
| Author      : Luca Grandicelli
| Author URL  : http://www.lucagrandicelli.com
| Plugin URL  : http://www.lucagrandicelli.com/special-recent-posts-plugin-for-wordpress/
| --------------------------------------------------------
*/

function srpTabsSwitcher(mode, id) {
	
	// Switching mode.
	switch(mode) {
		case "tobasic":
			
			// Adding active class to tab links.
			jQuery('a.srp_tab_basic_link').addClass('active');
			jQuery('a.srp_tab_advanced_link').removeClass('active');
			
			// Switching Tab.
			jQuery('ul#srp-widget-optionlist-advanced-' + id).hide();
			jQuery('ul#srp-widget-optionlist-basic-'    + id).show();
		break;
		
		case "toadvanced":
		
			// Adding active class to tab links.
			jQuery('a.srp_tab_advanced_link').addClass('active');
			jQuery('a.srp_tab_basic_link').removeClass('active');
			
			// Switching Tab.
			jQuery('ul#srp-widget-optionlist-basic-'    + id).hide();
			jQuery('ul#srp-widget-optionlist-advanced-' + id).show();
		break;
	}
}
