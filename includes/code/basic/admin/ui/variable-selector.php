<?php

/**
 * Provides variable selection functionality.
 * 
 * @since      1.0.0
 * @package    C9_Variables
 * @subpackage C9_Variables/includes/code/basic/admin/ui
 * @author     CloudNineApps
 */
?>
<html>
  <head>
    <title>Variable Selection</title>
    <meta http-equiv="cache-control" content="no-cache"/>
    <meta http-equiv="expires" content="0"/>
    <meta http-equiv="pragma" content="no-cache"/>
    <link rel="stylesheet" href="../../../../../admin/css/common/c9-common.css"/>
    <style type="text/css">
      body {
        font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, Oxygen-Sans, Ubuntu, Cantarell, "Helvetica Neue", sans-serif;
        font-size: 13px;
        line-height: 1.4em;
      }
      
      input {
        padding: 5px;
        margin: 8px;
        display: inline-block;
        border: 1px solid #ccc;
        border-radius: 3px;
        box-sizing: border-box;
      }
    </style>
  </head>
  <body>
    <table width="95%" style="padding: 5px;">
      <tr>
        <td>Search</td>
        <td><input id="c9SearchVar" type="text" size="30"/></td>
      </tr>
      <tr>
        <td>Selected Variable</td>
        <td><input id="c9SelectedVar" type="text" size="30" readonly/></td>
      </tr>
      <tr>
        <td colspan="2">Available Variables</td>
      </tr>
      <tr>
        <td colspan="2">
          <div id="c9VarsContent">
            <ul class="c9-alternate-color-tabular-list" id="c9VarsList"></ul>
          </div> 
          <div id="c9VarsContentMsg"></div> 
        </td>
      </tr>
    </table>
    <script type="text/javascript">
      var windowMgr = top.tinymce.activeEditor.windowManager;
      var args = windowMgr.getParams();
      var editor = args['editor'];
      var $ = args['jquery'];
      var ajaxURL = args['ajaxURL'];
      var security = args['c9_vars_security'];
      var result = null;
      var ctx = document.getElementsByTagName("body")[0];
      var varsContentElem = document.getElementById('c9VarsContent');
      var varsContentMsgElem = document.getElementById('c9VarsContentMsg');

      /** Shows the variables search result. */
      function showResult(parentElem, vars) {
        // Maintain focus in the keywords field
        searchVarElem.focus();
        
        // Show list of variables
        var variables = result.data;
        var total = Number(result.total);
        if (total > 0) {
          varsContentElem.style.display = 'block';
          varsContentMsgElem.style.display = 'none';
          $.each(variables, function(key, variable) {
            vars.append('<li id="' + variable.name + '">' + variable.display_name + '</li>');
          });
        }
        else if (searchVarElem.value && total == 0) {
          // Search criteria specified, but no match found 
          showMessage("No matching variables found.");
        }
        else if (total == 0) {
          // No variables exist
          showMessage("In order to select a variable, please add one or more variables via <b>Variables->All Variables</b>.");
        }
      }

      // Shows message
      function showMessage(msg) {
        varsContentElem.style.display = 'none';
        varsContentMsgElem.style.display = 'block';
        varsContentMsgElem.innerHTML = "<p>" + msg + "</p>";
      }
      
      var varsList = $('#c9VarsList', ctx);
      var varListElem = document.getElementById('c9VarsList');
      var searchVarElem = document.getElementById('c9SearchVar');
      
      // Event handlers
      varListElem.onclick = function(event) {
        event.preventDefault();
        var elem = event.target || window.event.srcElement;
        var variable = elem.id;
        var selectedVarElem = document.getElementById('c9SelectedVar');
        selectedVarElem.value = variable;
        editor.c9SelectedVar = variable;
      };
      var searchTimer = null;
      var searchVariables = function() {
        if (searchTimer) {
          clearTimeout(searchTimer);
        }
        // Search variables matching criteria
        $.ajax({
          url: ajaxURL,
          type: 'post',
          data : {
            action: 'c9_vars_get_variables',
            keywords: searchVarElem.value,
            c9_vars_security: security
          },
          success: function(response) {
            // Show search results
            result = response;
            varsList.empty();
            showResult(varsContentElem, varsList);
          }
        });
      };
      searchVarElem.onkeyup = function(event) {
        if (event.keyCode == 27) {
          windowMgr.close();
        }
        else {
          searchTimer = setTimeout(searchVariables, 300);
        }
      };

      // Perform a search on launch
      showMessage("Searching variables...");
      searchVariables();
    </script>
  </body>
</html>
