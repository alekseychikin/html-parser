<?php

  include_once "lime/parse_engine.php";
  include_once "tokenizer.php";
  include_once "parser.class";
  include_once "tokens/tag.php";
  include_once "tokens/attribute.php";
  include_once "tokens/text_node.php";
  include_once "tokens/logic_node.php";
  $lexers = file_get_contents("lexers.lex");
  $lines = file_get_contents("tests/logic_node_at_attribute.html");

  $tokens = new Tokenizer($lexers);

  function makeParse($lines)
  {
    global $parser, $tokens;
    if (!strlen($lines)) return;
    try {
      $parser->reset();
      $lines = explode("\n", $lines);
      foreach ($lines as $line) {
        if (!strlen($line)) continue;
        while ($token = $tokens->nextToken($line)) {
          // print_r($token);
          $parser->eat($token['token'], $token['value']);
        }
      }
      $parser->eat_eof();
    } catch (parse_error $e) {
      echo $e->getMessage(), "\n";
    }
  }

  $result = array();
  $file = fopen("examples/logic_node_at_attribute.html", "w");

  function prepareAttribute($attribute)
  {
    if (get_class($attribute) == 'Attribute') {
      $value = $attribute->values();
      if (gettype($value) == 'object' && get_class($value) == 'LogicNode') {
        $value = '';
/*
        $values = $value->get();
        $value = '';
        foreach ($values as $val) {
          $value .= $val['value'];
        }
//*/
      }
      return ' ' . $attribute->name() . '="' . $value . '"';
    }
    elseif (get_class($attribute) == 'LogicNode') {

    }
    return '';
  }

  function prepareTag($element)
  {
    global $file;
    print_r($element);
    $stringAttribute = '<' . ($element->type() == 'close' ? '/' : '') . $element->name();
    $attributes = $element->attributes();
    foreach ($attributes as $attribute) {
      $stringAttribute .= prepareAttribute($attribute);
    }
    $stringAttribute .= '>';
    fputs($file, $stringAttribute);
  }

  function showResults($lines)
  {
    foreach ($lines as $line) {
      foreach ($line as $element) {
        switch (get_class($element))
        {
          case 'Tag':
            prepareTag($element);
            break;
          case 'TextNode':
            // print_r($element);
            break;
          case 'LogicNode':

            break;
        }
      }
    }
  }


  $parser = new parse_engine(new template_parser());
  makeParse($lines);

  fclose($file);

?>
