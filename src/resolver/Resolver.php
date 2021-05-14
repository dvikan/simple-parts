<?php declare(strict_types=1);

namespace dvikan\SimpleParts;

use Closure;

final class Resolver
{
    public function resolve($handler)
    {
        if (is_array($handler)) {

            if (is_string($handler[0])) {

                if (! class_exists($handler[0])) {
                    throw new SimpleException(sprintf('Class not found: "%s"', $handler[0]));
                }

                if (! method_exists($handler[0], $handler[1])) {
                    throw new SimpleException(sprintf('Method not found: "%s"', $handler[1]));
                }

                return $handler;
            }

            if (is_callable($handler)) {
                return $handler;
            }
        }

        if ($handler instanceof Closure || is_callable($handler)) {
            return [$handler, '__invoke'];
        }

        if (is_string($handler)) {
            if (method_exists($handler, '__invoke')) {
                return [$handler, '__invoke'];
            }
        }

        throw new SimpleException('Unable to resolve handler');
    }
}