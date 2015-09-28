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
    private $tokens;
    private $parser;
    private $schemes = array();

    function __construct($lexers)
    {
      $this->tokens = new Tokenizer($lexers);
      $this->parser = new parse_engine(new template_parser());
    }

    public function parse($lines)
    {
      if (!strlen($lines)) return;
      try {
        $this->parser->reset();
        $lines = explode("\n", $lines);
        foreach ($lines as $line) {
          if (!strlen($line)) continue;
          while ($token = $this->tokens->nextToken($line)) {
            // print_r($token);
            $this->parser->eat($token['token'], $token['value']);
          }
        }
        $this->parser->eat_eof();
      }
      catch (parse_error $e) {
        echo $e->getMessage(), "\n";
      }
    }

    public function results($lines)
    {
      $root = new ParserElement();
      foreach ($lines as $line) {
        // print_r($line);
        $curentElement = $root;
        $nestings = array('root');
        foreach ($line as $element) {
          print_r($element);
          foreach ($this->schemes as $scheme) {
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

    public function registerScheme($checkElement)
    {
      $this->schemes[] = array(
        'check' => $checkElement
      );
    }
  }

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
