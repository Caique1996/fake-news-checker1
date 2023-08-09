<?php

namespace App\Libs;

class GoogleSearchResultObject
{
    private $result;

    public function __construct(?array $result)
    {
        $this->result = $result;
    }

    public function getPageMap()
    {
        $result = $this->result;
        if (isset($result['pagemap'])) {
            return $result['pagemap'];
        }
        return null;
    }

    public function getThumbNail()
    {
        $pageMap = $this->getPageMap();
        if (isset($pageMap['cse_thumbnail'][0])) {
            return $pageMap['cse_thumbnail'][0];
        }

        return null;
    }

    public function getCseImages()
    {
        $pageMap = $this->getPageMap();
        if (isset($pageMap['cse_image'][0])) {
            return $pageMap['cse_image'][0];
        }

        return null;
    }

    public function getNewsActicle()
    {
        $pageMap = $this->getPageMap();
        if (isset($pageMap['newsarticle'][0])) {
            return $pageMap['newsarticle'][0];
        }

        return null;
    }

    public function getMetaTags()
    {
        $pageMap = $this->getPageMap();
        if (isset($pageMap['metatags'][0])) {
            return $pageMap['metatags'][0];
        }

        return null;
    }

    public function getTitle()
    {
        return $this->getTag("title");
    }

    public function getDescription()
    {
        return $this->getTag("description");
    }

    public function getTag($name)
    {
        $result = $this->result;
        $metatags = $this->getMetaTags();
        $metaTag = $this->getArrInfo($metatags, ["og:$name", "twitter:$name", "fb:$name", "$name"]);
        if (!is_null($metaTag)) {
            return $metaTag;
        }
        $alias = [];
        $alias['description'] = ['snippet'];

        if (isset($alias[$name])) {
            $alias[$name][] = $name;
            $indexes = $alias;
        } else {
            $indexes = [$name];

        }


        $metaTag = $this->getArrInfo($result, $indexes);
        if (!is_null($metaTag)) {
            return $metaTag;
        }
        return "-";

    }

    public function getUrl()
    {
        $result = $this->result;
        $resulTitle = $this->getArrInfo($result, ['link']);
        if (!is_null($resulTitle)) {
            return $resulTitle;
        }
        return $this->getTag('url');

    }

    public function getDate()
    {
        $news = $this->getNewsActicle();
        $result = $this->result;

        $resulTitle = $this->getArrInfo($news, ['datepublished']);
        if (!is_null($resulTitle)) {
            return formatDateToDb($resulTitle);
        }
        $resulTitle = $this->getArrInfo($result, ['snippet']);
        if (!is_null($resulTitle)) {
            return convertGoogleDate($resulTitle);
        }

        return "-";
    }

    public function getImage()
    {
        $imagesArr = $this->getCseImages();
        $image = $this->getArrInfo($imagesArr, ['src']);
        if (!is_null($image)) {
            return $image;
        }
        $thumnails = $this->getThumbNail();
        $image = $this->getArrInfo($thumnails, ['src']);
        if (!is_null($image)) {
            return $image;
        }

        return $this->getTag('image');


    }

    private function getArrInfo(?array $rows, ?array $indexList)
    {

        return getAnyIndexArray($rows, $indexList);
    }

}
