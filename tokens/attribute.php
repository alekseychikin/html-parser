<?php

  class Attribute
  {
    private $attribute;
    private $value = "";

    public function __construct($attribute, $value)
    {
      $this->attribute = $attribute;
      if (gettype($value) == 'string' && substr($value, 0, 1) == '\'' || substr($value, 0, 1) == '"') {
        $value = substr($value, 1);
        $value = substr($value, 0, strlen($value) - 1);
      }
      $this->value = $value;
    }
  }
