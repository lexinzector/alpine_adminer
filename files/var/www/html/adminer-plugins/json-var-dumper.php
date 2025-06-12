<?php

use Symfony\Component\VarDumper\Cloner\VarCloner;
use Symfony\Component\VarDumper\Dumper\AbstractDumper;
use Symfony\Component\VarDumper\Dumper\HtmlDumper;


/**
 * Uses symfony's var-dumper for pretty treeview of decoded json in table and edit view.
 * needs symfony/var-dumper, install with:
 *
 *      composer require symfony/var-dumper:5.0.8
 *
 * in the file, where you add the plugins to adminer, add composer's autoloader:
 *
 *      include __DIR__ . "/vendor/autoload.php";
 *
 * 05/2020 v1.0.0
 *
 * @link https://www.adminer.org/plugins/#use
 * @author Marc Christenfeldt, https://www.christenfeldt-edv.de/
 * @license https://www.apache.org/licenses/LICENSE-2.0 Apache License, Version 2.0
 * @license https://www.gnu.org/licenses/gpl-2.0.html GNU General Public License, version 2 (one or other)
 */
class AdminerJsonVarDumper
{
    private $theme;


    /**
     * @param string $theme 'light' or 'dark'
     */
    public function __construct(string $theme = 'light')
    {
        $this->theme = $theme;
    }

    private function _decodeJson($value)
    {
        if ((substr($value, 0, 1) == '{' || substr($value, 0, 1) == '[')) {
            return json_decode($value, true);
        }

        return null;
    }


    private function _getDump($obj, string $title = null)
    {
        $cloner = new VarCloner();
        $dumper = new HtmlDumper(null, null, AbstractDumper::DUMP_LIGHT_ARRAY);
        $dumper->setTheme($this->theme);
        $html = $dumper->dump($cloner->cloneVar($obj), true, ['maxDepth' => 0]);

        $html = str_replace('<script>', '<script ' . nonce() . '>', $html);
        if (!is_null($title)) {
            $html = preg_replace('#(<pre class=sf-dump.*?)>#', '$1 style="z-index:0" >' . $title, $html);
        }

        return $html;
    }


    function selectVal(&$val, $link, $field, $original)
    {
        if (($json = $this->_decodeJson($original)) !== null) {
            $val = $this->_getDump($json, $val);
        }
    }


    function editInput($table, $field, $attrs, $value)
    {
        if (($json = $this->_decodeJson($value)) !== null) {
            echo $this->_getDump($json);
        }
    }
}
