<?php
/**
 * tests for matching patterns
 * format is [pattern, test url, captures]
 * -------------------------------------------------------------------------
 **/

return [
    /**
     * static matching
     * -------------------------------------------------------------------------
     **/
    ['/', '/', []],
    ['/foo', '/foo', []],
    ['foo', 'foo', []],
    ['/foo/', '/foo/', []],
    ['/foo/bar', '/foo/bar', []],
    ['/foo/bar/', '/foo/bar/', []],
    ['/foo/bar/baz', '/foo/bar/baz', []],

    /**
     * generic pattern matching
     * -------------------------------------------------------------------------
     **/
    ['/{first}', '/foo', ['foo']], // names are just syntactic sugar - matches are passed positionally
    ['/foo/{first}', '/foo/bar', ['bar']], // they must start with a letter
    ['/foo/{some-name}/', '/foo/bar/', ['bar']], // hyphens are ok
    ['/foo/bar/{another_match}', '/foo/bar/baz', ['baz']], // as are underscores
    ['/foo/bar/{Match1}/', '/foo/bar/baz/', ['baz']], // and numbers
    ['/foo/{seg1}/{seg2}', '/foo/bar/baz', ['bar', 'baz']],


    /**
     * specific pattern matching
     * -------------------------------------------------------------------------
     **/
    ['/{a:[a-z]+}', '/foo', ['foo']],
    ['/foo/{b:\d+}', '/foo/100', ['100']],
    ['/foo/{b:\d{4}}', '/foo/1234', ['1234']],
    ['/foo/{y:\d{4}}-{m:\d{2}}-{d:\d{2}}', '/foo/2001-03-24', ['2001', '03', '24']],

    /**
     * optional segments
     * -------------------------------------------------------------------------
     **/
    ['/optional/{segment}?', '/optional/', []],
    ['/optional/{segment}?', '/optional/segment', ['segment']],
    ['/optional/?{segment}?', '/optional', []], // make the slash optional too, don't capture
    ['/optional/?{segment}?', '/optional/', []],
    ['/optional/?{segment}?', '/optional/segment', ['segment']],
    ['/optional{segment:.*}?', '/optional/segment/with/more/stuff', ['/segment/with/more/stuff']],

    /**
     * using regexes
     * -------------------------------------------------------------------------
     */
    ['/foo/(?i:bar)', '/foo/bAr', []], // case-insensitive matching
    ['/foo/bar/.*', '/foo/bar/baz/etc/etc', []], // throwaway matching of extra segments
    ['/foo/(\d{4})', '/foo/1234', ['1234']], // use regex syntax alone
    ['/foo/bar.*?', '/foo/bar', []], // make even the slash optional
    ['/foo/bar.*?', '/foo/bar/baz/etc/etc', []], // still matches all
    ['/foo/(?:bar|baz|etc)', '/foo/baz', []],
    ['/foo/(bar|baz|etc)', '/foo/etc', ['etc']],
    ['/foo/{bar}/20(?:13|14)/([a-z-]+)', '/foo/bar/2014/some-thing', ['bar', 'some-thing']],

    /**
     * relative paths
     * -------------------------------------------------------------------------
     */
    ['\.\./foo', '../foo', []], // need to escape . in relative paths
    ['bar/\.\./foo', 'bar/../foo', []],
    ['\./foo', './foo', []]
];
