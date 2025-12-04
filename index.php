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
 * https: //animekhor.org/
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

// Helper function to strip tags and their content
function strip_tags_content($text, $tags = '', $invert = false)
{
    preg_match_all('/<(.+?)[\s]*\/?[\s]*>/si', trim($tags), $tags);
    $tags = array_unique($tags[1]);

    if (is_array($tags) and count($tags) > 0) {
        if ($invert == false) {
            return preg_replace('@<(?!(?:' . implode('|', $tags) . ')\b)(\w+)\b.*?>.*?</\1>@si', '', $text);
        } else {
            return preg_replace('@<(' . implode('|', $tags) . ')\b.*?>.*?</\1>@si', '', $text);
        }
    } elseif ($invert == false) {
        return preg_replace('@<(\w+)\b.*?>.*?</\1>@si', '', $text);
    }
    return $text;
}

// Ensure logs directory and error log file exist
if (!file_exists(__DIR__ . '/logs/error.log')) {
    touch(__DIR__ . '/logs/error.log');
}

// Route to the appropriate web crawler based on the 'web' parameter
switch ($web) {
    case 'animexin':
        require __DIR__ . '/web_animexin.php';
        break;
    case 'donghuastream':
        require __DIR__ . '/web_donghuastream.php';
        break;
    case 'animekhor':
        require __DIR__ . '/web_animekhor.php';
        break;
    default:
        die('The provided URL is not supported.');
}
