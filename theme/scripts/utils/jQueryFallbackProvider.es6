const inject = require('./inject')


module.exports = (fallbackUrl, resolveCallback, rejectCallback) => {

	// jQuery is defined
	if (window.jQuery) {
		resolveCallback(window.jQuery)
		return
	}

	// jQuery is undefined and without fallback
	if (!fallbackUrl) {
		rejectCallback()
		return
	}

	//jQuery is undefined with fallback available
	inject(
		fallbackUrl,
		() => {
			resolveCallback(window.jQuery)
		},
		rejectCallback
	)

}
