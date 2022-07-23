<?php

namespace Core\Blade\Concerns;

use Illuminate\View\Factory as ViewFactory;

trait CompilesLayouts
{
    /**
     * The name of the last section that was started.
     *
     * @var string
     */
    protected $lastSection;

    /**
     * Compile the extends statements into valid PHP.
     *
     * @param  string  $expression
     * @return string
     */
    protected function compileExtends($expression)
    {
        $expression = $this->stripParentheses($expression);

        $echo = "<?php echo \$__env->MakeExtendView($expression); ?>";

        $this->footer[] = $echo;

        return '';
    }

    /**
     * Compile the section statements into valid PHP.
     *
     * @param  string  $expression
     * @return string
     */
    protected function compileSection($expression)
    {
        $this->lastSection = trim($expression, "()'\" ");

        return "<?php \$__env->startSection{$expression};  ?>";
    }

    /**
     * Replace the @parent directive to a placeholder.
     *
     * @return string
     */
    protected function compileParent()
    {
        return ViewFactory::parentPlaceholder($this->lastSection ?: '');
    }

    /**
     * Compile the yield statements into valid PHP.
     *
     * @param  string  $expression
     * @return string
     */
    protected function compileYield($expression)
    {
        $expression=substr($expression, 2, -2);

        return "<?php echo $$expression; ?>";
    }

    /**
     * Compile the show statements into valid PHP.
     *
     * @return string
     */
    protected function compileShow()
    {
        return '<?php echo $__env->yieldSection(); ?>';
    }

    /**
     * Compile the append statements into valid PHP.
     *
     * @return string
     */
    protected function compileAppend()
    {
        return '<?php $__env->appendSection(); ?>';
    }

    /**
     * Compile the overwrite statements into valid PHP.
     *
     * @return string
     */
    protected function compileOverwrite()
    {
        return '<?php $__env->stopSection(true); ?>';
    }

    /**
     * Compile the stop statements into valid PHP.
     *
     * @return string
     */
    protected function compileStop()
    {
        return '<?php $__env->stopSection(); ?>';
    }

    /**
     * Compile the end-section statements into valid PHP.
     *
     * @return string
     */
    protected function compileEndsection()
    {
        return '<?php $__env->stopSection(); ?>';
    }
}
