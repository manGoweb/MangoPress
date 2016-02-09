var $ = window.jQuery
var eventSplitter = /^(\S+)\s*(.*)$/

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
class Component {

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
	 * 	- "type": "handlerName"
	 * 	- "type<space>.selector": "handlerName"
	 *
	 * @param {Component~eventHandler} event handler which is a component method
	 */
	get listeners() {
		return {
			// 'click .example-child': 'handleClick'
		}
	}

	/**
	 * Assign event handlers from this.listeners property
	 */
	attachListeners() {
		let self = this
		let listeners = this.listeners

		for(let event in listeners) {
			let type = event.trim()
			let selector = false
			let callback = this[listeners[event]]

			let split = event.match(eventSplitter)
			if(split) {
				type = split[1]
				selector = split[2]
			}

			/**
			 * Handler called when an event occured
			 *
			 * @callback Component~eventHandler
			 * @param {object} event - an event object
			 * @param {Component} self - currrent instance
			 * @param {Object} data - optional data passed with event
			 * @this {Element} - an element that catched the event
			 */
			let listener = function(e, data) {
				callback(e, self, data)
			}

			if(selector){
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

		for (let prop in this) {
			this[prop] = null
		}
	}

	/**
	 * Returns a child
	 * @param  {string} CSS selector
	 * @return {jQuery|null}
	 */
	child(selector) {
		var result = this.$el.find(selector)
		if(!result.length) return null
		else return result.eq(0)
	}

}

module.exports = Component
