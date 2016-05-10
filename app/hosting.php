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
