<?php

namespace Helldar\Sitemap\Traits;

use Illuminate\Support\Str;

trait Helpers
{
    private $item = [];

    /**
     * Escape HTML special characters in a string.
     *
     * @param $value
     *
     * @return string
     */
    protected function e($value): string
    {
        if (is_null($value)) {
            return null;
        }

        return htmlspecialchars($value, ENT_QUOTES, 'UTF-8', false);
    }

    /**
     * @param \Illuminate\Support\Collection $item
     * @param array $fields
     *
     * @return array
     */
    protected function routeParameters($item, $fields = []): array
    {
        $result = [];

        foreach ($fields as $key => $value) {
            $key   = is_numeric($key) ? $value : $key;
            $value = $item->{$value} ?? false;

            if ($value) {
                $result[$key] = $value;
            }
        }

        return $result;
    }

    /**
     * Add an item parameter to the resulting array.
     *
     * @param string $key
     * @param mixed $value
     */
    protected function setElement(string $key, $value)
    {
        if (!empty($value)) {
            $this->item[$key] = $value;
        }
    }

    /**
     * Push an item parameter or array of parameters to the resulting array.
     *
     * @param string $key
     * @param mixed $value
     */
    protected function pushElement(string $key, $value)
    {
        if (empty($value)) {
            return;
        }

        if (!array_key_exists($key, $this->item)) {
            $this->item[$key] = [];
        }

        array_push($this->item[$key], $value);
    }

    protected function clearDirectory(string $prefix = null)
    {
        if (empty($prefix)) {
            return;
        }

        $ext  = pathinfo($prefix, PATHINFO_EXTENSION);
        $path = Str::before($prefix, '.' . $ext);

        $path = storage_path("app/public/{$path}*.{$ext}");

        foreach (glob($path) as $file) {
            unlink($file);
        }
    }
}
