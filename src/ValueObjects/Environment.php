<?php
/**
 * Environment.php
 *
 * @package   contextwp-sdk
 * @copyright Copyright (c) 2022, Ashley Gibson
 * @license   MIT
 */

namespace ContextWP\ValueObjects;

use ContextWP\Contracts\Arrayable;
use ContextWP\Helpers\EnvironmentTypeParser;
use ContextWP\Helpers\SourceHasher;
use ContextWP\Helpers\Str;
use ContextWP\SDK;
use ContextWP\Traits\Makeable;
use Exception;

/**
 * Information about this WordPress instance.
 */
class Environment implements Arrayable
{
    use Makeable;

    /**
     * Converts the environment into the array expected for the API request.
     *
     * @return array
     * @throws Exception
     */
    public function toArray(): array
    {
        return array_map(function ($value) {
            return Str::maxChars($value, 100);
        }, [
            'source_hash' => $this->getSourceHash(),
            'wp_version'  => $this->getBlogValue('version'),
            'php_version' => phpversion() ?: null,
            'locale'      => $this->getBlogValue('language'),
            'type'        => $this->getEnvironmentType(),
            'sdk_version' => SDK::getVersion(),
        ]);
    }

    /**
     * Retrieves the hash for this site.
     *
     * @return string
     * @throws Exception
     */
    protected function getSourceHash(): string
    {
        return (new SourceHasher())->getHash();
    }

    /**
     * Helper function to retrieve values from `get_bloginfo()` and ensure any "empty" values are `null`.
     *
     * @param  string  $key
     *
     * @return string|null
     */
    protected function getBlogValue(string $key): ?string
    {
        return get_bloginfo($key) ?: null;
    }

    /**
     * Retrieves this site's environment type.
     *
     * @since 1.0
     *
     * @return string
     */
    protected function getEnvironmentType(): string
    {
        return (new EnvironmentTypeParser())->parse();
    }
}
