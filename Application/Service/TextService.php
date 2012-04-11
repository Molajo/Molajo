<?php
/**
 * @package   Molajo
 * @copyright 2012 Amy Stephen. All rights reserved.
 * @license   GNU General Public License Version 2, or later http://www.gnu.org/licenses/gpl.html
 */
namespace Molajo\Application\Service;

defined('MOLAJO') or die;

/**
 * Text
 *
 * @package     Molajo
 * @subpackage  Service
 * @since       1.0
 */
Class TextService extends BaseService
{

    /**
     * Static instance
     *
     * @var    object
     * @since  1.0
     */
    protected static $instance;

    /**
     * getInstance
     *
     * @static
     * @return bool|object
     * @since  1.0
     */
    public static function getInstance()
    {
        if (empty(self::$instance)) {
            self::$instance = new TextService();
        }
        return self::$instance;
    }

    /**
     * __construct
     *
     * Class constructor.
     *
     * @since  1.0
     */
    public function __construct()
    {

    }

    /**
     * addLineBreaks
     *
     * changes line breaks to br tags
     *
     * @param  string $text
     * @return string
     * @since  1.0
     */
    public function addLineBreaks($text)
    {
        return nl2br($text);
    }

    /**
     * replaceBuffer
     *
     * Change a value in the buffer
     *
     * @param  string $text
     * @return string
     * @since  1.0
     */
    public function replaceBuffer($change_from, $change_to)
    {
        $buffer = preg_replace(
            $change_from,
            $change_to,
            Services::Response()->getBody()
        );
        Services::Response()->setContent($buffer);
    }

    /**
     * smilies
     *
     * change text smiley values into icons
     *
     * @param  string $text
     * @return string
     * @since  1.0
     */
    public function smilies($text)
    {
        $smile = array(
            ':mrgreen:' => 'icon_mrgreen.gif',
            ':neutral:' => 'icon_neutral.gif',
            ':twisted:' => 'icon_twisted.gif',
            ':arrow:' => 'icon_arrow.gif',
            ':shock:' => 'icon_eek.gif',
            ':smile:' => 'icon_smile.gif',
            ':???:' => 'icon_confused.gif',
            ':cool:' => 'icon_cool.gif',
            ':evil:' => 'icon_evil.gif',
            ':grin:' => 'icon_biggrin.gif',
            ':idea:' => 'icon_idea.gif',
            ':oops:' => 'icon_redface.gif',
            ':razz:' => 'icon_razz.gif',
            ':roll:' => 'icon_rolleyes.gif',
            ':wink:' => 'icon_wink.gif',
            ':cry:' => 'icon_cry.gif',
            ':eek:' => 'icon_surprised.gif',
            ':lol:' => 'icon_lol.gif',
            ':mad:' => 'icon_mad.gif',
            ':sad:' => 'icon_sad.gif',
            '8-)' => 'icon_cool.gif',
            '8-O' => 'icon_eek.gif',
            ':-(' => 'icon_sad.gif',
            ':-)' => 'icon_smile.gif',
            ':-?' => 'icon_confused.gif',
            ':-D' => 'icon_biggrin.gif',
            ':-P' => 'icon_razz.gif',
            ':-o' => 'icon_surprised.gif',
            ':-x' => 'icon_mad.gif',
            ':-|' => 'icon_neutral.gif',
            ';-)' => 'icon_wink.gif',
            '8)' => 'icon_cool.gif',
            '8O' => 'icon_eek.gif',
            ':(' => 'icon_sad.gif',
            ':)' => 'icon_smile.gif',
            ':?' => 'icon_confused.gif',
            ':D' => 'icon_biggrin.gif',
            ':P' => 'icon_razz.gif',
            ':o' => 'icon_surprised.gif',
            ':x' => 'icon_mad.gif',
            ':|' => 'icon_neutral.gif',
            ';)' => 'icon_wink.gif',
            ':!:' => 'icon_exclaim.gif',
            ':?:' => 'icon_question.gif',
        );

        if (count($smile) > 0) {
            foreach ($smile as $key => $val) {
                $text = str_ireplace($key,
                    '<span><img src="' .
                        SITES_MEDIA_URL .
                        '/images/smilies/'
                        . $val
                        . '" alt="smiley" class="smiley-class" /></span>',
                    $text);
            }
        }
        return $text;
    }

    /**
     * getPlaceHolderText
     *
     * @param   $count
     * @param   array $options
     * @return  string
     * @since   1.0
     */
    public function getPlaceHolderText($count, $options = array())
    {
        $options = array_merge(
            array(
                'html' => false,
                'lorem' => true
            ),
            $options
        );

        $generator = new LoremIpsumGenerator;

        $html_format = $options['html'] ? 'plain' : 'html';
        $start_with_lorem_ipsum = $options['lorem'];

        return ucfirst($generator->getContent($count, $html_format, $start_with_lorem_ipsum));
    }
}

/**
 * @package   Molajo
 * @subpackage  Platform
 * @copyright   Copyright (c) 2009, Mathew Tinsley (tinsley@tinsology.net) All rights reserved.
 */
defined('MOLAJO') or die;

class LoremIpsumGenerator
{
    /**
     *    Copyright (c) 2009, Mathew Tinsley (tinsley@tinsology.net)
     *    All rights reserved.
     *
     *    Redistribution and use in source and binary forms, with or without
     *    modification, are permitted provided that the following conditions are met:
     *        * Redistributions of source code must retain the above copyright
     *          notice, this list of conditions and the following disclaimer.
     *        * Redistributions in binary form must reproduce the above copyright
     *          notice, this list of conditions and the following disclaimer in the
     *          documentation and/or other materials provided with the distribution.
     *        * Neither the name of the organization nor the
     *          names of its contributors may be used to endorse or promote products
     *          derived from this software without specific prior written permission.
     *
     *    THIS SOFTWARE IS PROVIDED BY MATHEW TINSLEY ''AS IS'' AND ANY
     *    EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT LIMITED TO, THE IMPLIED
     *    WARRANTIES OF MERCHANTABILITY AND FITNESS FOR A PARTICULAR PURPOSE ARE
     *    DISCLAIMED. IN NO EVENT SHALL <copyright holder> BE LIABLE FOR ANY
     *    DIRECT, INDIRECT, INCIDENTAL, SPECIAL, EXEMPLARY, OR CONSEQUENTIAL DAMAGES
     *    (INCLUDING, BUT NOT LIMITED TO, PROCUREMENT OF SUBSTITUTE GOODS OR SERVICES;
     *    LOSS OF USE, DATA, OR PROFITS; OR BUSINESS INTERRUPTION) HOWEVER CAUSED AND
     *    ON ANY THEORY OF LIABILITY, WHETHER IN CONTRACT, STRICT LIABILITY, OR TORT
     *    (INCLUDING NEGLIGENCE OR OTHERWISE) ARISING IN ANY WAY OUT OF THE USE OF THIS
     *    SOFTWARE, EVEN IF ADVISED OF THE POSSIBILITY OF SUCH DAMAGE.
     */

    private $words, $wordsPerParagraph, $wordsPerSentence;

    function __construct($wordsPer = 100)
    {
        $this->wordsPerParagraph = $wordsPer;
        $this->wordsPerSentence = 24.460;
        $this->words = array(
            'lorem',
            'ipsum',
            'dolor',
            'sit',
            'amet',
            'consectetur',
            'adipiscing',
            'elit',
            'curabitur',
            'vel',
            'hendrerit',
            'libero',
            'eleifend',
            'blandit',
            'nunc',
            'ornare',
            'odio',
            'ut',
            'orci',
            'gravida',
            'imperdiet',
            'nullam',
            'purus',
            'lacinia',
            'a',
            'pretium',
            'quis',
            'congue',
            'praesent',
            'sagittis',
            'laoreet',
            'auctor',
            'mauris',
            'non',
            'velit',
            'eros',
            'dictum',
            'proin',
            'accumsan',
            'sapien',
            'nec',
            'massa',
            'volutpat',
            'venenatis',
            'sed',
            'eu',
            'molestie',
            'lacus',
            'quisque',
            'porttitor',
            'ligula',
            'dui',
            'mollis',
            'tempus',
            'at',
            'magna',
            'vestibulum',
            'turpis',
            'ac',
            'diam',
            'tincidunt',
            'id',
            'condimentum',
            'enim',
            'sodales',
            'in',
            'hac',
            'habitasse',
            'platea',
            'dictumst',
            'aenean',
            'neque',
            'fusce',
            'augue',
            'leo',
            'eget',
            'semper',
            'mattis',
            'tortor',
            'scelerisque',
            'nulla',
            'interdum',
            'tellus',
            'malesuada',
            'rhoncus',
            'porta',
            'sem',
            'aliquet',
            'et',
            'nam',
            'suspendisse',
            'potenti',
            'vivamus',
            'luctus',
            'fringilla',
            'erat',
            'donec',
            'justo',
            'vehicula',
            'ultricies',
            'varius',
            'ante',
            'primis',
            'faucibus',
            'ultrices',
            'posuere',
            'cubilia',
            'curae',
            'etiam',
            'cursus',
            'aliquam',
            'quam',
            'dapibus',
            'nisl',
            'feugiat',
            'egestas',
            'class',
            'aptent',
            'taciti',
            'sociosqu',
            'ad',
            'litora',
            'torquent',
            'per',
            'conubia',
            'nostra',
            'inceptos',
            'himenaeos',
            'phasellus',
            'nibh',
            'pulvinar',
            'vitae',
            'urna',
            'iaculis',
            'lobortis',
            'nisi',
            'viverra',
            'arcu',
            'morbi',
            'pellentesque',
            'metus',
            'commodo',
            'ut',
            'facilisis',
            'felis',
            'tristique',
            'ullamcorper',
            'placerat',
            'aenean',
            'convallis',
            'sollicitudin',
            'integer',
            'rutrum',
            'duis',
            'est',
            'etiam',
            'bibendum',
            'donec',
            'pharetra',
            'vulputate',
            'maecenas',
            'mi',
            'fermentum',
            'consequat',
            'suscipit',
            'aliquam',
            'habitant',
            'senectus',
            'netus',
            'fames',
            'quisque',
            'euismod',
            'curabitur',
            'lectus',
            'elementum',
            'tempor',
            'risus',
            'cras');
    }

    function getContent($count, $format = 'html', $loremipsum = true)
    {
        $format = strtolower($format);

        if ($count <= 0)
            return '';

        switch ($format) {
            case 'txt':
                return $this->getText($count, $loremipsum);
            case 'plain':
                return $this->getPlain($count, $loremipsum);
            default:
                return $this->getHTML($count, $loremipsum);
        }
    }

    /**
     * getWords
     *
     * @param $arr
     * @param $count
     * @param $loremipsum
     */
    private function getWords(&$arr, $count, $loremipsum)
    {
        $i = 0;
        if ($loremipsum) {
            $i = 2;
            $arr[0] = 'lorem';
            $arr[1] = 'ipsum';
        }

        for ($i; $i < $count; $i++) {
            $index = array_rand($this->words);
            $word = $this->words[$index];

            if ($i > 0 && $arr[$i - 1] == $word)
                $i--;
            else
                $arr[$i] = $word;
        }
    }

    /**
     * getPlain
     *
     * @param $count
     * @param $loremipsum
     * @param bool $returnStr
     *
     * @return array|string
     */
    private function getPlain($count, $loremipsum, $returnStr = true)
    {
        $words = array();
        $this->getWords($words, $count, $loremipsum);

        $delta = $count;
        $curr = 0;
        $sentences = array();
        while ($delta > 0) {
            $senSize = $this->gaussianSentence();
            if (($delta - $senSize) < 4)
                $senSize = $delta;

            $delta -= $senSize;

            $sentence = array();
            for ($i = $curr; $i < ($curr + $senSize); $i++)
                $sentence[] = $words[$i];

            $this->punctuate($sentence);
            $curr = $curr + $senSize;
            $sentences[] = $sentence;
        }

        if ($returnStr) {
            $output = '';
            foreach ($sentences as $s)
                foreach ($s as $w)
                    $output .= $w . ' ';

            return $output;
        }
        else
            return $sentences;
    }

    /**
     * getText
     *
     * @param $count
     * @param $loremipsum
     *
     * @return string
     */
    private function getText($count, $loremipsum)
    {
        $sentences = $this->getPlain($count, $loremipsum, false);
        $paragraphs = $this->getParagraphArr($sentences);

        $paragraphStr = array();
        foreach ($paragraphs as $p) {
            $paragraphStr[] = $this->paragraphToString($p);
        }

        $paragraphStr[0] = "\t" . $paragraphStr[0];
        return implode("\n\n\t", $paragraphStr);
    }

    /**
     * getParagraphArr
     *
     * @param $sentences
     *
     * @return array
     */
    private function getParagraphArr($sentences)
    {
        $wordsPer = $this->wordsPerParagraph;
        $sentenceAvg = $this->wordsPerSentence;
        $total = count($sentences);

        $paragraphs = array();
        $pCount = 0;
        $currCount = 0;
        $curr = array();

        for ($i = 0; $i < $total; $i++) {
            $s = $sentences[$i];
            $currCount += count($s);
            $curr[] = $s;
            if ($currCount >= ($wordsPer - round($sentenceAvg / 2.00)) || $i == $total - 1) {
                $currCount = 0;
                $paragraphs[] = $curr;
                $curr = array();
            }
        }

        return $paragraphs;
    }

    /**
     * getHTML
     *
     * @param $count
     * @param $loremipsum
     *
     * @return string
     */
    private function getHTML($count, $loremipsum)
    {
        $sentences = $this->getPlain($count, $loremipsum, false);
        $paragraphs = $this->getParagraphArr($sentences);

        $paragraphStr = array();
        foreach ($paragraphs as $p) {
            $paragraphStr[] = "<p>\n" . $this->paragraphToString($p, true) . '</p>';
        }

        //add new lines for the sake of clean code
        return implode("\n", $paragraphStr);
    }

    /**
     * paragraphToString
     *
     * @param $paragraph
     * @param bool $htmlCleanCode
     *
     * @return string
     */
    private function paragraphToString($paragraph, $htmlCleanCode = false)
    {
        $paragraphStr = '';
        foreach ($paragraph as $sentence) {
            foreach ($sentence as $word)
                $paragraphStr .= $word . ' ';

            if ($htmlCleanCode)
                $paragraphStr .= "\n";
        }
        return $paragraphStr;
    }

    /**
     * punctuate
     *
     * Inserts commas and periods in word array.
     *
     * @param $sentence
     * @return array
     */
    private function punctuate(& $sentence)
    {
        $count = count($sentence);
        $sentence[$count - 1] = $sentence[$count - 1] . '.';

        if ($count < 4)
            return $sentence;

        $commas = $this->numberOfCommas($count);

        for ($i = 1; $i <= $commas; $i++) {
            $index = (int)round($i * $count / ($commas + 1));

            if ($index < ($count - 1) && $index > 0) {
                $sentence[$index] = $sentence[$index] . ',';
            }
        }
    }

    /**
     * numberOfCommas
     *
     * Determines the number of commas for a
     * sentence of the given length. Average and
     * standard deviation are determined superficially
     *
     * @param $len
     * @return int
     */
    private function numberOfCommas($len)
    {
        $avg = (float)log($len, 6);
        $stdDev = (float)$avg / 6.000;

        return (int)round($this->gauss_ms($avg, $stdDev));
    }

    /**
     * gaussianSentence
     *
     * Returns a number on a gaussian distribution
     * based on the average word length of an english
     * sentence.
     * Statistics Source:
     *    http://hearle.nahoo.net/Academic/Maths/Sentence.html
     *    Average: 24.46
     *    Standard Deviation: 5.08
     */
    private function gaussianSentence()
    {
        $avg = (float)24.460;
        $stdDev = (float)5.080;

        return (int)round($this->gauss_ms($avg, $stdDev));
    }

    /**
     * gauss
     *
     * The following three functions are used to
     * compute numbers with a guassian cmsbution
     * Source:
     *     http://us.php.net/manual/en/function.rand.php#53784
     */
    private function gauss()
    { // N(0,1)
        // returns random number with normal distribution:
        //   mean=0
        //   std dev=1

        // auxilary vars
        $x = $this->random_0_1();
        $y = $this->random_0_1();

        // two independent variables with normal distribution N(0,1)
        $u = sqrt(-2 * log($x)) * cos(2 * pi() * $y);
        $v = sqrt(-2 * log($x)) * sin(2 * pi() * $y);

        // i will return only one, couse only one needed
        return $u;
    }

    private function gauss_ms($m = 0.0, $s = 1.0)
    {
        return $this->gauss() * $s + $m;
    }

    private function random_0_1()
    {
        return (float)rand() / (float)getrandmax();
    }

}
