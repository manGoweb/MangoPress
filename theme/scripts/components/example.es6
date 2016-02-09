var Component = require('./component')

/**
 * Example component class
 *
 * - all DOM operations must be executed after creating an instance (in constructor)
 * - when defining own constructor, don't forget to call super(element, data)
 * - DOM event listeners are in Backbone style
 *
 */
class Example extends Component {

	get listeners() {
		return {
			'click .example-child': 'handleClick'
		}
	}

	handleClick(e, self) {
		e.preventDefault()
		alert(self.data)
	}

}

module.exports = Example