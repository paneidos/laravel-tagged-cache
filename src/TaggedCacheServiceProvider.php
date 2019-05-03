<?php

namespace Paneidos\LaravelTaggedCache;

use Illuminate\Cache\CacheManager;
use Illuminate\Cache\Repository;
use Illuminate\Cache\TaggableStore;
use Illuminate\Support\ServiceProvider;
use InvalidArgumentException;

class TaggedCacheServiceProvider extends ServiceProvider
{
    public function register()
    {
        //
    }

    public function boot()
    {
        /** @var CacheManager $cacheManager */
        $cacheManager = $this->app['cache'];
        $cacheManager->extend('tagged', function ($app, $options) {
            $backend = $options['backend'] ?? null;
            $tag = $options['tag'] ?? null;

            if (!$backend) {
                throw new InvalidArgumentException("No backend cache store specified");
            }

            if (!$tag) {
                throw new InvalidArgumentException("No tag specified");
            }

            $backendStore = $app['cache']->store($backend)->getStore();
            if (!($backendStore instanceof TaggableStore)) {
                throw new InvalidArgumentException("Backend cache store must be a taggable store");
            }

            $taggedStore = new TaggedStore($backendStore, $tag);
            return new Repository($taggedStore);
        });
    }
}
