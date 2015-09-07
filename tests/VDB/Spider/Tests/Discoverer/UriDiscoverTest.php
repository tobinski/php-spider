<?php

/*
 * This file is part of the Spider package.
 *
 * (c) tobinski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace VDB\Spider\Tests\Discoverer;

use VDB\Spider\Discoverer\UriDiscoverer;

/**
 *
 */
class UriDiscovererDiscovererTest extends DiscovererTestCase
{
    /**
     * @covers VDB\Spider\Discoverer\UriDiscoverer::discover()
     */
    public function testDiscover()
    {
        $discoverer = new UriDiscoverer(array("http://php-spider.org/%identifier%"));

        $uris = $discoverer->discover($this->spider, $this->spiderResource);
        $uri = $uris[0];

        $this->assertInstanceOf('VDB\\Uri\\Uri', $uri);
        $this->assertEquals($this->uri->toString(), $uri->toString());
    }
}
