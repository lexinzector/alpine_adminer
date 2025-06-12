<?php
/**
 * Adminer plugin
 * Download select result as XLSX format.
 *
 * Install to Adminer on http://www.adminer.org/plugins/
 * @author Tom Higuchi, http://tom-gs.com/
 */

class AdminerDumpXlsx
{
    /**
     * @var  string
     */
    private $pathToSheetJs;

    /**
     * @var string
     */
    private $pathToFileSaverJs;

    /**
     * @var string
     */
    private $pathToDumpXlsxJs;

    /**
     * @param string|null $pathToSheetJs
     * @param string|null $pathToFileSaverJs
     * @param string|null $pathtoDumpXlsxJs
     */
    public function __construct($pathToSheetJs = null, $pathToFileSaverJs = null, $pathToDumpXlsxJs = null)
    {
        $this->pathToSheetJs = $pathToSheetJs ?? 'https://cdnjs.cloudflare.com/ajax/libs/xlsx/0.16.9/xlsx.full.min.js';
        $this->pathToFileSaverJs = $pathToFileSaverJs ?? 'https://cdnjs.cloudflare.com/ajax/libs/FileSaver.js/2.0.5/FileSaver.min.js';
        $this->pathToDumpXlsxJs = $pathToDumpXlsxJs ?? __DIR__ . '/dumpxlsx.js';
    }

    /**
     * @param string $path
     * @return string
     */
    private function addNonce($path)
    {
        if (strpos('?', $path)) {
            return $path .= '&nonce=' . Adminer\get_nonce();
        }
        return $path .= '?nonce=' . Adminer\get_nonce();
    }

    public function head()
    {
        echo Adminer\script_src($this->addNonce($this->pathToSheetJs));
        echo Adminer\script_src($this->addNonce($this->pathToFileSaverJs));
        printf("<script%s>\n%s</script>\n", Adminer\nonce(), file_get_contents($this->pathToDumpXlsxJs));
    }
}
