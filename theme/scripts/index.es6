//require('./utils/swRegister')

const jQueryFallbackProvider = require('./utils/jQueryFallbackProvider')
const componentsHandler = require('./componentsHandler')


const onJQueryAvailable = ($) => {
	require('./plugins')
	componentsHandler({
		'example': require('./components/example'),
		'shapes': require('./components/shapes'),
	})
}

const onJQueryMissing = () => {
	console.log('jQuery dependency is missing. This page might not work correctly. Please try again later.')
}


jQueryFallbackProvider(
	'/node_modules/jquery/dist/jquery.min.js',
	onJQueryAvailable,
	onJQueryMissing
)
