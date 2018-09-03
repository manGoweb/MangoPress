import React from 'react'
import DOM from 'react-dom'
import { NamedComponent } from '../components/Component'
import Component from './../components/Component'

export default (CustomComponent: Constructor<React.Component> & NamedComponent) => {
	return class AdminComponent extends Component<any> {
		static componentName = CustomComponent.componentName

		init() {
			DOM.render(React.createElement(CustomComponent, this.data), this.el)
		}
	}
}
