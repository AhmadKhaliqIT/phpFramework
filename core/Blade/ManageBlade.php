<?php
/* بسم الله الرحمن الرحیم */
/**
 * phpFramework
 *
 * @author     Ahmad Khaliq
 * @author     Mojtaba Zadegi
 * @copyright  2022 Ahmad Khaliq
 * @license    https://github.com/AhmadKhaliqIT/phpFramework/blob/main/LICENSE
 * @link       https://github.com/AhmadKhaliqIT/phpFramework/
 */



namespace Core\Blade;
use InvalidArgumentException;
use Core\Auth\Auth;
use Core\Database\DB;
class ManageBlade{


    protected array $sectionStack = [];


    /**
     * All of the finished, captured sections.
     *
     * @var array
     */
    protected array $sections = [];

    /**
     * Start injecting content into a section.
     *
     * @param string $section
     * @param string|null $content
     * @return void
     */
    public function startSection(string $section)
    {
            if (ob_start()) {
                $this->sectionStack[] = $section;
            }
    }


    /**
     * Stop injecting content into a section.
     *
     */
    public function stopSection()
    {
        if (empty($this->sectionStack)) {
            throw new InvalidArgumentException('Cannot end a section without first starting one.');
        }

        $last = array_pop($this->sectionStack);


        $this->sections[$last]=ob_get_clean();
    }


    public function MakeExtendView($view)
    {
        $template = new View();
        echo $template->make($view,$this->sections);
    }




}