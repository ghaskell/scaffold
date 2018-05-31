<?php
/**
 * Created by PhpStorm.
 * User: georg
 * Date: 5/26/2018
 * Time: 1:30 PM
 */

namespace Ghaskell\Scaffold;

use Illuminate\Support\Arr;
use Illuminate\Support\Str;
use Illuminate\View\Compilers\BladeCompiler;
use Ghaskell\Scaffold\Facades\Code;


class VibroCompiler extends BladeCompiler
{

    /**
     * Array of opening and closing tags for raw echos.
     *
     * @var array
     */
    protected $rawTags = ['\*\|', '\|\*']; //pipes are escaped for regex, actual tags are #|, |#


    /**
     * Array of opening and closing tags for regular echos.
     *
     * @var array
     */
    protected $contentTags = ['<%', '%>'];

    /**
     * Array of opening and closing tags for escaped echos.
     *
     * @var array
     */
    protected $escapedTags = ['<%%%', '%%%>'];

    /**
     * The "regular" / legacy echo string format.
     *
     * @var string
     */
    protected $echoFormat = 'e(%s)';

    /**
     * Array of footer lines to be added to template.
     *
     * @var array
     */
    protected $footer = [];

    /**
     * Array to temporary store the raw blocks found in the template.
     *
     * @var array
     */
    protected $rawBlocks = [];



    public function compileFileName($fileName, $data) {
        extract($data);
        $parsed = $this->compileString($fileName);
        ob_start();
        eval( '?>' . $parsed );
        $output = ob_get_contents();
        ob_end_clean();
        return $output;
    }

    public function compileFile($filePath, array $data)
    {
        $result = Code::file($filePath, $data)->with($data)->render();
        return str_replace("<?stub", "<?php", $result);

    }

    public function compileString($value)
    {
        if (strpos($value, '@verbatim') !== false) {
            $value = $this->storeVerbatimBlocks($value);
        }

        $this->footer = [];

        if (strpos($value, '@php') !== false) {
            $value = $this->storePhpBlocks($value);
        }

        $result = '';

        // Here we will loop through all of the tokens returned by the Zend lexer and
        // parse each one into the corresponding valid PHP. We will then have this
        // template as the correctly rendered PHP that can be rendered natively.
        foreach (token_get_all($value) as $token) {
            $result .= is_array($token) ? $this->parseToken($token) : $token;
        }

        if (! empty($this->rawBlocks)) {
            $result = $this->restoreRawContent($result);
        }

        // If there are any footer lines that need to get added to a template we will
        // add them here at the end of the template. This gets used mainly for the
        // template inheritance via the extends keyword that should be appended.
        if (count($this->footer) > 0) {
            $result = $this->addFooters($result);
        }

        return $result;
    }

    /**
     * Compile Blade statements that start with "@%".
     *
     * @param  string  $value
     * @return string
     */
    protected function compileStatements($value)
    {
        return preg_replace_callback(
            '/\B@%(@?\w+(?:::\w+)?)([ \t]*)(\( ( (?>[^()]+) | (?3) )* \))?/x', function ($match) {
            return $this->compileStatement($match);
        }, $value
        );
    }

    /**
     * Compile a single Blade @% statement.
     *
     * @param  array  $match
     * @return string
     */
    protected function compileStatement($match)
    {
        if (Str::contains($match[1], '@%')) {
            $match[0] = isset($match[3]) ? $match[1].$match[3] : $match[1];
        }  elseif (method_exists($this, $method = 'compile'.ucfirst($match[1]))) {
            $match[0] = $this->$method(Arr::get($match, 3));
        }

        return isset($match[3]) ? $match[0] : $match[0].$match[2];
    }

}