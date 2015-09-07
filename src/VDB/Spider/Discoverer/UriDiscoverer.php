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
     */
    public function __construct(array $schemas)
    {
        $this->pregMatchExpressions = $this->createPregMatchExpressions($schemas);
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
                    $uri = str_replace('"','',$matches[0]);
                    $uris[] = new Uri($uri, $document->getUri()->toString());
                } catch (UriSyntaxException $e) {
                    $spider->getStatsHandler()->addToFailed($matches[0], 'Invalid URI: ' . $e->getMessage());
                }
            }
        }
        return $uris;
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
