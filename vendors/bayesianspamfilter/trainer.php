<?php
/*
***************************************************************************
*   Copyright (C) 2007 by Cesar D. Rodas                                  *
*   cesar@sixdegrees.com.br                                               *
*                                                                         *
*   Permission is hereby granted, free of charge, to any person obtaining *
*   a copy of this software and associated documentation files (the       *
*   "Software"), to deal in the Software without restriction, including   *
*   without limitation the rights to use, copy, modify, merge, publish,   *
*   distribute, sublicense, and/or sell copies of the Software, and to    *
*   permit persons to whom the Software is furnished to do so, subject to *
*   the following conditions:                                             *
*                                                                         *
*   The above copyright notice and this permission notice shall be        *
*   included in all copies or substantial portions of the Software.       *
*                                                                         *
*   THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND,       *
*   EXPRESS OR IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF    *
*   MERCHANTABILITY, FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT.*
*   IN NO EVENT SHALL THE AUTHORS BE LIABLE FOR ANY CLAIM, DAMAGES OR     *
*   OTHER LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, *
*   ARISING FROM, OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR *
*   OTHER DEALINGS IN THE SOFTWARE.                                       *
***************************************************************************
*/ 
if (defined("TRAINER_CLASS") ) return true;
define("TRAINER_CLASS",true);
require(dirname(__FILE__)."/ngram.php");

/**
 *    This class "learn" about what is spam and what is not
 *
 *    
 */
class trainer {
    var $examples;
    var $ngram;
    var $knowledge;
	// Adding the $previous var
	var $previous;
    
    function trainer() {
        $this->ngram = new ngram;
    }
    
    function add_example($text, $clasification) {
        $this->examples[$clasification][] = $text;
    }
    
    function setPreviousLearn($f) {
        $this->previous = $f;
    }
    
    function extractPatterns() {
        $previous = & $this->previous;
        $examples = & $this->examples;
        $ngram = & $this->ngram;
        $knowledge = & $this->knowledge;
        
        foreach($examples as $tipo => $texts) {
            $params[$tipo] = 0;
            $ngram->setInitialNgram( isset($previous[$tipo]) ? $previous[$tipo] : array() );
            foreach ($texts as $text) {
                $ngram->setText($text);
                for($i=3; $i <= 5;$i++) {
                    $ngram->setLength($i);
                    $ngram->extract();
                }
            }
 
            $actual = & $knowledge[$tipo];
            foreach( $ngram->getnGrams() as $k => $v) {
                $actual[$k]['cant'] = $v;
                $params[$tipo] += $v;
            }
        }
        $this->computeBayesianFiltering($params);
    }
    
    function computeBayesianFiltering($param) {
        $knowledge = & $this->knowledge;
        //print_r($param);
        //
        foreach($knowledge as $tipo => $caracterist) {
            foreach($caracterist as $k => $v) {
                 $t = ($v['cant']/$param[$tipo]);
                 $f = 0;
                 foreach($param as $k1 => $v1) 
                     if ( $k1 != $tipo) {
                        
                        $f += isset($knowledge[$k1][$k]['cant']) ? $knowledge[$k1][$k]['cant'] / $v1 : 0; 
                    }
                 $knowledge[$tipo][$k]['bayesian'] = $t / ($t + $f);
            }
        }
    }
}
?>