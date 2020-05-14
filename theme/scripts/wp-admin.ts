import { initializeComponents } from '@mangoweb/scripts-base'
import ExampleAdminPage from './adminComponents/ExampleAdminPage'
import ExampleMetabox from './adminComponents/ExampleMetabox'
import ExampleToolbarButton from './adminComponents/ExampleToolbarButton'
import PageRolesMetabox from './adminComponents/PageRolesMetabox'
import './plugins'

initializeComponents(
	[ExampleToolbarButton, PageRolesMetabox, ExampleAdminPage, ExampleMetabox],
	'initAdminComponents'
)
