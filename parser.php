<?php

  include_once "lime/parse_engine.php";
  include_once "tokenizer.php";
  include_once "parser.class";
  include_once "tokens/tag.php";
  include_once "tokens/attribute.php";
  include_once "tokens/text_node.php";
  include_once "tokens/logic_node.php";
  $lexers = file_get_contents("lexers.lex");
  $lines = file_get_contents("tests/if.html");

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
          print_r($token)."\n";
          $parser->eat($token['token'], $token['value']);
        }
      }
      $parser->eat_eof();
    } catch (parse_error $e) {
      echo $e->getMessage(), "\n";
    }
  }

  function showResults($res)
  {
    echo " -> ";
    print_r($res);
    echo "\n";
  }

  $parser = new parse_engine(new template_parser());
  makeParse($lines);


?>
