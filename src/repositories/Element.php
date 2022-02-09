<?php

namespace hcolab\cms\repositories;


class Element extends Column
{

    public $elements;

    public function getElements()
    {
        return $this->elements;
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

    public function ForeignKey($label, $container, $required, $field_name)
    {

        $ui = new \StdClass;
        $ui->type = "select";
        $ui->label = $label;
        $ui->container = $container;
        $ui->required = $required;

        $std = new \StdClass;
        $std->name = $field_name;
        $std->ui = $ui;
        $std->db = set_db($field_name, "bigint", NULL, NULL, false);

        $this->elements = $this->elements->push($std);

        return $this;
    }

    public function MultipleSelect($label, $container, $required, $field_name)
    {

        $ui = new \StdClass;
        $ui->type = "multiple select";
        $ui->label = $label;
        $ui->container = $container;
        $ui->required = $required;

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

    public function BooleanCheckbox($label, $container, $required, $field_name)
    {

        $ui = new \StdClass;
        $ui->type = "boolean checkbox";
        $ui->label = $label;
        $ui->container = $container;
        $ui->required = $required;

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


    public function FileUploadField($label, $container, $required, $field_name)
    {

        $ui = new \StdClass;
        $ui->type = "file";
        $ui->label = $label;
        $ui->container = $container;
        $ui->required = $required;

        $std = new \StdClass;
        $std->name = $field_name;
        $std->ui = $ui;
        $std->db = null;


        $this->elements = $this->elements->push($std);

        return $this;
    }

    public function FileMultipleUploadField($label, $container, $required, $field_name)
    {

        $ui = new \StdClass;
        $ui->type = "multiple file";
        $ui->label = $label;
        $ui->container = $container;
        $ui->required = $required;

        $std = new \StdClass;
        $std->name = $field_name;
        $std->ui = $ui;
        $std->db = null;

        
        
        $this->elements = $this->elements->push($std);
        
        return $this;
    }
}