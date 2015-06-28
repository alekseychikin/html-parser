<?php

  $parser->registerScheme(function ($element, $e)
  {
    $singleTags = array('hr', 'br', 'base', 'button', 'col', 'embed', 'img', 'area', 'source', 'track');
    if (get_class($element) == 'Tag') {
      $name = $element->name();
      if ($element->type() == 'open' && !in_array($name, $singleTags)) {
        $e->nesting($name);
        return true;
      }
    }
    return false;
  });
