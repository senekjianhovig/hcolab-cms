<?php

namespace hcolab\cms\repositories;


class Element extends Column
{

    public $elements;

    public function getElements()
    {
        return $this->elements;
    }

    public function ExternalTextField($target_page , $label, $container, $required, $field_name,  $field_length = 255, $field_default = NULL, $is_multi_language = false){
        $ui = new \StdClass;
        $ui->type = "external textfield";
        $ui->label = $label;
        $ui->container = $container;
        $ui->required = $required;

        $std = new \StdClass;
        $std->name = $field_name;
        $std->target_page = $target_page;
        $std->ui = $ui;
        $std->db = set_db($field_name, "varchar", $field_length, $field_default, $is_multi_language);

        $this->elements = $this->elements->push($std);

        return $this;
    }

    public function EcomInventory($target_page , $name){
        $ui = new \StdClass;
        $ui->type = "ecom inventory";
        $ui->label = "Stock Quantity";
        $ui->container = null;
        $ui->required = true;

        $std = new \StdClass;
        $std->name = $name;
        $std->target_page = $target_page;
        $std->ui = $ui;
        $std->db = set_db("stock_quantity", "bigint", null, 0, false);

        $this->elements = $this->elements->push($std);

        return $this;
    }


    public function EcomPricing($target_page , $name){
        $ui = new \StdClass;
        $ui->type = "ecom pricing";
        $ui->label = null;
        $ui->container = null;
        $ui->required = true;

        $std = new \StdClass;
        $std->name = $name;
        $std->target_page = $target_page;
        $std->ui = $ui;
        $std->db = null;

        $this->elements = $this->elements->push($std);

        return $this;
    }


    public function TextField($label, $container, $required, $field_name,  $field_length = 255, $field_default = NULL, $is_multi_language = false)
    {
        $ui = new \StdClass;
        $ui->type = "textfield";
        $ui->label = $label;
        $ui->container = $container;
        $ui->required = $required;

        $std = new \StdClass;
        $std->name = $field_name;
        $std->ui = $ui;
        $std->db = set_db($field_name, "varchar", $field_length, $field_default, $is_multi_language);

        $this->elements = $this->elements->push($std);

        return $this;
    }

    public function Slug($label, $container, $required, $field_name,  $slugable_by ,$field_length = 255, $field_default = NULL, $is_multi_language = false)
    {
        $ui = new \StdClass;
        $ui->type = "slug";
        $ui->label = $label;
        $ui->container = $container;
        $ui->required = $required;
        $ui->slugable_by = $slugable_by;
    
        $std = new \StdClass;
        $std->name = $field_name;
        $std->ui = $ui;
        $std->db = set_db($field_name, "varchar", $field_length, $field_default, $is_multi_language);

        $this->elements = $this->elements->push($std);

        return $this;
    }


    public function ForeignKey($label, $container, $required, $field_name , $field_type ="bigint" , $field_length = NULL)
    {

        $ui = new \StdClass;
        $ui->type = "select";
        $ui->label = $label;
        $ui->container = $container;
        $ui->required = $required;

        $std = new \StdClass;
        $std->name = $field_name;
        $std->ui = $ui;
        $std->db = set_db($field_name, $field_type, $field_length, NULL, false);

        $this->elements = $this->elements->push($std);

        return $this;
    }

    public function Tags($label, $container, $required, $field_name , $options = [])
    {

        $ui = new \StdClass;
        $ui->type = "tags";
        $ui->label = $label;
        $ui->container = $container;
        $ui->required = $required;
        $ui->options = $options;

        $std = new \StdClass;
        $std->name = $field_name;
        $std->ui = $ui;
        $std->db = set_db($field_name, "text", NULL, NULL, false);

        $this->elements = $this->elements->push($std);

        return $this;
    }

    public function MultipleSelect($label, $container, $required, $field_name , $options = [])
    {

        $ui = new \StdClass;
        $ui->type = "multiple select";
        $ui->label = $label;
        $ui->container = $container;
        $ui->required = $required;
        $ui->options = $options;

        $std = new \StdClass;
        $std->name = $field_name;
        $std->ui = $ui;
        $std->db = set_db($field_name, "text", NULL, NULL, false);

        $this->elements = $this->elements->push($std);

        return $this;
    }

    public function VariantsPanel($target_page , $variant_page , $product_price_page , $product_inventory_page){
        $ui = new \StdClass;
        $ui->type = "variants panel";
    
        $ui->target_page = $target_page;
        $ui->variant_page = $variant_page;
        $ui->product_price_page = $product_price_page;
        $ui->product_inventory_page = $product_inventory_page;

        $std = new \StdClass;
        $std->name = null;
        $std->ui = $ui;
        $std->db = null;

        $this->elements = $this->elements->push($std);

        return $this;
    }
    
    public function Select($label, $container, $required, $field_name, $options = [])
    {

        $ui = new \StdClass;
        $ui->type = "values select";
        $ui->label = $label;
        $ui->container = $container;
        $ui->required = $required;
        $ui->options = $options;

        $std = new \StdClass;
        $std->name = $field_name;
        $std->ui = $ui;
        $std->db = set_db($field_name, "text", NULL, NULL, false);

        $this->elements = $this->elements->push($std);

        return $this;
    }

    public function DisabledTextField($label, $container, $required, $field_name,  $field_length = 255, $field_default = NULL, $is_multi_language = false)
    {
        $ui = new \StdClass;
        $ui->type = "disabled_textfield";
        $ui->label = $label;
        $ui->container = $container;
        $ui->required = $required;

        $std = new \StdClass;
        $std->name = $field_name;
        $std->ui = $ui;
        $std->db = set_db($field_name, "bigint", $field_length, $field_default, $is_multi_language);

        $this->elements = $this->elements->push($std);

        return $this;
    }

    public function ReadOnlyTextField($label, $container,$required ,$field_name)
    {
        $ui = new \StdClass;
        $ui->type = "readonly_textfield";
        $ui->label = $label;
        $ui->container = $container;
        $ui->required = false;

        $std = new \StdClass;
        $std->name = $field_name;
        $std->ui = $ui;
        $std->db = set_db($field_name, "varchar", 255, NULL, 0);

        $this->elements = $this->elements->push($std);

        return $this;
    }

    public function HiddenTextField($label, $container, $required, $field_name,  $field_length = 255, $field_default = NULL, $is_multi_language = false)
    {
        $ui = new \StdClass;
        $ui->type = "hidden_textfield";
        $ui->label = $label;
        $ui->container = $container;
        $ui->required = $required;

        $std = new \StdClass;
        $std->name = $field_name;
        $std->ui = $ui;
        $std->db = set_db($field_name, "varchar", $field_length, $field_default, $is_multi_language);

        $this->elements = $this->elements->push($std);

        return $this;
    }

    public function BooleanCheckbox($label, $container, $required, $field_name , $related_fields=null)
    {

        $ui = new \StdClass;
        $ui->type = "boolean checkbox";
        $ui->label = $label;
        $ui->container = $container;
        $ui->required = $required;
        $ui->related_fields = $related_fields;

        $std = new \StdClass;
        $std->name = $field_name;
        $std->ui = $ui;
        $std->db = set_db($field_name, "tinyint", 1, 0, false);

        $this->elements = $this->elements->push($std);

        return $this;
    }

    


    public function TextAreaField($label, $container, $required, $field_name, $field_length = NULL, $field_default = NULL, $is_multi_language = false)
    {

        $ui = new \StdClass;
        $ui->type = "textarea";
        $ui->label = $label;
        $ui->container = $container;
        $ui->required = $required;

        $std = new \StdClass;
        $std->name = $field_name;
        $std->ui = $ui;
        $std->db = set_db($field_name, "text", $field_length, $field_default, $is_multi_language);

        $this->elements = $this->elements->push($std);

        return $this;
    }

    public function PasswordField($label, $container, $required, $field_name)
    {

        $ui = new \StdClass;
        $ui->type = "password";
        $ui->label = $label;
        $ui->container = $container;
        $ui->required = $required;

        $std = new \StdClass;
        $std->name = $field_name;
        $std->ui = $ui;
        $std->db = set_db($field_name, "varchar", 255, NULL, false);

        $this->elements = $this->elements->push($std);

        return $this;
    }


    public function DatePickerField($label, $container, $required, $field_name, $field_default = null)
    {

        $ui = new \StdClass;
        $ui->type = "date picker";
        $ui->label = $label;
        $ui->container = $container;
        $ui->required = $required;

        $std = new \StdClass;
        $std->name = $field_name;
        $std->ui = $ui;
        $std->db = set_db($field_name, "date", NULL, $field_default, false);

        $this->elements = $this->elements->push($std);

        return $this;
    }

    public function DateTimePickerField($label, $container, $required, $field_name, $field_default = null)
    {

        $ui = new \StdClass;
        $ui->type = "date time picker";
        $ui->label = $label;
        $ui->container = $container;
        $ui->required = $required;

        $std = new \StdClass;
        $std->name = $field_name;
        $std->ui = $ui;
        $std->db = set_db($field_name, "datetime", NULL, $field_default, false);

        $this->elements = $this->elements->push($std);

        return $this;
    }


    public function EditorTextField($label, $container, $required, $field_name)
    {

        $ui = new \StdClass;
        $ui->type = "wysiwyg";
        $ui->label = $label;
        $ui->container = $container;
        $ui->required = $required;

        $std = new \StdClass;
        $std->name = $field_name;
        $std->ui = $ui;
        $std->db = set_db($field_name, "longtext", NULL, NULL, false);

        $this->elements = $this->elements->push($std);

        return $this;
    }

    public function UrlField($label, $container, $required, $field_name, $is_multi_language = false)
    {

        $ui = new \StdClass;
        $ui->type = "url";
        $ui->label = $label;
        $ui->container = $container;
        $ui->required = $required;

        $std = new \StdClass;
        $std->name = $field_name;
        $std->ui = $ui;
        $std->db = set_db($field_name, "varchar", 255, NULL, $is_multi_language);

        $this->elements = $this->elements->push($std);

        return $this;
    }

    public function OpenDiv($classes){
        $ui = new \StdClass;
        $ui->type = "open div";
        $ui->classes = $classes;
       
        $std = new \StdClass;
        $std->name = null;
        $std->ui = $ui;
        $std->db = null;


        $this->elements = $this->elements->push($std);

        return $this;
    }

    public function Text($text){
        $ui = new \StdClass;
        $ui->type = "text";
        $ui->text = $text;
       
        $std = new \StdClass;
        $std->name = null;
        $std->ui = $ui;
        $std->db = null;


        $this->elements = $this->elements->push($std);

        return $this;
    }


    public function CloseDiv(){
        $ui = new \StdClass;
        $ui->type = "close div";
       
        $std = new \StdClass;
        $std->name = null;
        $std->ui = $ui;
        $std->db = null;


        $this->elements = $this->elements->push($std);

        return $this;
    }

    public function FileUploadField($label, $container, $required, $field_name , $accept = null , $resize = null)
    {

        $ui = new \StdClass;
        $ui->type = "file";
        $ui->label = $label;
        $ui->container = $container;
        $ui->required = $required;
        $ui->accept = $accept;
        $ui->resize = $resize;

        $std = new \StdClass;
        $std->name = $field_name;
        $std->ui = $ui;
        $std->db = set_db($field_name, "varchar", "255" , null, null);


        $this->elements = $this->elements->push($std);

        return $this;
    }

    public function FileMultipleUploadField($label, $container, $required, $field_name , $accept = null)
    {

        $ui = new \StdClass;
        $ui->type = "multiple file";
        $ui->label = $label;
        $ui->container = $container;
        $ui->required = $required;
        $ui->accept = $accept;

        $std = new \StdClass;
        $std->name = $field_name;
        $std->ui = $ui;
        $std->db = set_db($field_name, "text", null , null, null);

        
        
        $this->elements = $this->elements->push($std);
        
        return $this;
    }

    public function HiddenJsonField($field_name)
    {

        $ui = new \StdClass;
        $ui->type = "hidden json field";
    
        $std = new \StdClass;
        $std->name = $field_name;
        $std->ui = $ui;
        $std->db = set_db($field_name, "longtext", NULL, NULL, false);

        $this->elements = $this->elements->push($std);

        return $this;
    }

    /**
     * @param array $fields Legacy: ['fieldName' => 'type', ...]. New: array of objects with name, type, optional label, optional options (for select).
     *                      Example: [['name' => 'type', 'type' => 'select', 'label' => 'Type', 'options' => [['id' => 'a', 'label' => 'A']]], ['name' => 'value', 'type' => 'text']]
     */
    public function RepeaterField($label, $container, $required, $field_name, $fields = [])
    {
        $ui = new \StdClass;
        $ui->type = "repeater";
        $ui->label = $label;
        $ui->container = $container;
        $ui->required = $required;
        $ui->fields = $fields;

        $std = new \StdClass;
        $std->name = $field_name;
        $std->ui = $ui;
        $std->db = set_db($field_name, "longtext", NULL, NULL, false);

        $this->elements = $this->elements->push($std);

        return $this;
    }

    public function BigIntegerField($label, $container, $required, $field_name, $field_default = NULL, $is_multi_language = false)
    {
        $ui = new \StdClass;
        $ui->type = "big_integer";
        $ui->label = $label;
        $ui->container = $container;
        $ui->required = $required;

        $std = new \StdClass;
        $std->name = $field_name;
        $std->ui = $ui;
        $std->db = set_db($field_name, "bigint", NULL, $field_default, $is_multi_language);

        $this->elements = $this->elements->push($std);

        return $this;
    }

    public function DecimalField($label, $container, $required, $field_name, $precision = 10, $scale = 2, $field_default = NULL, $is_multi_language = false)
    {
        $ui = new \StdClass;
        $ui->type = "decimal";
        $ui->label = $label;
        $ui->container = $container;
        $ui->required = $required;
        $ui->precision = $precision;
        $ui->scale = $scale;

        $std = new \StdClass;
        $std->name = $field_name;
        $std->ui = $ui;
        $std->db = set_db($field_name, "decimal", "{$precision},{$scale}", $field_default, $is_multi_language);

        $this->elements = $this->elements->push($std);

        return $this;
    }
}