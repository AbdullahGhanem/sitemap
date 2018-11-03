<?php

namespace Helldar\Sitemap\Traits\Processes;

use DOMElement;
use Helldar\Sitemap\Services\Make\Images;
use Helldar\Sitemap\Services\Xml;
use Illuminate\Support\Collection;

trait ImagesProcess
{
    /** @var \Helldar\Sitemap\Services\Xml */
    protected $xml;

    /** @var array */
    protected $images = [];

    public function makeImages(): Images
    {
        return new Images;
    }

    /**
     * @param \Helldar\Sitemap\Services\Make\Images ...$images
     *
     * @return \Helldar\Sitemap\Traits\Processes\ImagesProcess
     */
    public function images($images): self
    {
        array_map(function ($image) {
            if ($image instanceof Images) {
                $this->pushImage($image);
            } else {
                array_map(function ($image) {
                    $this->images($image);
                }, $image);
            }
        }, func_get_args());

        return $this;
    }

    protected function processImages(Images $image)
    {
        $this->makeXml();

        $item = collect($image->get());

        $this->processImageSection($item);
    }

    private function processImageSection(Collection $item)
    {
        $images = $item->get('images', []);

        if (!$images) {
            return;
        }

        $xml = $this->xml->makeItem('url');

        if ($loc = $item->get('loc')) {
            $loc = $this->xml->makeItem('loc', $this->e($loc));
            $this->xml->appendChild($xml, $loc);
        }

        array_map(function ($image) use (&$xml) {
            $this->processImageImages($xml, $image);
        }, $item->get('images', []));

        $this->xml->appendToRoot($xml);
    }

    private function processImageImages(DOMElement &$xml, array $image = [])
    {
        $element = $this->xml->makeItem('image:image');

        array_map(function ($key, $value) use (&$element) {
            if ($key == 'loc') {
                $value = $this->e($value);
            }

            $el = $this->xml->makeItem('image:' . $key, $value);
            $this->xml->appendChild($element, $el);
        }, array_keys($image), array_values($image));

        $this->xml->appendChild($xml, $element);
    }

    private function makeXml()
    {
        $this->xml = Xml::init('urlset', [
            'xmlns'       => 'http://www.sitemaps.org/schemas/sitemap/0.9',
            'xmlns:image' => 'http://www.google.com/schemas/sitemap-image/1.1',
        ]);
    }

    private function pushImage(Images $image)
    {
        array_push($this->images, $image);
    }
}
