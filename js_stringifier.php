<?php

  class JsStringifier
  {
    private $tabChar = '  ';
    private $resultString = '';
    private $currentElements = array();
    private $elementIndex = 0;

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
      foreach ($exprs as $index => $expr) {
        $values[] = $expr['value'];
        if (!$index) $values[] = '(';
      }
      $values[] = ') {';
    }

    private function prepareEndif($exprs, & $values)
    {
      $values[] = '}';
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
        $value = array("\n" . 'var attrs = \'\';');
        $lastName = ($this->currentElements[count($this->currentElements) - 1]);
        if (gettype($values) == 'array') {
          foreach ($values as $val) {
            if (gettype($val) == 'object' && get_class($val) == 'LogicNode') {
              $exprs = array();
              $this->expandLogicExpressions($val, $exprs);
              $value[] = implode(' ', $exprs);
            }
            else {
              $value[] = 'attrs += "' . $val . '";';
            }
          }
        }
        return implode ("\n", $value) . "\n" . $lastName . '.setAttribute("' . $attribute->name() . '", attrs);' . "\n";
      }
      elseif (get_class($attribute) == 'LogicNode') {
        $value = array();
        $this->expandLogicExpressions($attribute, $value);
        return ' '. implode(' ', $value) . ' ';
      }
      return '';
    }

    private function prepareTag($element, $zindex, $nesting = false)
    {
      if ($element->type() == 'open') {
        $elementName = 'element' . $this->elementIndex++;
        $this->currentElements[] = $elementName;
        $this->resultString .=
          "\n" . $this->tabify($zindex) .
          '  var ' . $elementName . ' = document.createElement(\'' . $element->name() .'\');'.
          "\n" . $this->tabify($zindex) .
          '  parent.appendChild(' . $elementName . ');';
          $attributes = $element->attributes();
          foreach ($attributes as $attribute) {
            $this->resultString .= $this->prepareAttribute($attribute);
          }
          if ($nesting) {
            $this->resultString .=
              "\n" . '(function (parent)' . "\n" .
              $this->tabify($zindex) .
              '  {' . "\n" . $this->tabify($zindex);
          }
      }
      if ($element->type() == 'close') {
        $elementName = array_pop($this->currentElements);
        $this->resultString .= "\n" . $this->tabify($zindex) .
        '})(' . $elementName . ');';
      }
    }

    private function prepareTextNode($element, $zindex)
    {
      $elementName = ($this->currentElements[count($this->currentElements) - 1]);
      $this->resultString .= "\n" . 'parent.appendChild(document.createTextNode(\'' . $element->text() . '\'));';
    }

    private function prepareLogicNode($sourceElement, $zindex)
    {
      $value = array();
      $this->expandLogicExpressions($sourceElement, $value);
      $this->resultString .= "\n" . implode(' ', $value);
    }

    private function recString($tree, $zindex = 0)
    {
      $childs = $tree->getChilds();
      foreach ($childs as $element) {
        $sourceElement = $element->element();
        switch (get_class($sourceElement)) {
          case 'Tag':
            $this->prepareTag($sourceElement, $zindex, $element->nesting());
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
      $this->resultString .= '(function (parent) {';
      $this->recString($tree);
      $this->resultString .= "\n" . '})(document.body);';
      return $this->resultString;
    }
  }
