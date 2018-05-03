import "./plugins"
import componentsHandler from "./utils/initComponentsHandler"

import page_roles from "./components/PageRolesMetabox"
import example_admin_page from "./components/ExampleAdminPage"
import example from "./components/Example"
import example_metabox from "./components/ExampleMetabox"

componentsHandler(
	{
		page_roles,
		example,
		example_admin_page,
		example_metabox,
	},
	"initAdminComponentcs"
)
