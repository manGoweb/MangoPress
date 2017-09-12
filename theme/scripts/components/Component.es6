const $ = window.jQuery
const eventSplitter = /^(\S+)\s*(.*)$/

export default class Component {
	constructor(element, data = {}) {
		this.el = element
		this.$el = $(element)
		this.data = data

		this.attachListeners()
	}

	get listeners() {
		return {
			// 'click .example-child': 'handleClick',
		}
	}

	attachListeners() {
		const listeners = this.listeners

		for (const event in listeners) {
			let type = event.trim()
			let selector = false
			const callback = this[listeners[event]]

			const split = event.match(eventSplitter)
			if (split) {
				;[, type, selector] = split
			}

			let listener = $.proxy(callback, this)

			if (selector) {
				this.$el.on(type, selector, listener)
			} else {
				this.$el.on(type, listener)
			}
		}
	}

	detachListeners() {
		this.$el.off()
	}

	destroy() {
		this.detachListeners()

		for (const prop in this) {
			this[prop] = null
		}
	}

	child(selector) {
		const $result = this.$el.find(selector)

		if (!$result.length) {
			return null
		}
		return $result.eq(0)
	}
}
