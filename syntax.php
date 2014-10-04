<?php
/**
 * Plugin MicroAlg: Embed MicroAlg interactive snippets.
 *
 * @license    GPL 2 (http://www.gnu.org/licenses/gpl.html)
 * @author     Prof Gragnic <profgra.org@gmail.com>
 */

// must be run within DokuWiki
if(!defined('DOKU_INC')) die();

if(!defined('DOKU_PLUGIN')) define('DOKU_PLUGIN',DOKU_INC.'lib/plugins/');
require_once(DOKU_PLUGIN.'syntax.php');

define(MALG_TAG, "MicroAlg");

/**
 * All DokuWiki plugins to extend the parser/rendering mechanism
 * need to inherit from this class
 */
class syntax_plugin_microalg extends DokuWiki_Syntax_Plugin {

    function getType(){
        return 'protected';
    }
    function getPType(){
        return 'block';
    }
    function getSort(){
        return 157;
    }
    function connectTo($mode) {
         $this->Lexer->addEntryPattern('\(' . MALG_TAG . '.*?\)(?=.*?\(/' . MALG_TAG . '\))',$mode,'plugin_microalg');
    }
    function postConnect() {
         $this->Lexer->addExitPattern('\(/' . MALG_TAG . '\)','plugin_microalg');
     }


    /**
     * Handle the match
     */
    function handle($match, $state, $pos, &$handler){
        switch ($state) {
          case DOKU_LEXER_ENTER :
                return array($state, $match);
          case DOKU_LEXER_UNMATCHED :
                return array($state, $match);
          case DOKU_LEXER_EXIT :
                return array($state, '');
        }
        return array();
    }

    /**
     * Create output
     */
    function render($mode, &$renderer, $data) {
        if($mode == 'xhtml'){
            list($state, $match) = $data;
            switch ($state) {
                case DOKU_LEXER_ENTER :
                    // Entry tag is like (MALG_TAG "div_id")
                    $div_id = substr($match, strlen('(' . MALG_TAG . ' "'), -(strlen('")')));
                    $error_msg = "";
                    if ($div_id == "")
                        $error_msg = "Il manque l’identifiant du programme.";
                    if (!preg_match('/^[_a-zA-Z]+[_a-zA-Z0-9-]*$/', $div_id))
                        $error_msg = "L’identifiant ne doit contenir que des lettres, des chiffres et des tirets";
                    if (preg_match('/\s/', $div_id))
                        $error_msg = "L’identifiant ne doit pas contenir d’espace.";
                    if ($error_msg != "") {
                        $renderer->doc .= "<div class=\"error\">";
                        $renderer->doc .= "Vous voulez insérer du code MicroAlg ?<br>\n";
                        $renderer->doc .= $error_msg . "<br>\n";
                        $renderer->doc .= "Merci de taper:\n";
                        $renderer->doc .= "<pre>(MicroAlg \"identifiant_du_prg\")\n";
                        $renderer->doc .= "(... ici votre programme ...)\n";
                        $renderer->doc .= "(/MicroAlg)</pre>\n";
                        $renderer->doc .= "</div>\n";
                    } else {
                        $renderer->doc .= '<div id="' . $div_id . '"></div>' . "\n";
                        $renderer->doc .= '<script>inject_microalg_editor_in("' . $div_id . '", {localStorage: false},' . "\n";
                    }
                    break;
                case DOKU_LEXER_UNMATCHED :
                    $renderer->doc .= $this->_malg_escape($match);
                    break;
                case DOKU_LEXER_EXIT :
                    $renderer->doc .= "'');</script>" . "\n";
                    break;
            }
            return true;
        }
        if($mode == 'microalg') {
            list($state, $match) = $data;
            switch ($state) {
                case DOKU_LEXER_ENTER :
                    // Entry tag is like (MALG_TAG "div_id")
                    $div_id = substr($match, strlen('(' . MALG_TAG . ' "'), -(strlen('")')));
                    $error_msg = "";
                    if ($div_id == "")
                        $error_msg = "Il manque l’identifiant du programme.";
                    if (!preg_match('/^[_a-zA-Z]+[_a-zA-Z0-9-]*$/', $div_id))
                        $error_msg = "L’identifiant ne doit contenir que des lettres, des chiffres et des tirets";
                    if (preg_match('/\s/', $div_id))
                        $error_msg = "L’identifiant ne doit pas contenir d’espace.";
                    if ($error_msg != "") {
                        $renderer->doc .= "Vous voulez insérer du code MicroAlg ?\n";
                        $renderer->doc .= $error_msg . "\n";
                        $renderer->doc .= "Merci de taper:\n";
                        $renderer->doc .= "    (MicroAlg \"identifiant_du_prg\")\n";
                        $renderer->doc .= "(... ici votre programme ...)\n";
                    } else {
                        $renderer->doc .= "\n# Programme " . $div_id;
                    }
                    break;
                case DOKU_LEXER_UNMATCHED :
                    $renderer->doc .= $match;
                    break;
                case DOKU_LEXER_EXIT :
                    // nothing
                    break;
            }
            return true;
        }
        return false;
    }

    function _malg_escape($src) {
        $result = '';
        foreach (explode("\n", trim($src, "\n")) as $line) {
            $line = str_replace('"', '\"', $line);
            $line = str_replace('//', '', $line);
            $line = str_replace('/*', '', $line);
            $result .= ('"' . $line . '\n" +' . "\n");
        }
        return $result;
    }
}
?>
