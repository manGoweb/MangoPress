import React from "react"
import DOM from "react-dom"
const $ = window.jQuery
import Component from "../components/Component"

export default function createReactComponent(CustomComponent) {
	return class ReactComponent extends Component {
		constructor(element, data) {
			super(element, data)

			if (data.unwrapped) {
				var $el = $(element)
				$el.unwrap()
				$el.unwrap()
			} else if (data.nopadding) {
				var $el = $(element)
				$el.unwrap()
			}

			DOM.render(<CustomComponent {...data} />, element)
		}
	}
}
