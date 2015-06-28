<?php

  class PhpStringifier
  {
    private $tabChar = '  ';
    private $resultString = '';

    private function tabify($zindex)
    {
      $tabs = '';
      for ($i = 0; $i < $zindex; $i++) {
        $tabs .= $this->tabChar;
      }
      return $tabs;
    }

    private function prepareIf($exprs, & $values)
    {
      $values[] = '<?php';
      foreach ($exprs as $index => $expr) {
        $values[] = $expr['value'];
        if (!$index) $values[] = '(';
      }
      $values[] = ') {';
      $values[] = '?>';
    }

    private function prepareEndif($exprs, & $values)
    {
      $values[] = '<?php';
      $values[] = '}';
      $values[] = '?>';
    }

    private function expandLogicExpressions($element, & $value)
    {
      $values = $element->get();
      if ($values[0]['value'] == 'if') {
        $this->prepareIf($values, $value);
      }
      if ($values[0]['value'] == 'endif') {
        $this->prepareEndIf($values, $value);
      }
    }

    private function prepareAttribute($attribute)
    {
      if (get_class($attribute) == 'Attribute') {
        $values = $attribute->values();
        $value = array();
        if (gettype($values) == 'array') {
          foreach ($values as $val) {
            if (gettype($val) == 'object' && get_class($val) == 'LogicNode') {
              $this->expandLogicExpressions($val, $value);
            }
            else {
              $value[] = $val;
            }
          }
        }
        return ' ' . $attribute->name() . '="' . implode(' ', $value) . '"';
      }
      elseif (get_class($attribute) == 'LogicNode') {
        $value = array();
        $this->expandLogicExpressions($attribute, $value);
        return ' '. implode(' ', $value) . ' ';
      }
      return '';
    }

    private function prepareTag($element, $zindex)
    {
      $this->resultString .= "\n" . $this->tabify($zindex) . '<' . ($element->type() == 'close' ? '/' : '') . $element->name();
      $attributes = $element->attributes();
      foreach ($attributes as $attribute) {
        $this->resultString .= $this->prepareAttribute($attribute);
      }
      $this->resultString .= '>';
    }

    private function prepareTextNode($element, $zindex)
    {
      $this->resultString .= $element->text();
    }

    private function prepareLogicNode($sourceElement, $zindex)
    {
      $value = array();
      $this->expandLogicExpressions($sourceElement, $value);
      $this->resultString .= ' ' . implode(' ', $value) . ' ';
    }

    private function recString($tree, $zindex = 0)
    {
      $childs = $tree->getChilds();
      foreach ($childs as $element) {
        $sourceElement = $element->element();
        switch (get_class($sourceElement)) {
          case 'Tag':
            $this->prepareTag($sourceElement, $zindex);
            break;
          case 'TextNode':
            $this->prepareTextNode($sourceElement, $zindex);
            break;
          case 'LogicNode':
          $this->prepareLogicNode($sourceElement, $zindex);
            break;
        }
        $this->recString($element, $zindex + 1);
      }
    }

    public function stringify($tree)
    {
      $this->recString($tree);
      return $this->resultString;
    }
  }
