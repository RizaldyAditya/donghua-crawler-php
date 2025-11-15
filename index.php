<?php
require __DIR__ . '/vendor/autoload.php';

use Crwlr\Crawler\HttpCrawler;
use Crwlr\Crawler\UserAgents\UserAgent;
use Crwlr\Crawler\UserAgents\UserAgentInterface;
use Dotenv\Dotenv;

class MyCrawler extends HttpCrawler
{
    protected function userAgent(): UserAgentInterface
    {
        return new UserAgent('Mozilla / 5.0(Windows NT10.0; Win64; x64)AppleWebKit / 537.36(KHTML, like Gecko)Chrome / 142.0.0.0Safari / 537.36');
    }
}

/* 
 * Supported websites:
 * https: //animexin.dev/
 * https: //donghuastream.org/
*/

// Load environment variables
$dotenv = Dotenv::createImmutable(__DIR__);
$dotenv->safeLoad();

// Parse command line arguments to simulate GET parameters
$cli_args = array_slice($argv, 1);
$query_string = implode('&', $cli_args);
parse_str($query_string, $_GET);
$web = $_GET['web'] ?? null;

// Validate 'web' parameter
if (empty($web)) {
    die('Please provide a WEB Parameter, e.g., web=animexin');
}

// Helper function to extract episode number
function episode_number($str)
{
    preg_match('/\d+/', $str, $matches);
    return isset($matches[0]) ? (int) $matches[0] : '';
}

// Route to the appropriate web crawler based on the 'web' parameter
switch ($web) {
    case 'animexin':
        require __DIR__ . '/web_animexin.php';
        break;
    case 'donghuastream':
        require __DIR__ . '/web_donghuastream.php';
        break;
    default:
        die('The provided URL is not supported.');
}
