const Component = require('./component')

/**
 * Example component class
 *
 * - all DOM operations must be executed after creating an instance (in constructor)
 * - when defining own constructor, don't forget to call super(element, data)
 * - DOM event listeners are in Backbone style
 *
 */
module.exports = class Example extends Component {

	constructor(el, data) {
		super(el, data)
	}

	get listeners() {
		return {
			'click .example-child': 'handleClick',
		}
	}

	handleClick(e, data) {
		e.preventDefault()
		alert(this.data)
	}

}
