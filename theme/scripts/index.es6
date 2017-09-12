import "./plugins"
import componentsHandler from "./utils/initComponentsHandler"

import example from "./components/Example"
import shapes from "./components/Shapes"

componentsHandler(
	{
		example,
		shapes,
	},
	"initComponents"
)
