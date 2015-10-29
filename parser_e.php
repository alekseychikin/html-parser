<?php

  include_once "lime/parse_engine.php";
  include_once "tokenizer.php";
  include_once "parser.class";
  include_once "tokens/tag.php";
  include_once "tokens/attribute.php";
  include_once "tokens/text_node.php";
  include_once "tokens/logic_node.php";

  class ParserE
  {
    private static $tokens;
    private static $parser;
    private static $schemes = array();
    private static $tree = null;

    public static function init()
    {
      print_r($lexers);
      self::$tokens = new Tokenizer(file_get_contents("lexers.lex"));
      self::$parser = new parse_engine(new template_parser());
    }

    public static function parseFile($filename)
    {
      return self::parse(file_get_contents($filename), $filename);
    }

    private static function generateDashes($char, $number)
    {
      $str = '';
      for ($i = 0; $i < $number; $i++) {
        $str .= $char;
      }
      return $str;
    }

    public static function parse($lines, $filename = null)
    {
      self::$tree = null;
      if (!strlen($lines)) return;
      // try {
        self::$parser->reset();
        $lines = explode("\n", $lines);
        $lastFoundedToken = '';
        $lastFoundedTokenValue = '';
        foreach ($lines as $lineNumber => $line) {
          $originalLine = $line;
          $lineNumber++;
          $columnNumber = 0;
          if (!strlen($line)) continue;
          while ($token = self::$tokens->nextToken($line)) {
            if ($lineNumber == 13) {
              // print_r($token);
            }
            try {
              self::$parser->eat($token['token'], $token['value']);
            }
            catch (Exception $e) {
              if ($filename) {
                echo $e->getMessage(). " at " . $filename . ":" . $lineNumber . ":" . $columnNumber . "\n";
              }
              else {
                echo $e->getMessage(). " at line " . $lineNumber . ":" . $columnNumber . "\n";
              }
              echo "Last token was (" . $lastFoundedToken . ")(" . $lastFoundedTokenValue . ")\n";
              echo $originalLine."\n";
              echo self::generateDashes('-', $columnNumber) . "^\n";
              return false;
            }
            $lastFoundedToken = $token['token'];
            $lastFoundedTokenValue = $token['value'];
            $columnNumber += strlen($token['value']);
          }
        }
        self::$parser->eat_eof();
      // }
      // catch (parse_error $e) {
      //   echo $e->getMessage(), "\n";
      // }
      // print_r(self::$parser->semantic);
      // var_dump(self::$tree);
      return self::$tree;
      return self::results(self::$tree);
    }

    public static function saveResults($tree)
    {
      self::$tree = $tree;
    }

    private static function results($lines)
    {
      $root = new ParserElement();
      foreach ($lines as $line) {
        // print_r($line);
        $curentElement = $root;
        $nestings = array('root');
        foreach ($line as $element) {
          // print_r($element);
          foreach (self::$schemes as $scheme) {
            $event = new ParserEvent();
            $check = $scheme['check']($element, $event);
            if ($check) {
              // echo "event, ". $event->type() ."\n";
              // echo get_class($element)."\n";
              // if (get_class($element) == 'Tag') {
              //   echo $element->name()."\n";
              // }
              $elem = new ParserElement($element, $curentElement);
              $curentElement->appendChild($elem);
              $type = $event->type();
              // echo $event->keyword() . ", type: $type\n";
              if ($type == 'nesting') {
                // echo "make nesting\n";
                $nestings[] = $event->keyword();
                $elem->setNesting();
                $curentElement = $elem;
              }
              else if ($type == 'closenesting') {
                try {
                  // echo "close tag " . $event->keyword() . "\n";
                  $popKeyword = array_pop($nestings);
                  if ($popKeyword != $event->keyword()) {
                    throw new Exception('Parser error: element closing nasting `' . $event->keyword() . '` not in turn'."\n");
                  }
                  $curentElement = $elem->getParent();
                }
                catch (Exception $e) {
                  die($e->getMessage());
                }
              }
              else {
                // echo "single type\n";
              }
            }
          }
        }
      }
      return $root;
    }

    public static function registerScheme($checkElement)
    {
      self::$schemes[] = array(
        'check' => $checkElement
      );
    }
  }

  ParserE::init();

  class ParserEvent
  {
    private $type = 'simple';
    private $pairkeyword;

    public function nesting($pairkeyword)
    {
      $this->type = 'nesting';
      $this->pairkeyword = $pairkeyword;
    }

    public function type()
    {
      return $this->type;
    }

    public function keyword()
    {
      return $this->pairkeyword;
    }

    public function closeNesting($pairkeyword)
    {
      $this->type = 'closenesting';
      $this->pairkeyword = $pairkeyword;
    }
  }

  class ParserElement
  {
    private $childs = array();
    private $parent;
    private $element;
    private $nesting = false;

    public function __construct($element = null, $parent = null)
    {
      $this->element = $element;
      $this->parent = $parent;
    }

    public function appendChild($element)
    {
      $this->childs[] = $element;
      return $this;
    }

    public function element()
    {
      return $this->element;
    }

    public function getChilds()
    {
      return $this->childs;
    }

    public function getParent()
    {
      return $this->parent;
    }

    public function nesting()
    {
      return $this->nesting;
    }

    public function setNesting()
    {
      $this->nesting = true;
    }


  }
