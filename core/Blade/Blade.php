<?php

namespace Core\Blade;
use Core\FileSystem\FileSystem;
use Core\Support\Arr;
use Core\Support\Str;
use InvalidArgumentException;

if(!defined('BASE_PATH')) {
    exit('No direct script access allowed');
}
/**
 * Class Blade
 * @package Blade
 */

class Blade
{

    use Concerns\CompilesAuthorizations,
        Concerns\CompilesComments,
        Concerns\CompilesComponents,
        Concerns\CompilesConditionals,
        Concerns\CompilesEchos,
        Concerns\CompilesIncludes,
        Concerns\CompilesInjections,
        Concerns\CompilesJson,
        Concerns\CompilesLayouts,
        Concerns\CompilesLoops,
        Concerns\CompilesRawPhp,
        Concerns\CompilesStacks,
        Concerns\CompilesTranslations;




    /**
     * Array to temporary store the raw blocks found in the template.
     *
     * @var array
     */
    protected array $rawBlocks = [];


    /**
     * All of the available compiler functions.
     *
     * @var array
     */
    protected array $compilers = [
        'Comments',
        'Extensions',
        'Statements',
        'Echos',
    ];

    /**
     * Array of opening and closing tags for raw echos.
     *
     * @var array
     */
    protected array $rawTags = ['{!!', '!!}'];

    /**
     * Array of opening and closing tags for regular echos.
     *
     * @var array
     */
    protected array $contentTags = ['{{', '}}'];


    /**
     * Array of opening and closing tags for escaped echos.
     *
     * @var array
     */
    protected array $escapedTags = ['{{{', '}}}'];


    /**
     * The "regular" / legacy echo string format.
     *
     * @var string
     */
    protected string $echoFormat = 'e(%s)';

    /**
     * All of the registered extensions.
     *
     * @var array
     */
    protected array $extensions = [];


    /**
     * Array of footer lines to be added to template.
     *
     * @var array
     */
    protected array $footer = [];


    private string $path=  BASE_PATH.'/views';

    private string $cache_path= BASE_PATH.'/cache/views';


    protected $echo_format = 'e(%s)';

    /**
     * @var array
     */
    private array $parameters = [];


    /**
     * @throws \Exception
     */
    public function make(string $view, array $context = []): bool|string
    {

       $__env=new ManageBlade();
       extract($context);
       $compiled_path= $this->CompileBladeFile( $view);
       ob_start();
       include ($compiled_path);
        return ob_get_clean();


    }

    /**
     * @throws \Exception
     */
    public function CompileBladeFile(string $view): string
    {
        $file_path=$this->findViewInPath($view);

        $compiled_path=$this->cache_path.'/'.sha1($file_path).'.php';

        /* اگر فایل وجود داشت و ویرایش ننشده بود دوباره کامپایل نمی شود */
        if ( Filesystem::exists($compiled_path) &&  (Filesystem::lastModified($file_path) == Filesystem::lastModified($compiled_path))){
            return $compiled_path;
        }


        $content=Filesystem::get($file_path);

        if (str_contains($content, '@php')) {
            $content = $this->storePhpBlocks($content);
        }

        $result = '';

        // Here we will loop through all of the tokens returned by the Zend lexer and
        // parse each one into the corresponding valid PHP. We will then have this
        // template as the correctly rendered PHP that can be rendered natively.
        foreach (token_get_all($content) as $token) {
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




        Filesystem::put($compiled_path, $result);
        Filesystem::touch($compiled_path,Filesystem::lastModified($file_path));



        return $compiled_path;
//        dd('cache file '.Filesystem::lastModified($compiled_path),false);
//        dd('org file '.Filesystem::lastModified($file_path),false);
//        dd(' time '.time());

    }


    /**
     * Find the given view in the list of paths.
     *
     * @param string $name
     * @param  array   $paths
     * @return string
     *
     * @throws \InvalidArgumentException
     */
    protected function findViewInPath(string $name): string
    {

            foreach ($this->getPossibleViewFiles($name) as $file) {
                if (file_exists($viewPath = $this->path.'/'.$file)) {
                    return $viewPath;
                }
            }


        throw new InvalidArgumentException("View [$name] not found.");
    }

    /**
     * Get an array of possible view files.
     *
     * @param string $name
     * @return array
     */
    protected function getPossibleViewFiles(string $name): array
    {
        return array_map(function ($extension) use ($name) {
            return str_replace('.', '/', $name).'.'.$extension;
        }, ['blade.php', 'php', 'css']);
    }

    /**
     * @param string $key
     * @return mixed
     */
    public function get(string $key): mixed
    {
        return $this->parameters[$key] ?? null;
    }


    /**
     * Store the PHP blocks and replace them with a temporary placeholder.
     *
     * @param string $value
     * @return string
     */
    protected function storePhpBlocks(string $value): string
    {
        return preg_replace_callback('/(?<!@)@php(.*?)@endphp/s', function ($matches) {
            return $this->storeRawBlock("<?php{$matches[1]}?>");
        }, $value);
    }

    /**
     * Store a raw block and return a unique raw placeholder.
     *
     * @param string $value
     * @return string
     */
    protected function storeRawBlock(string $value): string
    {

        return $this->getRawPlaceholder(
            array_push($this->rawBlocks, $value) - 1
        );
    }

    /**
     * Get a placeholder to temporary mark the position of raw blocks.
     *
     * @param int|string $replace
     * @return string
     */
    protected function getRawPlaceholder(int|string $replace): string
    {
        return str_replace('#', $replace, '@__raw_block_#__@');
    }


    /**
     * Parse the tokens from the template.
     *
     * @param array $token
     * @return string
     */
    protected function parseToken(array $token): string
    {
        list($id, $content) = $token;

        if ($id == T_INLINE_HTML) {
            foreach ($this->compilers as $type) {
                $content = $this->{"compile{$type}"}($content);
            }
        }

        return $content;
    }




    /**
     * Execute the user defined extensions.
     *
     * @param  string  $value
     * @return string
     */
    protected function compileExtensions($value)
    {
        foreach ($this->extensions as $compiler) {
            $value = call_user_func($compiler, $value, $this);
        }

        return $value;
    }


    /**
     * Compile Blade statements that start with "@".
     *
     * @param  string  $value
     * @return string
     */
    protected function compileStatements($value)
    {

        return preg_replace_callback(
            '/\B@(@?\w+(?:::\w+)?)([ \t]*)(\( ( (?>[^()]+) | (?3) )* \))?/x', function ($match) {
            return $this->compileStatement($match);
        }, $value
        );
    }

    /**
     * Compile a single Blade @ statement.
     *
     * @param  array  $match
     * @return string
     */
    protected function compileStatement($match)
    {
        if (Str::contains($match[1], '@')) {
            $match[0] = isset($match[3]) ? $match[1].$match[3] : $match[1];
        } elseif (isset($this->customDirectives[$match[1]])) {
            $match[0] = $this->callCustomDirective($match[1], Arr::get($match, 3));
        } elseif (method_exists($this, $method = 'compile'.ucfirst($match[1]))) {
            $match[0] = $this->$method(Arr::get($match, 3));
        }

        return isset($match[3]) ? $match[0] : $match[0].$match[2];
    }


    /**
     * Call the given directive with the given value.
     *
     * @param  string  $name
     * @param  string|null  $value
     * @return string
     */
    protected function callCustomDirective($name, $value)
    {
        if (Str::startsWith($value, '(') && Str::endsWith($value, ')')) {
            $value = Str::substr($value, 1, -1);
        }

        return call_user_func($this->customDirectives[$name], trim($value));
    }



    /**
     * Strip the parentheses from the given expression.
     *
     * @param  string  $expression
     * @return string
     */
    public function stripParentheses($expression)
    {
        if (Str::startsWith($expression, '(')) {
            $expression = substr($expression, 1, -1);
        }

        return $expression;
    }



    /**
     * Replace the raw placeholders with the original code stored in the raw blocks.
     *
     * @param  string  $result
     * @return string
     */
    protected function restoreRawContent($result)
    {
        $result = preg_replace_callback('/'.$this->getRawPlaceholder('(\d+)').'/', function ($matches) {
            return $this->rawBlocks[$matches[1]];
        }, $result);

        $this->rawBlocks = [];

        return $result;
    }


    /**
     * Add the stored footers onto the given content.
     *
     * @param  string  $result
     * @return string
     */
    protected function addFooters($result)
    {
        return ltrim($result, PHP_EOL)
            .PHP_EOL.implode(PHP_EOL, array_reverse($this->footer));
    }


}