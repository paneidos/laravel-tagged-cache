<?php


namespace Paneidos\LaravelTaggedCache;

use Illuminate\Cache\TaggableStore;

class TaggedStore extends TaggableStore
{
    /**
     * @var TaggableStore
     */
    private $backend;
    private $tag;

    public function __construct(TaggableStore $backend, $tag)
    {
        $this->backend = $backend;
        $this->tag = $tag;
    }

    /**
     * Retrieve an item from the cache by key.
     *
     * @param  string|array $key
     * @return mixed
     */
    public function get($key)
    {
        return $this->tags([])->get($key);
    }

    /**
     * Retrieve multiple items from the cache by key.
     *
     * Items not found in the cache will have a null value.
     *
     * @param  array $keys
     * @return array
     */
    public function many(array $keys)
    {
        return $this->tags([])->many($keys);
    }

    /**
     * Store an item in the cache for a given number of seconds.
     *
     * @param  string $key
     * @param  mixed $value
     * @param  int $seconds
     * @return bool
     */
    public function put($key, $value, $seconds)
    {
        return $this->tags([])->put($key, $value, $seconds);
    }

    /**
     * Store multiple items in the cache for a given number of seconds.
     *
     * @param  array $values
     * @param  int $seconds
     * @return bool
     */
    public function putMany(array $values, $seconds)
    {
        return $this->tags([])->putMany($values, $seconds);
    }

    /**
     * Increment the value of an item in the cache.
     *
     * @param  string $key
     * @param  mixed $value
     * @return int|bool
     */
    public function increment($key, $value = 1)
    {
        $taggedCache = $this->tags([]);
        $taggedCache->increment($key, $value);
        return $taggedCache->get($key);
    }

    /**
     * Decrement the value of an item in the cache.
     *
     * @param  string $key
     * @param  mixed $value
     * @return int|bool
     */
    public function decrement($key, $value = 1)
    {
        $taggedCache = $this->tags([]);
        $taggedCache->decrement($key, $value);
        return $taggedCache->get($key);
    }

    /**
     * Store an item in the cache indefinitely.
     *
     * @param  string $key
     * @param  mixed $value
     * @return bool
     */
    public function forever($key, $value)
    {
        return $this->tags([])->forever($key, $value);
    }

    /**
     * Remove an item from the cache.
     *
     * @param  string $key
     * @return bool
     */
    public function forget($key)
    {
        return $this->tags([])->forget($key);
    }

    /**
     * Remove all items from the cache.
     *
     * @return bool
     */
    public function flush()
    {
        return $this->tags([])->flush();
    }

    /**
     * Get the cache key prefix.
     *
     * @return string
     */
    public function getPrefix()
    {
        return $this->backend->getPrefix();
    }

    public function tags($names)
    {
        $tags = array_merge([$this->tag], is_array($names) ? $names : func_get_args());
        return $this->backend->tags(array_values(array_unique($tags)));
    }
}
