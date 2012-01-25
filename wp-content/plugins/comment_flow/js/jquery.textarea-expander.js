/**
 * TextAreaExpander plugin for jQuery - v1.0
 * Expands or contracts a textarea height depending on the
 * quatity of content entered by the user in the box.
 * By Craig Buckler, Optimalworks.net
 * As featured on SitePoint.com:
 * http://www.sitepoint.com/blogs/2009/07/29/build-auto-expanding-textarea-1/
 * Please use as you wish at your own risk.
 */

(function(a){a.fn.TextAreaExpander=function(b,c){function e(a){a=a.target||a;var b=a.value.length,c=a.offsetWidth;if(b!=a.valLength||c!=a.boxWidth){if(d&&(b<a.valLength||c!=a.boxWidth))a.style.height="0px";var e=Math.max(a.expandMin,Math.min(a.scrollHeight,a.expandMax));a.style.overflow=a.scrollHeight>e?"auto":"hidden";a.style.height=e+"px";a.valLength=b;a.boxWidth=c}return true}var d=!(a.browser.msie||a.browser.opera);this.each(function(){if(this.nodeName.toLowerCase()!="textarea")return;var d=this.className.match(/expand(\d+)\-*(\d+)*/i);this.expandMin=b||(d?parseInt("0"+d[1],10):0);this.expandMax=c||(d?parseInt("0"+d[2],10):99999);e(this);if(!this.Initialized){this.Initialized=true;a(this).css("padding-top",0).css("padding-bottom",0);a(this).bind("keyup",e).bind("focus",e)}});return this}})(jQuery);jQuery(document).ready(function(){jQuery("textarea[class*=expand]").TextAreaExpander()})