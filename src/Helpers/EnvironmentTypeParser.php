<?php
/**
 * EnvironmentTypeParser.php
 *
 * @package   contextwp-sdk
 * @copyright Copyright (c) 2022, Ashley Gibson
 * @license   MIT
 */

namespace ContextWP\Helpers;

/**
 * Parses the current WordPress environment.
 */
class EnvironmentTypeParser
{
    const Local = 'local';
    const Development = 'development';
    const Staging = 'staging';
    const Production = 'production';

    /** @var string[] */
    protected $localExtensions = [
        '.test', '.testing', '.local', '.localhost', '.localdomain', '.develop', '.example', '.invalid', '.dev'
    ];

    /**
     * Parses the environment type.
     * If the WordPress-defined environment returns `production`, then we actually do further checks on it, as
     * that's the default value and many people may not have set it properly.
     *
     * @since 1.0
     *
     * @return string
     */
    public function parse(): string
    {
        $wpType = $this->getWpDefinedType();
        if ($wpType && $wpType !== static::Production) {
            return $wpType;
        }

        return $this->guessTypeFromDomain();
    }

    /**
     * Gets the type defined by WordPress, if it's available.
     *
     * @since 1.0
     *
     * @return string|null
     */
    protected function getWpDefinedType(): ?string
    {
        return function_exists('wp_get_environment_type') ? (string) wp_get_environment_type() : null;
    }

    /**
     * Guesses the type from the domain name.
     *
     * @return string
     */
    protected function guessTypeFromDomain(): string
    {
        $host = $this->getHost();
        if (! is_string($host)) {
            return static::Production;
        }

        if ($this->isLocalSite($host)) {
            return static::Local;
        } elseif ($this->isStagingSite($host)) {
            return static::Staging;
        } else {
            return static::Production;
        }
    }

    /**
     * Retrieves the host.
     *
     * @since 1.0
     *
     * @return string|null
     */
    protected function getHost(): ?string
    {
        $host = $_SERVER['HTTP_HOST'] ?? null;

        return is_string($host) ? $host : null;
    }

    /**
     * Determines if this is a local site.
     *
     * @since 1.0
     *
     * @param  string  $host
     *
     * @return bool
     */
    protected function isLocalSite(string $host): bool
    {
        return ! strpos($host, '.') || in_array(strrchr($host, '.'), $this->localExtensions);
    }

    /**
     * Determines if this is a staging site. We assume it is if it's on a `staging.` subdomain.
     *
     * @since 1.0
     *
     * @param  string  $host
     *
     * @return bool
     */
    protected function isStagingSite(string $host): bool
    {
        return in_array(strstr($host, '.', true), ['staging']);
    }
}
