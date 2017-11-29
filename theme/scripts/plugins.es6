//
// Tiny project dependencies like polyfills and environment setup
//


// Avoid `console` errors in browsers that lack a console.
//
(() => {
	let method
	const noop = () => {}
	const methods = [
		'assert', 'clear', 'count', 'debug', 'dir', 'dirxml', 'error',
		'exception', 'group', 'groupCollapsed', 'groupEnd', 'info', 'log',
		'markTimeline', 'profile', 'profileEnd', 'table', 'time', 'timeEnd',
		'timeStamp', 'trace', 'warn',
	]
	let length = methods.length
	const console = (window.console = window.console || {})

	while (length--) {
		method = methods[length]
		// Only stub undefined methods.
		if (!console[method]) {
			console[method] = noop
		}
	}
})()
