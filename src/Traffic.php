<?php

namespace silawrenc\Traffic;

class Traffic {

    protected $routes = [];
    protected $compiled = null;

    public function route(string $method, string $uri): bool
    {
        $req = $method . ' ' . $uri;

        if ($this->compiled === null) {
            $this->compile();
        }

        foreach ($this->compiled as $regex => $routes) {
            if (preg_match($regex, $req) === 1) {
                preg_replace_callback_array($routes, $req, 1);
                return true;
            }
        }
        return false;
    }

    public function get(string $pattern, callable ...$handlers): self
    {
        return $this->add('GET', $pattern, ...$handlers);
    }

    public function post(string $pattern, callable ...$handlers): self
    {
        return $this->add('POST', $pattern, ...$handlers);
    }

    public function add(string $method, string $pattern, callable ...$handlers): self
    {
        $regex = $this->parse($method, $pattern);
        $handler = $this->wrap($handlers);
        $this->routes[$regex] = $handler;
        return $this;
    }

    protected function parse(string $method, string $pattern) {
        $regex = preg_replace(
            [
                '~^\* ~', // handle wildcard method option
                '~ \{ [A-z][A-z0-9_-]* : ([^{}]*(?:\{(?-1)\}[^{}]*)*) \} ~x', // format {year:\d{4}}
                '~{[A-z][A-z1-9\-_]*}~' // format {name}
            ],
            [
                '([A-Z]+) ', // match any METHOD
                '($1)', // (\d{4})
                '([^/]+)' // match anything up to next /
            ],
            $method . ' ' . $pattern
        );
        return '~^' . $regex . '$~';
    }

    protected function wrap(array $handlers)
    {
        return function ($matches) use ($handlers) {
            array_shift($matches); // drop the full match
            foreach ($handlers as $handler) {
                if ($handler(...$matches) === false) {
                    return;
                };
            }
        };
    }

    protected function compile()
    {
        $chunk = max(20, floor(sqrt(count($this->routes))));
        foreach (array_chunk($this->routes, $chunk, true) as $routes) {
            $keys = array_map(function ($pattern) { return substr($pattern, 1, -2); }, array_keys($routes));
            $this->compiled['~^(?|' . implode('|', $keys) . ')$~'] = $routes;
        }
    }
}
