<?php

namespace Paneidos\LaravelTaggedCache\Tests;

use Illuminate\Cache\ArrayStore;
use Paneidos\LaravelTaggedCache\TaggedStore;
use PHPUnit\Framework\TestCase;

class TaggedStoreTest extends TestCase
{
    public function testItSeparatesStoresByTagOnSameBackend()
    {
        $backend = new ArrayStore();

        $store1 = new TaggedStore($backend, 'tag1');
        $store2 = new TaggedStore($backend, 'tag2');

        $store1->put('test', 'foo', 100);
        $store2->forever('test', 'bar');

        // Verify stores
        $this->assertEquals('foo', $store1->get('test'));
        $this->assertEquals('bar', $store2->get('test'));

        // Verify backend
        $this->assertEquals('foo', $backend->tags('tag1')->get('test'));
        $this->assertEquals('bar', $backend->tags('tag2')->get('test'));
    }

    public function testItAddsTheTagWhenUsingMany()
    {
        $backend = new ArrayStore();

        $store1 = new TaggedStore($backend, 'tag1');
        $store2 = new TaggedStore($backend, 'tag2');

        $store1->putMany([
            'test1' => 'foo',
            'test2' => 'bar',
        ], 100);
        $store2->putMany([
            'test1' => 'bar',
            'test2' => 'baz',
        ], 100);

        // Verify stores
        $this->assertEquals([
            'test1' => 'foo',
            'test2' => 'bar',
        ], $store1->many(['test1', 'test2']));

        $this->assertEquals([
            'test1' => 'bar',
            'test2' => 'baz',
        ], $store2->many(['test1', 'test2']));

        // Verify backend
        $this->assertEquals([
            'test1' => 'foo',
            'test2' => 'bar',
        ], $backend->tags('tag1')->many(['test1', 'test2']));

        $this->assertEquals([
            'test1' => 'bar',
            'test2' => 'baz',
        ], $backend->tags('tag2')->many(['test1', 'test2']));
    }

    public function testIncrementAndDecrementIsSeparated()
    {
        $backend = new ArrayStore();

        $store1 = new TaggedStore($backend, 'tag1');
        $store2 = new TaggedStore($backend, 'tag2');

        $store1->forever('foo', 25);
        $store1->increment('foo', 1);
        $store1->increment('foo', 2);
        $store1->decrement('foo', 4);

        $store2->forever('foo', 75);
        $store2->increment('foo', 10);
        $store2->increment('foo', 20);
        $store2->decrement('foo', 40);

        // Verify stores
        $this->assertEquals(24, $store1->get('foo'));
        $this->assertEquals(65, $store2->get('foo'));

        // Verify stores
        $this->assertEquals(24, $backend->tags('tag1')->get('foo'));
        $this->assertEquals(65, $backend->tags('tag2')->get('foo'));
    }

    public function testForgetDoesNotAffectOtherStores()
    {

        $backend = new ArrayStore();

        $store1 = new TaggedStore($backend, 'tag1');
        $store2 = new TaggedStore($backend, 'tag2');

        $store1->forever('foo', 25);
        $store2->forever('foo', 75);

        $store1->forget('foo');

        // Verify stores
        $this->assertNull($store1->get('foo'));
        $this->assertEquals(75, $store2->get('foo'));

        // Verify backend
        $this->assertNull($backend->tags('tag1')->get('foo'));
        $this->assertEquals(75, $backend->tags('tag2')->get('foo'));
    }

    public function testFlushDoesNotAffectOtherStores()
    {
        $backend = new ArrayStore();

        $store1 = new TaggedStore($backend, 'tag1');
        $store2 = new TaggedStore($backend, 'tag2');

        $store1->forever('foo', 25);
        $store2->forever('foo', 75);

        $store1->flush();

        // Verify stores
        $this->assertNull($store1->get('foo'));
        $this->assertEquals(75, $store2->get('foo'));

        // Verify backend
        $this->assertNull($backend->tags('tag1')->get('foo'));
        $this->assertEquals(75, $backend->tags('tag2')->get('foo'));
    }

    public function testGetPrefixDelegatesToBackend()
    {
        $backend = new class extends ArrayStore {
            public function getPrefix()
            {
                return 'prefix:';
            }
        };

        $store1 = new TaggedStore($backend, 'tag1');
        $store2 = new TaggedStore($backend, 'tag2');

        $this->assertEquals('prefix:', $store1->getPrefix());
        $this->assertEquals('prefix:', $store2->getPrefix());
    }

    public function testItDelegatesUnknownMethodsToBackend()
    {
        $backend = new class extends ArrayStore {
            public function foo()
            {
                return 'bar';
            }
        };

        $store1 = new TaggedStore($backend, 'tag1');
        $store2 = new TaggedStore($backend, 'tag2');

        /** @noinspection PhpUndefinedMethodInspection */
        $this->assertEquals('bar', $store1->foo());
        /** @noinspection PhpUndefinedMethodInspection */
        $this->assertEquals('bar', $store2->foo());
    }

    public function testItExposesTheBackend()
    {
        $backend = new ArrayStore();

        $store1 = new TaggedStore($backend, 'tag1');
        $store2 = new TaggedStore($backend, 'tag2');

        $this->assertSame($backend, $store1->getBackend());
        $this->assertSame($backend, $store2->getBackend());
    }
}
