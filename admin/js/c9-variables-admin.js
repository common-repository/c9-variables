jQuery(document).ready(function($) {
  'use strict';
  if (typeof tinymce === 'undefined') {
    return;
  }
  tinymce.PluginManager.add('c9_vars_variables_button', function(editor, url) {
    editor.addButton('c9_vars_variables_button', {
      title: 'Variables',
      icon: 'icon c9-variables-icon',
      onclick: function() {
        //console.log('get_variables(): resp: ' + response);
        editor.windowManager.open(
           {
             title: 'Variable Selection',
             width: 600,
             height: 400,
             inline: 1,
             autoScroll: true,
             url: url + '/../../includes/code/basic/admin/ui/variable-selector.php',
             buttons: [
               {
                 text: 'Insert Variable',
                 class: 'button',
                 onclick: function(data) {
                   var variable = $('<div/>').text(editor.c9SelectedVar).html();
                   if (variable) {
                     editor.insertContent("[c9-vars-insert name='" + variable + "']");
                     tinymce.activeEditor.windowManager.close();
                     $.ajax({
                       url: c9_vars_update_variable_last_used.ajax_url,
                       type: 'post',
                       data : {
                         action: 'c9_vars_update_variable_last_used',
                         name: variable,
                         c9_vars_security: c9_vars_update_variable_last_used.c9_vars_security
                       },
                       success: function(response) {
                         //console.log('update_variable_last_used(): Updated timestamp.');
                       }
                     });
                   }
                 }
               },
               {
                 text: 'Cancel',
                 onclick: 'close'
               }
             ]
           },
           {
             editor: editor,
             jquery: $,
             ajaxURL: c9_vars_get_variables.ajax_url,
             c9_vars_security: c9_vars_get_variables.c9_vars_security
           }
        );
      }
    });
  });
  $(document).on('click', '#mce-modal-block', function() {
    tinyMCE.activeEditor.windowManager.close();
  });
});