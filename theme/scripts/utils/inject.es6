const head = document.head || document.getElementsByTagName('head')[0]

/**
 * Injects script into the current page
 *
 * @param  {string|Object} options      the script's src or Object script attributes with optional 'content' as inline javascript
 * @param  {Function} successCallback   success callback
 * @param  {Function} failCallback      failed callback
 *
 * @author Matěj Šimek <email@matejsimek.com> (http://www.matejsimek.com)
 */
module.exports = (options, successCallback, failCallback) => {
	const script = document.createElement('script')

	script.addEventListener('load', successCallback)
	script.addEventListener('error', failCallback)
	script.type = 'text/javascript'

	// options is an URL string
	if (typeof options === 'string') {
		script.src = options
		script.async = true
	}

	// options is an object with script attributes
	// key 'content' is alias for inline script content
	else if (typeof options === 'object') {

		for (const key in options) {
			if (!options.hasOwnProperty(key)) {
				continue
			}
			const value = options[key]

			if (key === 'content') {
				script.appendChild(document.createTextNode(value))
			} else {
				script[key] = value
			}
		}

	}

	head.appendChild(script)
}
