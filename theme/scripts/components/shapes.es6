var Component = require('./component')
var $ = window.jQuery

var spriteInserted = false

/**
 * Shapes component class
 *
 * - injects SVG sprite into body
 */
class Shapes extends Component {

	/**
	 * @param {HTMLElement} element
	 * @param {Object} data
	 */
	constructor(element, data) {
		super(element, data)

		this.supportsSVG = document.implementation.hasFeature("http://www.w3.org/TR/SVG11/feature#BasicStructure", "1.1")
		if(this.supportsSVG && !spriteInserted) this.injectSprite()
	}

	injectSprite() {
		spriteInserted = true

		$.get(this.data.url, (response, status) => {
			if(status == 'success') {
				$(document.body).prepend(response)
			} else {
				spriteInserted = false
				this.injectSprite()
			}
		}, 'text')
	}

}


module.exports = Shapes
