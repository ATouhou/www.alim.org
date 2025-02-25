﻿<?php
// $Id: views-view-field.tpl.php,v 1.1 2008/05/16 22:22:32 merlinofchaos Exp $
 /**
  * This template is used to print a single field in a view. It is not
  * actually used in default Views, as this is registered as a theme
  * function which has better performance. For single overrides, the
  * template is perfectly okay.
  *
  * Variables available:
  * - $view: The view object
  * - $field: The field handler object that can process the input
  * - $row: The raw SQL result that can be used
  * - $output: The processed output that will normally be used.
  *
  * When fetching output from the $row, this construct should be used:
  * $data = $row->{$field->field_alias}
  *
  * The above will guarantee that you'll always get the correct data,
  * regardless of any changes in the aliasing that might happen if
  * the view is modified.
  */
  
  
// Correcting the arabic text display in Chrome
   $var="<span class='sign1'>&nbsp;</span>";
   if(arg(4)!=1 && arg(4)!=9 && !($_SESSION['arabicqry']))
   {
     $_SESSION['arabicqry']=0;
	 $viewName = 'arabic_text';	  
   $view_11 = views_get_view($viewName);
   $view_11->set_display('default');
   $view_11->set_arguments(array(1,1,arg(5))); 
   $view_11->execute();
   $result_11 = $view_11->result;
   $exp = explode($result_11[0]->node_revisions_body,$output);
	 if($exp[1]!="")
	 {
		   		print $exp[1]; // wrap the text
	 }
	 else
	 {
	    	    print $output;
     }
   }
   else
   {
		     	print $output;
   }
?>

