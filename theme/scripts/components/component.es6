const $ = window.jQuery
const eventSplitter = /^(\S+)\s*(.*)$/

/**
 * Abstract component class
 *
 * - use for creating own components with standartized API
 * - dependes on jQuery.on() for attaching listeners (can be replaced with Zepto, Gator, etc.)
 *
 * @abstract
 * @class
 * @module component
 *
 * @author Matěj Šimek <email@matejsimek.com> (http://www.matejsimek.com)
 */
module.exports = class Component {

	/**
	 * @constructor
	 * @param {HTMLElement} element
	 * @param {object} data
	 */
	constructor(element, data = {}) {
		/** @type {HTMLElement} */
		this.el = element
		/** @type {jQuery} */
		this.$el = $(element)
		/** @type {object|null} */
		this.data = data

		this.attachListeners()
	}

	/**
	 * Component listeners
	 *
	 * Format:
	 *  - "type": "handlerName"
	 *  - "type<space>.selector": "handlerName"
	 */
	get listeners() {
		return {
			// 'click .example-child': 'handleClick',
		}
	}

	/**
	 * Assign event handlers from this.listeners property
	 */
	attachListeners() {
		const listeners = this.listeners

		for (const event in listeners) {
			let type = event.trim()
			let selector = false
			const callback = this[listeners[event]]

			const split = event.match(eventSplitter)
			if (split) {
				[, type, selector] = split
			}

			/**
			 * Handler called when an event occurred
			 *
			 * @callback Component~eventHandler
			 * @param {object} event - an event object
			 * @param {Object} data - optional data passed with event
			 * @this {Element} - an element that caught the event
			 */
			let listener = $.proxy(callback, this)

			if (selector) {
				this.$el.on(type, selector, listener)
			} else {
				this.$el.on(type, listener)
			}
		}
	}

	/**
	 * Remove event listeners
	 */
	detachListeners() {
		this.$el.off()
	}

	/**
	 * Gracefully destroy current instance properties
	 */
	destroy() {
		this.detachListeners()

		for (const prop in this) {
			this[prop] = null
		}
	}

	/**
	 * Returns a child
	 * @param  {string} selector - CSS selector
	 * @return {jQuery|null}
	 */
	child(selector) {
		const $result = this.$el.find(selector)

		if (!$result.length) {
			return null
		}
		return $result.eq(0)
	}

}
