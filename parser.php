<?php

  include_once "parser_e.php";
  $lexers = file_get_contents("lexers.lex");
  // $lines = file_get_contents("tests/logic_node_at_attribute.html");
  // $lines = file_get_contents("tests/rating.html");
  // $lines = file_get_contents("tests/title.html");
  $lines = file_get_contents("tests/multiline.html");

  $parser = new ParserE($lexers);
  include_once "schemes/single_tag.php";
  include_once "schemes/pair_tag.php";
  include_once "schemes/pair_tag_close.php";
  include_once "schemes/if.php";
  include_once "schemes/text_node.php";

  include_once "php_stringifier.php";
  include_once "js_stringifier.php";

  $parser->parse($lines);

  function getResults($lines)
  {
    global $parser;
    $phpStringifier = new PhpStringifier();
    $jsStringifier = new JsStringifier();
    $tree = $parser->results($lines);
    // print_r($tree);

    // $file = fopen("examples/rating.html", "w");
    // fputs($file, $phpStringifier->stringify($tree));
    // fclose($file);

    // $file = fopen("examples/logic_node_at_attribute_js.html", "w");
    // fputs($file, $jsStringifier->stringify($tree));
    // fclose($file);
  }


?>
