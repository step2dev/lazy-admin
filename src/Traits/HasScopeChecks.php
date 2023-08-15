<?php

namespace Step2dev\LazyAdmin\Traits;

use Illuminate\Support\Str;

trait HasScopeChecks
{
    protected function isCheckScopeMethod(string $method): bool
    {
        return $this->startWithIsHas($method);
    }

    protected function getOriginalScopeMethodName(string $checkMethodName): string
    {
        return $this->removeIsHasPrefix($checkMethodName);
    }

    protected function forwardCallTo($object, string $method, $parameters)
    {
        if ($this->isCheckScopeMethod($method)) {
            $originalScopeMethodName = $this->getOriginalScopeMethodName($method);
            $methodExists = $this->isScopeMethodExists("scope{$originalScopeMethodName}");
            if ($methodExists) {
                $queryString = $this->generateQuery($this->getKeyName(), $this->getKey());
                $builder = $this->constructBuilderWithOriginalScopeMethod($queryString, $originalScopeMethodName,
                    $parameters);

                return $builder->exists();
            }
        }

        return parent::forwardCallTo($object, $method, $parameters);
    }

    protected function startWithIsHas(string $method): bool
    {
        return Str::startsWith($method, 'is') || Str::startsWith($method, 'has');
    }

    protected function removeIsHasPrefix(string $checkMethodName): string
    {
        return preg_replace('/^(is|has)(.*)$/m', '$2', $checkMethodName);
    }

    protected function isScopeMethodExists(string $methodName): bool
    {
        return method_exists($this, $methodName);
    }

    protected function generateQuery(string $keyName, string $key): string
    {
        return $this->newQuery()->where($keyName, $key);
    }

    protected function constructBuilderWithOriginalScopeMethod(
        $query,
        string $originalScopeMethodName,
        array $parameters
    ) {
        return call_user_func_array([$query, $originalScopeMethodName], $parameters);
    }
}
