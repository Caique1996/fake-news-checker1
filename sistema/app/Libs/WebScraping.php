<?php

namespace App\Libs;

use App\Models\HtmlFile;
use Illuminate\Support\Facades\Http;

class WebScraping
{
    public $html;
    private $htmlMetaTags = [];

    public function __construct(string $url, $domainMode = true)
    {

        $this->html = HtmlFile::createAndGetHtmlFile($url, $domainMode);
        $this->htmlMetaTags = $this->getUrlData();
        $this->htmlMetaTags2 = $this->getMetaTags();
    }

    function getMetaTags()
    {
        $str = $this->html;
        $pattern = '
  ~<\s*meta\s

  # using lookahead to capture type to $1
    (?=[^>]*?
    \b(?:name|property|http-equiv)\s*=\s*
    (?|"\s*([^"]*?)\s*"|\'\s*([^\']*?)\s*\'|
    ([^"\'>]*?)(?=\s*/?\s*>|\s\w+\s*=))
  )

  # capture content to $2
  [^>]*?\bcontent\s*=\s*
    (?|"\s*([^"]*?)\s*"|\'\s*([^\']*?)\s*\'|
    ([^"\'>]*?)(?=\s*/?\s*>|\s\w+\s*=))
  [^>]*>

  ~ix';

        if (preg_match_all($pattern, $str, $out))
            return array_combine($out[1], $out[2]);
        return array();
    }

    function getUrlData()
    {
        $result = false;

        $contents = $this->html;

        if (isset($contents) && is_string($contents)) {
            $title = null;
            $metaTags = null;

            preg_match('/<title>([^>]*)<\/title>/si', $contents, $match);

            if (isset($match) && is_array($match) && count($match) > 0) {
                $title = strip_tags($match[1]);
            }


            $result = array(
                'title' => $title,
                'metaTags' => []
            );
        }

        return $result;
    }

    public function getImage(): string
    {
        if (isset($this->htmlMetaTags2['og:image'])) {
            return trim($this->htmlMetaTags2['og:image']);
        }
        if (isset($this->htmlMetaTags2['twitter:image'])) {
            return trim($this->htmlMetaTags2['twitter:image']);
        }
        return "";
    }

    public function getSiteTitle(): string
    {
        if (isset($this->htmlMetaTags['title'])) {
            return trim($this->htmlMetaTags['title']);
        }
        return "";
    }

    public function getSiteDescription(): string
    {
        if (isset($this->htmlMetaTags2['description'])) {
            return trim($this->htmlMetaTags2['description']);
        }
        return "";
    }
}
