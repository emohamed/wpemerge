<?php

namespace WPEmerge\Framework;

use WPEmerge\Support\Facade;

/**
 * Provide access to the framework instance
 *
 * @codeCoverageIgnore
 */
class FrameworkFacade extends Facade {
    protected static function getFacadeAccessor() {
        return WP_EMERGE_FRAMEWORK_KEY;
    }
}
