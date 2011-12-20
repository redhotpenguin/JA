(function() {  
    tinymce.create('tinymce.plugins.ja_comment', {  
        init : function(ed, url) {  
            ed.addButton('ja_comment', {  
                title : 'Embed a comment',  
                image : url+'/comment_ico.png',  
                onclick : function() {
					tinyMCE.execCommand('mceFocus', false, 'content');
                    ed.selection.setContent("[ja_comment comment_id = '123'] abc [/ja_comment]");  
  
                }  
            });  
        },  
        createControl : function(n, cm) {  
            return null;  
        },  
    });  
    tinymce.PluginManager.add('ja_comment', tinymce.plugins.ja_comment);  
})();