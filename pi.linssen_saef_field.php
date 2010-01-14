<?php

/*
=====================================================
   Linssen Stand Alone Entry Fields
   by: Wil Linssen
   http://wil-linssen.com/
=====================================================
  This software is based upon and derived from
  EllisLab ExpressionEngine software protected under
  copyright dated 2004 - 2007. Please see
  www.expressionengine.com/docs/license.html
=====================================================
  File: pi.linssen_saef_field.php
-----------------------------------------------------
  Purpose: Linssen Stand Alone Entry Fields
=====================================================

*/

$plugin_info = array(
	'pi_name'			=> 'Linssen SAEF Field',
	'pi_version'		=> '1.0',
	'pi_author'			=> 'Wil Linssen',
	'pi_author_url'		=> 'http://wil-linssen.com/',
	'pi_description'	=> 'Will return correctly formatted custom fields for use in stand alone entry forms',
	'pi_usage'			=> Linssen_saef_field::usage()
);


class Linssen_saef_field {

  var $return_data;
  var $field_info = array();
  
	/**
	* ---------------------------------------------------------------------------------------------------------
	* Linssen SAEF Field
	*
	* ---------------------------------------------------------------------------------------------------------
	*/
  function Linssen_saef_field()
  {
    global $DB, $TMPL, $PREFS;
    
    // Have they chosen some wonky db prefix?
    $this->db_prefix = $PREFS->core_ini['db_prefix'];
    $tagdata = $TMPL->tagdata;
    
    // Get all the field's info from the (unique) name
    $sql = "SELECT `field_id`,`field_name`,`field_label`,`field_type`,`field_related_id`,`field_list_items`
      FROM `".$this->db_prefix."_weblog_fields`
      WHERE `site_id` = ".$PREFS->core_ini['site_id']."
      AND `field_name` = '".$TMPL->fetch_param('name')."'";
    $query = $DB->query($sql);
    $this->field_info = $query->row;
    
    // Now swap out any single variables
    foreach ($TMPL->var_single as $key => $val)
    {
      if ( ereg("^id", $key) )
        $tagdata = str_replace(LD."id".RD,"field_id_".$this->field_info['field_id'],$tagdata);
      
      if ( ereg("^label", $key) )
        $tagdata = str_replace(LD."label".RD,$this->field_info['field_label'],$tagdata);
        
      if ( ereg("^name", $key) )
        $tagdata = str_replace(LD."label".RD,$this->field_info['field_name'],$tagdata);
        
    }
    
    // Now for the pairs
    foreach ($TMPL->var_pair as $key => $val)
    {
      // Options for a select box
      if ( ereg("^options", $key) )
      {
        $data	= $TMPL->fetch_data_between_var_pairs($TMPL->tagdata, $key);
        $options = "";
        
        // This conditional will ensure we only retrieve select and relative options
        // Starting with the select
        if ( "select" == $this->field_info['field_type'] )
        {
          $rows = explode("\n",$this->field_info['field_list_items']);
          foreach ($rows as $option)
          {
            $r = str_replace(LD."value".RD,$option,$data);
            $r = str_replace(LD."name".RD,$option,$data);
            $options .= $r;
          }
        }
        
        // Now for all relative options - the entry's name and id
        elseif ( "rel" == $this->field_info['field_type'] )
        {
          $sql = "SELECT `entry_id`,`title`
            FROM `".$this->db_prefix."_weblog_titles`
            WHERE `site_id` = ".$PREFS->core_ini['site_id']."
            AND `weblog_id` = ".$this->field_info['field_related_id']."
            ORDER BY `title`";
          $query = $DB->query($sql);
          foreach ($query->result as $option)
          {
            $r = str_replace(LD."value".RD,$option['entry_id'],$data);
            $r = str_replace(LD."name".RD,$option['title'],$data);
            $options .= $r;
          }
        }
        
        // Replace the template tags with the new data
        $pattern = "/".LD.$key.RD."(.*?)".LD.SLASH.$key.RD."/s";
        $tagdata = preg_replace($pattern,$options,$tagdata);
      }
    }
    
    $this->return_data = $tagdata;
 		
  }
  /* END */
  
  /**
  * ---------------------------------------------------------------------------------------------------------
  * Plugin usage
  *
  * ---------------------------------------------------------------------------------------------------------
  */
  function usage()
  {
  ob_start(); 
?>
When building your stand alone entry form use the plugin to fetch your custom field info:

The 'name' parameter is mandatory

For a simple text box you would use:
{exp:linssen_saef_field name="summary"}
  <p><label for="{id}">{label}</label><br />
    <input type="text" name="{id}" id="{id}" value="" />
  </p>
{/exp:linssen_saef_field}

For a select box (which could be a select or relationship field type) use:
{exp:linssen_saef_field name="summary"}
  <p><label for="{id}">{label}</label><br />
    <select name="{id}" id="{id}">
      {options}<option value="{value}">{name}</option>{/options}
    </select>
  </p>
{/exp:linssen_saef_field}
<?php

  $buffer = ob_get_contents();

  ob_end_clean(); 

  return $buffer;
  }
  /* END */

}
// END CLASS
?>