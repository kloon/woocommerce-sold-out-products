<?php
/**
 * WordPress Test Configuration.
 *
 * This file is used when running tests locally with PHPUnit.
 * For wp-env, the environment provides its own configuration.
 *
 * @package Kloon\WCSoldOutProducts\Tests
 */

// Path to WordPress codebase.
define( 'ABSPATH', dirname( __DIR__, 4 ) . '/wordpress/' );

// Test with WordPress debug mode.
define( 'WP_DEBUG', true );

// Database settings for local testing.
// These are overridden when using wp-env.
define( 'DB_NAME', getenv( 'WP_DB_NAME' ) ?: 'wordpress_test' );
define( 'DB_USER', getenv( 'WP_DB_USER' ) ?: 'root' );
define( 'DB_PASSWORD', getenv( 'WP_DB_PASSWORD' ) ?: '' );
define( 'DB_HOST', getenv( 'WP_DB_HOST' ) ?: 'localhost' );
define( 'DB_CHARSET', 'utf8' );
define( 'DB_COLLATE', '' );

// WordPress test configuration.
$table_prefix = 'wptests_'; // phpcs:ignore WordPress.WP.GlobalVariablesOverride.Prohibited

define( 'WP_TESTS_DOMAIN', 'example.org' );
define( 'WP_TESTS_EMAIL', 'admin@example.org' );
define( 'WP_TESTS_TITLE', 'Test Blog' );

define( 'WP_PHP_BINARY', 'php' );

define( 'WPLANG', '' );
