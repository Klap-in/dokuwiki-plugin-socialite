<?php
/**
 * DokuWiki Plugin lsb (Syntax Component)
 *
 * @license GPL 2 http://www.gnu.org/licenses/gpl-2.0.html
 * @author  Fernando Ribeiro <pinguim.ribeiro@gmail.com>
 */

// must be run within Dokuwiki
if (!defined('DOKU_INC')) die();

class syntax_plugin_lsb_button extends DokuWiki_Syntax_Plugin {
    /**
     * @return string Syntax mode type
     */
    public function getType() {
        return 'substition';
    }
    /**
     * @return string Paragraph type
     */
    public function getPType() {
        return 'normal';
    }
    /**
     * @return int Sort order - Low numbers go before high numbers
     */
    public function getSort() {
        return 999;
    }

    /**
     * Connect lookup pattern to lexer.
     *
     * @param string $mode Parser mode
     */
    public function connectTo($mode) {
        $this->Lexer->addSpecialPattern('~~LSB\b.*?~~',$mode,'plugin_lsb_button');
//        $this->Lexer->addEntryPattern('<FIXME>',$mode,'plugin_lsb_button');
    }

//    public function postConnect() {
//        $this->Lexer->addExitPattern('</FIXME>','plugin_lsb_button');
//    }

    /**
     * Handle matches of the lsb syntax
     *
     * @param string $match The match of the syntax
     * @param int    $state The state of the handler
     * @param int    $pos The position in the document
     * @param Doku_Handler    $handler The handler
     * @return array Data for the renderer
     */
    public function handle($match, $state, $pos, Doku_Handler &$handler){
        $match = strtolower(trim(substr($match, 5, -2))); // strip markup

        if (empty($match)) {
            $match = strtolower(trim($this->getConf('networks')));
        }

        return explode(' ', $match);
    }

    /**
     * Render xhtml output or metadata
     *
     * @param string         $mode      Renderer mode (supported modes: xhtml)
     * @param Doku_Renderer  $renderer  The renderer
     * @param array          $data      The data from the handler() function
     * @return bool If rendering was successful.
     */
    public function render($mode, Doku_Renderer &$renderer, $data) {

        if($mode != 'xhtml') return false;

        $renderer->doc .= '<ul class="lsb">';

        // validation list of available social networks
        $protected = array('twitter', 'facebook', 'google+', 'linkedin', 'pinterest', 'tumblr', 'reddit');


        foreach ($data as $network) {
            if (in_array($network, $protected)) {
                $renderer->doc .= $this->lsb_button($network);
            }
        }
        $renderer->doc .= '</ul>';

        return true;
    }

    /**
     * Render xhtml output for facebook share button
     *
     * @param string $network The social network to render the button to
     * @return string Xhtml code for button.
     */
    protected function lsb_button ($network) {
        global $ID;
        global $INFO;

        $url      = rawurlencode(wl($ID,'',true));
        $title    = rawurlencode(($INFO['meta']['title']) ? $INFO['meta']['title'] : $meta);
        $abstract = rawurlencode($INFO['meta']['description']['abstract']);
        $text     = $this->lsb_getText($network);
        $class    = 'ico-' . $network;

        // see: https://github.com/cferdinandi/social-sharing
        // <a href="https://twitter.com/intent/tweet?text=YOUR-TITLE&url=YOUR-URL&via=TWITTER-HANDLE">Tweet</a>
        // <a href="https://www.facebook.com/sharer/sharer.php?u=YOUR-URL">Share on Facebook</a>
        // <a href="https://plus.google.com/share?url=YOUR-URL">Plus on Google+</a>
        // <a href="https://www.linkedin.com/shareArticle?mini=true&url=YOUR-URL&title=YOUR-TITLE&summary=YOUR-SUMMARY&source=YOUR-URL">Share on LinkedIn</a>
        // <a href="https://pinterest.com/pin/create/button/?url=YOUR-URL&description=YOUR-DESCRIPTION&media=YOUR-IMAGE-SRC">Pin on Pinterest</a>
        // <a href="https://vk.com/share.php?url=YOUR-URL&title=YOUR-TITLE&description=YOUR-DESCRIPTION&image=YOUR-IMAGE-SRC&noparse=true">Share on VK</a>
        // <a href="https://www.xing-share.com/app/user?op=share;sc_p=xing-share;url=YOUR-URL">Share on Xing</a>
        // <a href="http://www.tumblr.com/share/link?url=YOUR-URL&description=YOUR-DESCRIPTION">Share on Tumblr</a>
        // <a href="http://www.reddit.com/submit?url=YOUR_URL&title=YOUR_TITLE">Share on Reddit</a>
 
        switch ($network) {
            case 'twitter':
                $href = 'https://twitter.com/intent/tweet?url=' . $url . '&text='. $title .'&via=TWITTER-HANDLE';
                break;
            case 'facebook':
                $href = 'http://www.facebook.com/sharer.php?u='. $url;
                break;
            case 'google+':
                $href = 'https://plus.google.com/share?url='. $url;
                break;
            case 'linkedin':
                $href = 'href="https://www.linkedin.com/shareArticle?url='. $url .'&title=' . $title . '&summary=' . $abstract . '&mini=true&source=' . $url;
                break;
            case 'pinterest':
                $href = 'https://pinterest.com/pin/create/button/?url='. $url .'&description=' . $abstract . '&media=YOUR-IMAGE-SRC';
                break;
            case 'tumblr':
                $href = 'http://www.tumblr.com/share/link?url='. $url .'&description=' . $abstract;
                break;
            case 'reddit':
                $href = 'http://www.reddit.com/submit?url='. $url .'&title=' . $title;
                break;
        }

        $xhtml  = '<li class="lsb-item">';
        $xhtml .= '<a class="lsb-link ' . $class . '" href="' . $href . '">' . $text . '</a>';
        $xhtml .= '</li>';

        return $xhtml;
    }


    protected function lsb_getText($network) {

        $display = $this->getConf('display');
        if ($display == 'name') {
            return $network;
        } elseif ($display == 'text') {
            return $this->getLang($network.'_text');
        } else {
            return '';
        }
    }

}
