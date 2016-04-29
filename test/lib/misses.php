<?php
/**
 * tests for non-matching patterns
 * format is [pattern, test url]
 * -------------------------------------------------------------------------
 **/

return [
    /**
     * static matching
     * -------------------------------------------------------------------------
     **/
    ['/', ''],
    ['/foo', 'foo'],
    ['/foo', '/foo/'],
    ['/foo/', '/foo'],
    ['/foo/bar/', '/foo/bar'],
    ['/foo/bas', '/foo/bar'],
    ['foo/bar/baz', '/foo/bar/baz'],

    /**
     * generic pattern matching
     * -------------------------------------------------------------------------
     **/
    ['{first}', '/foo'], // leading slashes
    ['/foo/{first}', '/bar/foo'], // match all segments
    ['/foo/{seg1}/bar/{seg2}', '/foo/bar/baz/etc'],


    /**
     * specific pattern matching
     * -------------------------------------------------------------------------
     **/
    ['/{a:[a-z]+}', '/foo1'],
    ['/foo/{b:\d+}', '/foo/10a0'],
    ['/foo/{b:\d{4}}', '/foo/12345'],
    ['/foo/{y:\d{4}}-{m:\d{2}}-{d:\d{2}}', '/foo/20a01-03-24'],

    /**
     * optional segments
     * -------------------------------------------------------------------------
     **/
    ['/optional/{segment}?', '/optional'], // missing slash
    ['/optional/{segment}?', '/optional/segment/'], // extra slash
    ['/optional/?{segment}?', '/optional/segment/'],
    ['/optional{segment}?', '/optional/segment'], // default match excludes /

    /**
     * using regexes
     * -------------------------------------------------------------------------
     */
    ['/foo/bar/.*', '/foo/bar'], // extra slash
    ['/foo/(?:bar|baz|etc)', '/foo/nope'],
    ['/foo/{bar}/20(?:13|14)/([a-z-]+)', '/foo/bar/2015/some-thing'],


    /**
     * relative paths
     * -------------------------------------------------------------------------
     */
    ['\.\./foo', './foo', []], // parent not current
    ['\./foo', 'b/foo', []] // only matches dot
];
