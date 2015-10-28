<?php

  require_once './parser_e.php';

  $testFiles = array(
    'tag',
    'tag_attribute',
    'tag_attribute_empty',
    'textnode',
    'logic_node_var',
    'logic_node_var_with_index'
  );

  foreach ($testFiles as $filename) {
    $tree = ParserE::parseFile('./tests/' . $filename . '.html');
    // print_r($tree);
  }
  // $tree = ParserE::parse();
  // print_r($tree);


?>
