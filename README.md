Linssen SAEF fields
======================================================================

A way to retrieve the info for your EE custom fields in a stand alone entry form (or anywhere else for that matter)

Requirements
----------------------------------------------------------------------
- ExpressionEngine 1.6+
- PHP 5+

Installation
----------------------------------------------------------------------

1. Upload the pi.linssen_saef_fields to the `system/plugins/` folder to

Use
----------------------------------------------------------------------
When building your stand alone entry form use the plugin to fetch your custom field info:

The `name` parameter is mandatory

For a simple text box you would use:
`{exp:linssen_saef_field name="summary"}
  <p><label for="{id}">{label}</label><br />
    <input type="text" name="{id}" id="{id}" value="" />
  </p>
{/exp:linssen_saef_field}`

For a select box (which could be a select or relationship field type) use:
`{exp:linssen_saef_field name="summary"}
  <p><label for="{id}">{label}</label><br />
    <select name="{id}" id="{id}">
      {options}<option value="{value}">{name}</option>{/options}
    </select>
  </p>
{/exp:linssen_saef_field}`