const componentsHandler = require('./componentsHandler')

require('./plugins')
componentsHandler({
	'example': require('./components/example'),
	'page_roles': require('./components/PageRolesMetabox'),
}, 'initAdminComponents')
