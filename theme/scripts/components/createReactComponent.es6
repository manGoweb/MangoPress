import React from 'react'
import DOM from 'react-dom'
const $ = window.jQuery
const Component = require('./component')

export default function createReactComponent(Component) {
	return class ReactComponent extends Component {

		constructor(element, data) {
			super(element, data)

			if(data.unwrapped) {
				var $el = $(element)
				$el.unwrap()
				$el.unwrap()
			} else if (data.nopadding) {
				var $el = $(element)
				$el.unwrap()
			}

			DOM.render(<Component {...data} />, element)
		}

	}
}

