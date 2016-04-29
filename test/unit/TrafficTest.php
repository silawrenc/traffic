<?php

use silawrenc\Traffic\Traffic;

class TrafficTest extends \PHPUnit_Framework_TestCase
{
    public function setUp()
    {
        $this->router = new Traffic;
        $this->flag = null;
    }

    public function testConstructor()
    {
        $this->assertInstanceOf(Traffic::class, $this->router);
    }

    public function testGet()
    {
        $this->assertInstanceOf(Traffic::class, $this->router->get('/foo/bar'));
    }

    public function testPost()
    {
        $this->assertInstanceOf(Traffic::class, $this->router->post('/foo/{bar}'));
    }

    public function testMatch()
    {
        $this->assertInstanceOf(Traffic::class, $this->router->add('*', '/foo/bar'));
    }

    public function testRouteTrue()
    {
        $this->router->get('/foo/{bar:[a-z]+}');

        $this->assertTrue($this->router->route('GET', '/foo/bar'));
    }

    public function testRouteNoMatchFalse()
    {
        $this->router->get('/foo/{bar:[1-9]+}');

        $this->assertFalse($this->router->route('GET', '/foo/bar'));
    }

    public function testMatchWildCardMatches()
    {
        $this->router->add('*', '/foo/bar');
        $this->assertTrue($this->router->route('OPTIONS', '/foo/bar'));
    }

    public function testRegexMethodMatches()
    {
        $this->router->add('(GET|POST)', '/foo/bar');
        $this->assertTrue($this->router->route('POST', '/foo/bar'));
    }

    public function testRegexMethodNoMatch()
    {
        $this->router->add('(GET|HEAD)', '/foo/bar');
        $this->assertFalse($this->router->route('POST', '/foo/bar'));
    }

    public function testOnlyFirstMatchingRouteInvoked()
    {
        $this->router->get('/foo/{first:[a-z]+}', $this->flag('first'))
                     ->get('/foo/{second}', $this->flag('second'))
                     ->route('GET', '/foo/bar');

        $this->assertEquals('first', $this->flag);
    }

    public function testAllHandlersInvoked()
    {
        $this->router->get('/foo/bar', $this->flag(1), $this->flag(2), $this->flag(3))
                     ->route('GET', '/foo/bar');

        $this->assertEquals(3, $this->flag);
    }

    public function testHandlerInvocationEndsOnStrictFalse()
    {
        $this->router->get('/foo/bar', $this->flag(1), function () {
            $this->flag = 2;
            return false;
        }, $this->flag(3));
        $this->router->route('GET', '/foo/bar');

        $this->assertEquals(2, $this->flag);
    }

    /**
     * test matching
     * -------------------------------------------------------------------------
     */

    /**
     * @dataProvider matchingData
     */
    public function testGets($pattern, $uri, $captures)
    {
        $this->router->get($pattern, $this->flag())
                     ->route('GET', $uri);

        $this->assertFlag($captures);
    }

    /**
     * @dataProvider matchingData
     */
    public function testPosts($pattern, $uri, $captures)
    {
        $this->router->post($pattern, $this->flag())
                     ->route('POST', $uri);

        $this->assertFlag($captures);
    }

    /**
     * @dataProvider matchingData
     */
    public function testMatches($pattern, $uri, $captures)
    {
        array_unshift($captures, 'GET');
        $this->router->add('*', $pattern, $this->flag())
                     ->route('GET', $uri);

        $this->assertFlag($captures);
    }

    /**
     * @dataProvider nonMatchingData
     */
    public function testGetsNoMatch($pattern, $uri)
    {
        $this->router->get($pattern, $this->flag())
                     ->route('GET', $uri);

        $this->assertFlag(null);
    }

    /**
     * @dataProvider nonMatchingData
     */
    public function testPostsNoMatch($pattern, $uri)
    {
        $this->router->post($pattern, $this->flag())
                     ->route('POST', $uri);

        $this->assertFlag(null);
    }

    /**
     * @dataProvider nonMatchingData
     */
    public function testMatchesNoMatch($pattern, $uri)
    {
        $this->router->add('*', $pattern, $this->flag())
                     ->route('GET', $uri);

        $this->assertFlag(null);
    }

    public function matchingData()
    {
        return require(__DIR__ .'/../lib/matches.php');
    }

    public function nonMatchingData()
    {
        return require(__DIR__ .'/../lib/misses.php');
    }

    protected function flag($v = null)
    {
        return function (...$args) use ($v) {
            $this->flag = $v ?: $args;
        };
    }

    protected function assertFlag($expected)
    {
        $this->assertEquals($expected, $this->flag);
    }
}
