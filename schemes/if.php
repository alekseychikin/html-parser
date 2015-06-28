<?php

  $parser->registerScheme(function ($element, $e)
  {
    if (get_class($element) == 'LogicNode') {
      $exprs = $element->get();
      if ($exprs[0]['value'] == 'if') {
        $e->nesting('if');
        return true;
      }
      else if ($exprs[0]['value'] == 'endif') {
        $e->closeNesting('if');
        return true;
      }
    }
    return false;
  });
