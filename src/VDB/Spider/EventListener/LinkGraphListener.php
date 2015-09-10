<?php
namespace VDB\Spider\EventListener;

use Symfony\Component\EventDispatcher\GenericEvent;
use VDB\Spider\LinkGraph;

/**
 * @author tobinski
 * @copyleft 2015 tobinski
 */
class LinkGraphListener
{
    /** @var string */
    private $previousRoot;

    /** @var LinkGraph */
    private $graph;

    /**
     * @param LinkGraph $graph a graph object to store the connection between the sites
     */
    public function __construct(LinkGraph $graph)
    {
        $this->graph = $graph;
    }

    /**
     * @param GenericEvent $event
     */
    public function onCrawlPreRequest(GenericEvent $event)
    {
        $baseUrl = $event->getArgument('uri')->toString();
        $this->graph->addNode($baseUrl);
        $this->previousRoot = $baseUrl;
    }

    /**
     * @param GenericEvent $event
     */
    public function onCrawlPostFilter(GenericEvent $event)
    {
        $linkUri = $event->getArgument('uri')->toString();
        $this->graph->addNode($linkUri);
        $this->graph->addEdge($this->previousRoot, $linkUri);
    }

    /**
     * @return LinkGraph
     */
    public function getGraph()
    {
        return $this->graph;
    }
}
