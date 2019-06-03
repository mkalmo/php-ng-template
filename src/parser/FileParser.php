<?php

require_once('HtmlLexer.php');
require_once('HtmlParser.php');
require_once('TreeBuilderActions.php');

class FileParser {

    private $filePath;
    private $input;

    public function __construct($filePath) {
        $this->filePath = $filePath;
        $this->input = join('', file($this->filePath));;
    }

    public function parse() {
        $tokens = [];
        try {
            $tokens = (new HtmlLexer($this->input))->tokenize();
        } catch (LexerException $e) {
            $this->throwLexerError($e);
        }

        $builder = new TreeBuilderActions();

        (new HtmlParser($tokens, $builder))->parse();

        return $builder->getResult();
    }

    private function throwLexerError($e) {
        $message = printf("%s \nat %s:%s\n",
            $e->message,
            realpath($this->filePath),
            $this->locationString($e));

        throw new Error($message);
    }

    private function locationString($e) {
        $textParsed = substr($this->input, 0, $e->pos);
        $lines = explode("\n", $textParsed);
        $lineNr = count($lines);
        $colNr = strlen($lines[$lineNr - 1]);

        return sprintf('%s:%s', $lineNr, $colNr);
    }


}

