import { Component } from '@mangoweb/scripts-base'
import React from 'react'
import DOM from 'react-dom'

export default function fromReactComponent<Props>(
	CustomComponent: React.ComponentClass<Props, any> | React.FunctionComponent<Props>,
	componentName: string
) {
	return class ReactComponent extends Component<Props, HTMLDivElement> {
		public static componentName = componentName

		public init() {
			DOM.render(React.createElement(CustomComponent, this.props), this.el)
		}
	}
}
