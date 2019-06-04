<?php

require_once('HtmlLexer.php');
require_once('HtmlParser.php');
require_once('TreeBuilderActions.php');

class FileParser {

    private $filePath;
    private $input;

    public function __construct($filePath) {
        $this->filePath = $filePath;
        $this->input = file_get_contents($this->filePath);
    }

    public function parse() {
        try {
            $tokens = (new HtmlLexer($this->input))->tokenize();

            $builder = new TreeBuilderActions();

            (new HtmlParser($tokens, $builder))->parse();

        } catch (ParseException $e) {
            throw $this->error($e);
        }

        return $builder->getResult();
    }

    private function error($e) {
        $message = printf("%s \nat %s:%s\n",
            $e->message,
            realpath($this->filePath),
            $this->locationString($e->pos));

        return new Error($message);
    }

    private function locationString($pos) {
        $textParsed = substr($this->input, 0, $pos);
        $lines = explode("\n", $textParsed);
        $lineNr = count($lines);
        $colNr = strlen($lines[$lineNr - 1]) + 1; // +1: starts from 1

        return sprintf('%s:%s', $lineNr, $colNr);
    }


}

