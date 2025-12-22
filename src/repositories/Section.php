<?php

namespace hcolab\cms\repositories;

class Section extends Element
{

    public $version = "1.0";
    public $section;
    public $title;
    public $identifier;
    public $elements = [];
    public $foreign_keys = [];
    public $component;
    public $locations = [];

}