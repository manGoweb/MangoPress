<?php

namespace Mangoweb;

/**
 * @return bool
 */
function isSharedHost() {
	return strncmp(gethostname(), 'shared-', 7) === 0;
}

/**
 * @return bool
 */
function isBetaHost() {
	return strncmp(gethostname(), 'shared-beta', 11) === 0;
}

/**
 * @example 'shared-prod' for hostname 'shared-prod--i-i-78258ac5'
 * @return string
 */
function getReplicationGroupName() {
	return explode('--', gethostname())[0];
}
