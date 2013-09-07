<?php if (!defined('BASEPATH')) exit('No direct script access allowed');
/**
 * ezmedia class
 * A simple class to create and parse package specifiq requests
 *
 * @version    1.0
 * @package    ezRbac
 * @since      ezRbac v 0.2
 * @author     Roni Kumar Saha<roni.cse@gmail.com>
 * @copyright  Copyright &copy; 2012 Roni Saha
 * @license    GPL v3 - http://www.gnu.org/licenses/gpl-3.0.html
 *
 */

class ezmedia
{
    private $_usecache = TRUE;

    private $CI;

    function __construct()
    {
        $this->CI = & get_instance();

        $file = $this->CI->ezRbacPath . DIRECTORY_SEPARATOR . $this->CI->ezuri->ruri_string(DIRECTORY_SEPARATOR);

        $this->_serveFile($file);
        //We need this to clene resources! Like db connections etc..
        $this->CI->we_are_done = TRUE;
        exit;
    }

    /**
     * Publishes a file to user.
     * This method will serve the original file or the browser cached version if available and uppon user settings
     *
     * @param string|null $file
     *
     * @return void
     * @throws 404 not found  if the requested file does not exist.
     */
    private function _serveFile($file = NULL)
    {
        if (!is_file($file)) {
            show_404('requested url is invalid');
        }
        $Modified   = filemtime($file);
        $gmdate_mod = gmdate('D, d M Y H:i:s', $Modified) . " GMT";

        if ($this->_usecache) {
            if (isset($_SERVER['HTTP_IF_MODIFIED_SINCE'])) {
                $if_modified_since = preg_replace('/;.*$/', '', $_SERVER['HTTP_IF_MODIFIED_SINCE']);
                if ($if_modified_since >= $gmdate_mod) //Browser have the data
                {
                    $this->CI->output->set_header('HTTP/1.1 304 Not Modified'); // HTTP/1.1
                    //header('HTTP/1.1 304 Not Modified');
                    $this->CI->we_are_done = TRUE;
                    exit();
                }
            } //no cache found, so we serve original file
        } else {
            $this->CI->output->set_header("Cache-Control: no-cache, must-revalidate"); //Cache-Controle
            //header("Cache-Control: no-cache, must-revalidate");  // HTTP/1.1
            $this->CI->output->set_header("Expires: Sat, 26 Jul 1997 05:00:00 GMT"); //Date in the past
            // header("Expires: Sat, 26 Jul 1997 05:00:00 GMT");  // Date in the past
        }

        $mime = get_mime_by_extension($file);

        $this->CI->output->set_header("HTTP/1.0 200 OK")
            ->set_header("HTTP/1.1 200 OK")
            ->set_content_type($mime)
            ->set_header('Last-Modified: ' . $gmdate_mod) //Last modified
            ->set_header('Content-Length: ' . filesize($file)) //Size of content help browser to monitor progress
            ->set_output(file_get_contents($file));

    }
}

/* End of file ezmedia.php */
/* Location: ./ezRbac/libraries/ezmedia.php */