<?php

namespace Massive\Bundle\SearchBundle\Tests\Unit\Search\Metadata\Provider;

use Metadata\MetadataFactory;
use Massive\Bundle\SearchBundle\Search\Metadata\Provider\DefaultProvider;
use Massive\Bundle\SearchBundle\Search\Metadata\ClassMetadata;
use Massive\Bundle\SearchBundle\Search\Document;

class DefaultProviderTest extends \PHPUnit_Framework_TestCase
{
    private $provider;
    private $metadataFactory;
    private $metadata1;
    private $metadata2;
    private $document;

    public function setUp()
    {
        $this->metadataFactory = $this->prophesize(MetadataFactory::class);
        $this->metadata1 = $this->prophesize(ClassMetadata::class);
        $this->metadata2 = $this->prophesize(ClassMetadata::class);
        $this->document = $this->prophesize(Document::class);
        $this->provider = new DefaultProvider(
            $this->metadataFactory->reveal()
        );
    }

    /**
     * It should return metadata for the given object
     */
    public function testGetMetadataForObject()
    {
        $object = new \stdClass;
        $this->metadataFactory->getMetadataForClass('stdClass')->willReturn($this->metadata1->reveal());
        $metadata = $this->provider->getMetadataForObject($object);
        $this->assertSame($this->metadata1->reveal(), $metadata);
    }

    /**
     * It should return all metadatas
     */
    public function testGetAllMetadata()
    {
        $this->metadataFactory->getAllClassNames()->willReturn(array('one', 'two'));
        $this->metadataFactory->getMetadataForClass('one')->willReturn($this->metadata1->reveal());
        $this->metadataFactory->getMetadataForClass('two')->willReturn($this->metadata2->reveal());
        $metadatas = $this->provider->getAllMetadata();

        $this->assertSame(array(
            $this->metadata1->reveal(),
            $this->metadata2->reveal(),
        ), $metadatas);
    }

    /**
     * It should return the metadata for a search document
     */
    public function testGetMetadataForDocument()
    {
        $this->document->getClass()->willReturn('Class');
        $this->metadataFactory->getMetadataForClass('Class')->willReturn($this->metadata1->reveal());
        $metadata = $this->provider->getMetadataForDocument($this->document->reveal());
        $this->assertSame($this->metadata1->reveal(), $metadata);
    }
}
