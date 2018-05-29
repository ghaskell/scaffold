<?php
/**
 * Created by PhpStorm.
 * User: georg
 * Date: 5/26/2018
 * Time: 1:30 PM
 */

namespace Ghaskell\Scaffold;

use Illuminate\View\Compilers\BladeCompiler;


class VibroCompiler extends BladeCompiler
{

    /**
     * Array of opening and closing tags for raw echos.
     *
     * @var array
     */
    protected $rawTags = ['<%', '%>'];


    /**
     * Array of opening and closing tags for regular echos.
     *
     * @var array
     */
//    protected $contentTags = ['<%', '%>'];

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



    public function compileFileName($fileName, $model) {
        $parsed = $this->compileString($fileName);
        ob_start();
        eval( '?>' . $parsed );
        $output = ob_get_contents();
        ob_end_clean();
        return $output;
    }

    public function compileFile($filePath, $model)
    {
        return Code::file($filePath)->with(['model'=>$model])->render();
    }
}