<?php
namespace VDB\Spider\Discoverer;

use VDB\Spider\Discoverer\Discoverer;
use VDB\Spider\Resource;
use VDB\Spider\Spider;
use VDB\Uri\Exception\UriSyntaxException;
use VDB\Uri\Uri;
use VDB\Uri\UriInterface;

/**
 * @author Tobinski
 * @copyright 2015 Tobinski
 */
class UriDiscoverer implements Discoverer
{
    /** @var string */
    protected $pregMatchExpressions;

    /**
     * @param array $schemas
     * @param array $modifier. An array of uri postfixes in case you wanna scrap a special format
     */
    public function __construct(array $schemas, array $modifier = array())
    {
        $this->pregMatchExpressions = $this->createPregMatchExpressions($schemas);
        $this->modifier = $modifier;
    }

    /**
     * @param Spider $spider
     * @param Resource $document
     * @return Uri[]
     */
    public function discover(Spider $spider, Resource $document)
    {
        $content = $document->getResponse()->getBody();
        $uris = array();
        foreach ($this->pregMatchExpressions as $expression) {
            if(preg_match($expression,$content,$matches))
            {
                try {
                    $url = str_replace('"','',$matches[0]);
                    $uris[] = new Uri($url);
                } catch (UriSyntaxException $e) {
                    $spider->getStatsHandler()->addToFailed($matches[0], 'Invalid URI: ' . $e->getMessage());
                }
            }
        }
        return $this->modify($uris);
    }

    /**
     * Add a postfix to an url. This is f.e. useful, if you wanna scrap a rdf instead a html
     * @param array $uris
     * @return array
     */
    private function modify (array $uris)
    {
        foreach($uris as $uri)
        {
            if(array_key_exists($uri->getHost(), $this->modifier))
            {
                $result[] = new Uri($uri->toString().$this->modifier[$uri->getHost()]);
            }
            else {
                $result[] = $uri;
            }
        }
        return $result;
    }
    /**
     * Create a pregMatch expression for a given schema (http://example.com/%identifier%/person)
     * @param $schemas
     * @return array
     */
    private function createPregMatchExpressions($schemas)
    {
        $pattern = array();
        foreach ($schemas as $schema) {
            $patter_split = preg_split("/%identifier%/", $schema);

            // find pattern
            if (empty($patter_split[1])) {
                $pregPattern = "#\"".preg_quote($patter_split[0])."(.*?)\"#";
            } else {
                $pregPattern = "#\"".preg_quote($patter_split[0])."(.*?)".preg_quote($patter_split[1])."\"#";
            }
            $pattern[] = $pregPattern;
        }
        return $pattern;
    }
}
