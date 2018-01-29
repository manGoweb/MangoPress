import "./plugins"
import componentsHandler from "./utils/initComponentsHandler"

import page_roles from "./components/PageRolesMetabox.jsx"
import example_admin_page from "./components/ExampleAdminPage.jsx"
import example from "./components/Example"
import example_metabox from "./components/ExampleMetabox.jsx"

componentsHandler(
	{
		page_roles,
		example,
		example_admin_page,
		example_metabox,
	},
	"initAdminComponents"
)
