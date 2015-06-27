<?php

  class Attribute
  {
    private $attribute;
    private $values = array();

    public function __construct($attribute, $values = array())
    {
      $this->attribute = $attribute;
      if (gettype($values) == 'array') {
        foreach ($values as $index => $value) {
          if (gettype($value) == 'string') {
            if (substr($value, 0, 1) == '\'' || substr($value, 0, 1) == '"') {
              $value = substr($value, 1);
              $values[$index] = substr($value, 0, strlen($value) - 1);
            }
          }
        }
      }
      $this->values = $values;
    }

    public function name()
    {
      return $this->attribute;
    }

    public function values()
    {
      return $this->values;
    }
  }
