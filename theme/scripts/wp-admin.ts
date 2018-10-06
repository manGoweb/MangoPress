import ExampleAdminPage from './adminComponents/ExampleAdminPage'
import ExampleMetabox from './adminComponents/ExampleMetabox'
import ExampleToolbarButton from './adminComponents/ExampleToolbarButton'
import PageRolesMetabox from './adminComponents/PageRolesMetabox'
import componentHandler from './componentHandler'
import Example from './components/Example'
import Shapes from './components/Shapes'
import './plugins'

componentHandler(
	[Shapes, Example, ExampleToolbarButton, PageRolesMetabox, ExampleAdminPage, ExampleMetabox],
	'initAdminComponents'
)
