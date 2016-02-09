var head = document.head || document.getElementsByTagName('head')[0]

/**
 * Injects script into the current page
 *
 * @param  {string|Object}  scripts src or Object script attributes with optional 'content' as inline javascript
 * @param  {Function}       load callback
 * 
 * @author Matěj Šimek <email@matejsimek.com> (http://www.matejsimek.com)
 */
module.exports = function inject(options, callback) {
	var script = document.createElement('script')

	script.addEventListener('load', callback)
	script.type = 'text/javascript'

	// options is an URL string
	if(typeof options == 'string') {
		script.src = options
		script.async = true
	}

	// options is an object with script attributes
	// key 'content' is alias for inline script content
	else if(typeof options == 'object') {

		for(key in options) {
			if(!options.hasOwnProperty(key)) continue
			var value = options[key]

			if(key == 'content') {
				script.appendChild(document.createTextNode(value))
			} else {
				script[key] = value
			}
		})

	}

	head.appendChild(script)
}
