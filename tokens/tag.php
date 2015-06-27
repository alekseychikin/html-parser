<?php

  class Tag
  {
    private $name;
    private $type;
    private $attributes = array();

    public function __construct($tagName, $type = 'open')
    {
      $this->name = $tagName;
      $this->type = $type;
    }

    public function setAttributes($attribs)
    {
      if (gettype($attribs) == 'array' && count($attribs)) {
        $this->attributes = $attribs;
      }
    }
  }
