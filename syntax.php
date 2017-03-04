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
         $this->Lexer->addEntryPattern('\(' . MALG_TAG . '.*?\)$(?=.*?\(/' . MALG_TAG . '\))',$mode,'plugin_microalg');
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
                    list($div_id, $conf) = $this->_malg_parse_tag($match);
                    $error_msg = "";
                    // Check the div_id :
                    if ($div_id == "")
                        $error_msg = "Il manque l’identifiant du programme.";
                    if (!preg_match('/^[_a-zA-Z]+[_a-zA-Z0-9-]*$/', $div_id))
                        $error_msg = "L’identifiant ne doit contenir que des lettres, des chiffres et des tirets";
                    if (preg_match('/\s/', $div_id))
                        $error_msg = "L’identifiant ne doit pas contenir d’espace.";
                    // Check the conf :
                    $parsed_conf = json_decode($conf);
                    if ($conf != "" && $parsed_conf == NULL)
                        $error_msg = "La configuration est mal formée. Elle doit être au format JSON.";
                    // Display error messages and raise the error flag for UNMATCHED and EXIT :
                    if ($error_msg != "") {
                        $renderer->error = true;
                        $renderer->doc .= "<div class=\"error\">";
                        $renderer->doc .= "Vous voulez insérer du code MicroAlg ?<br>\n";
                        $renderer->doc .= $error_msg . "<br>\n";
                        $renderer->doc .= "Merci de taper:\n";
                        $renderer->doc .= "<pre>(MicroAlg \"identifiant_du_prg\" {configuration facultative au format JSON})\n";
                        $renderer->doc .= "(... ici votre programme ...)\n";
                        $renderer->doc .= "(/MicroAlg)</pre>\n";
                        $renderer->doc .= "</div>\n";
                    } else {
                        $renderer->doc .= '<div id="' . $div_id . '"></div>' . "\n";
                        $renderer->doc .= '<script>inject_microalg_editor_in("' . $div_id . '", {' . "\n";
                        // 2 and -2 to remove "{ and }"
                        $encoded = json_encode($conf);
                        $patterns = array('\\u0394');
                        $substs = array('Δ');
                        $semi_encoded = str_replace($patterns, $substs, $encoded);
                        $json_injection = stripslashes(substr($semi_encoded, 2, -2));
                        if ($json_injection) {
                            $renderer->doc .= $json_injection . ",\n";
                        }
                        $renderer->doc .= "src:\n";
                    }
                    break;
                case DOKU_LEXER_UNMATCHED :
                    if (! $renderer->error)
                        $renderer->doc .= $this->_malg_escape($match);
                    break;
                case DOKU_LEXER_EXIT :
                    if (! $renderer->error)
                        $renderer->doc .= "''});</script>" . "\n";
                    break;
            }
            return true;
        }
        if($mode == 'microalg') {
            list($state, $match) = $data;
            switch ($state) {
                case DOKU_LEXER_ENTER :
                    // Entry tag is like (MALG_TAG "div_id")
                    list($div_id, $conf_not_used) = $this->_malg_parse_tag($match);
                    $renderer->doc .= "\n# Programme " . $div_id;
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

    function _malg_parse_tag($match) {
        // Entry tag is like (MALG_TAG "div_id" {…optional conf as JSON…})
        $content = substr($match, strlen('(' . MALG_TAG . ' '), -(strlen(')')));
        // Grab the div_id :
        $first_quote = strpos($content, '"');
        $second_quote = strpos($content, '"', $first_quote + 1);
        $div_id = substr($content, $first_quote + 1, $second_quote - $first_quote - 1);
        // Grab the optional JSON :
        $conf = trim(substr($content, $second_quote + 1));
        return array($div_id, $conf);
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
