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
if (defined("SPAM_CLASS") ) return true;
define("SPAM_CLASS",true);

require(dirname(__FILE__)."/ngram.php");


class spam {
    var $_source;
    /**
     *  Constructor
     *
     *  Because the Spam Class do not know the source, you must
     *  define a function that return an array of token and values.
     *  
     *  @param string $Callback Function name
     */
    function spam($callback='') {
        if ( !is_callable($callback) ) {
            trigger_error("$callback is not a valid funciton",E_USER_ERROR);
        }
        $this->_source = $callback;
    }
    
    /**
     *    Returns the the posibility to text belogs to "spams" 
     *    
     *    @param $text Text to analize
     *    @access public 
     *    @return true
     */
    function isItSpam($text,$type) {
        $ngram = new ngram;
        $ngram->setText($text);
        
        for($i=3; $i <= 5;$i++) {
            $ngram->setLength($i);
            $ngram->extract();
        }
        
        $fnc = $this->_source;
        $ngrams =  $ngram->getnGrams();
        $knowledge =  $fnc( $ngrams,$type );
        $total=0;
        $acc=0;
        foreach($ngrams as $k => $v) {
            if ( isset($knowledge[$k]) ) {
                $acc += $knowledge[$k] * $v;
                $total++;
            }
        }
        $percent = ($acc/$total);
        $percent = $percent > 1.0 ? 1.0 : $percent;
        return $percent * 100;
    }
    
   
    
    function isItSpam_v2($text,$type) {
        $ngram = new ngram;
        $ngram->setText($text);
        
        for($i=3; $i <= 5;$i++) {
            $ngram->setLength($i);
            $ngram->extract();
        }
        
        $fnc = $this->_source;
        $ngrams =  $ngram->getnGrams();
        $knowledge =  $fnc( $ngrams,$type );
        $total=0;
        $acc=0;
        
        /**
         *  N = total number of n-grams used.
         *  K = product of all n-grams (values are extracted from knowledge base)
         *  
         *  H = chi2Q( -2N K, 2N);
         *  S = chi2Q( -2N ( (1.0 - ngram(1)) ( 1.0 - ngram(2)) .. ( 1.0 - ngram(N)) ), 2N)
         *  I = ( 1 + H - S ) / 2
         *
         */
        $N = 0;
        $H = $S = 1;
        
        foreach($ngrams as $k => $v) {
            if ( !isset($knowledge[$k]) ) continue;
            $N++;
            $value = $knowledge[$k] * $v; 
            $H *= $value;
            $S *= (float)( 1 - ( ($value>=1) ? 0.99 : $value) );
        }

        $H = $this->chi2Q( -2 * log( $N *  $H), 2 * $N);
        $S = (float)$this->chi2Q( -2 * log( $N *  $S), 2 * $N);
        $percent = (( 1 + $H - $S ) / 2) * 100;
        return is_finite($percent) ? $percent : 100;
    }
    
    function chi2Q( $x,  $v) {
        $m = (double)$x / 2.0;
        $s = exp(-$m);
        $t = $s;
        
        for($i=1; $i < ($v/2);$i++) {
            $t *= $m/$i;
            $s += $t;
        }
        return ( $s < 1.0) ? $s : 1.0;
    }
}
?>