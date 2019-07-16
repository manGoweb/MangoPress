import { initializeComponents } from '@mangoweb/scripts-base'
import ExampleAdminPage from './adminComponents/ExampleAdminPage'
import ExampleMetabox from './adminComponents/ExampleMetabox'
import ExampleToolbarButton from './adminComponents/ExampleToolbarButton'
import PageRolesMetabox from './adminComponents/PageRolesMetabox'
import { Shapes } from '@mangoweb/scripts-base'
import './plugins'

initializeComponents(
	[Shapes, ExampleToolbarButton, PageRolesMetabox, ExampleAdminPage, ExampleMetabox],
	'initAdminComponents'
)
