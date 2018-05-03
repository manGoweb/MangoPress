import Component from "./Component"
const $ = window.jQuery

export default class Shapes extends Component {
	constructor(element, data) {
		super(element, data)

		this.supportsSVG = document.implementation.hasFeature(
			"http://www.w3.org/TR/SVG11/feature#BasicStructure",
			"1.1"
		)

		if (this.supportsSVG) {
			this.injectSprite()
		}
	}

	injectSprite() {
		$.get(
			this.data.url,
			(response, status) => {
				if (status === "success") {
					$(document.body).prepend(response)
				} else {
					setTimeout(() => this.injectSprite(), 1000 * 10)
				}
			},
			"text"
		)
	}
}
