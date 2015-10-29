<?php

  require_once './parser_e.php';

  $testFiles = array(
    // 'tag',
    // 'tag_attribute',
    // 'tag_attribute_empty',
    // 'textnode',
    // 'logic_node_assigment_var',
    // 'logic_node_assigment_var_with_index',
    // 'logic_node_assigment_expressions',
    'tag_attribute_logic'
  );

  foreach ($testFiles as $filename) {
    $tree = ParserE::parseFile('./tests/' . $filename . '.html');
    print_r($tree);
  }
  // $tree = ParserE::parse();
  // print_r($tree);


?>
