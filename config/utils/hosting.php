<?php

namespace Mangoweb;

/**
 * @return bool
 */
function isSharedHost()
{
	return 0 === strncmp(gethostname(), 'shared-', 7);
}

/**
 * @return bool
 */
function isBetaHost()
{
	return 0 === strncmp(gethostname(), 'shared-beta', 11);
}

/**
 * @example 'shared-prod' for hostname 'shared-prod--i-i-78258ac5'
 *
 * @return string
 */
function getReplicationGroupName()
{
	return explode('--', gethostname())[0];
}
