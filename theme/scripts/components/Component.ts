import matchesSelector from '../utils/matchesSelector'

export interface NamedComponent {
	componentName: string
}

export type ComponentConstructor<D> = NamedComponent &
	(new (element: HTMLElement, data: D) => Component<D>)

export class ComponentInitializationError extends Error {}

export default class Component<D> {
	protected readonly el: HTMLElement
	protected readonly data: D

	getListeners = (): EventListeners => []

	constructor(element: HTMLElement, data: D) {
		this.el = element
		this.data = data
	}

	setup() {
		this.attachListeners()

		this.init()
	}

	init() {}

	private attachListeners() {
		const listeners = this.getListeners()

		for (let i = 0, listenersCount = listeners.length; i < listenersCount; i++) {
			const listenersSpec = listeners[i]

			if (listenersSpec.length === 2) {
				// EventListenerSpec
				const [type, callback] = listenersSpec

				this.el.addEventListener(type, callback.bind(this), false)
			} else {
				// DelegateEventListenerSpec
				const [type, delegateSelector, callback] = listenersSpec

				this.el.addEventListener(
					type,
					(e: Event) => {
						let target = e.target

						while (target && target instanceof HTMLElement && target !== this.el) {
							if (matchesSelector(target, delegateSelector)) {
								const delegateEvent: any = e
								delegateEvent.delegateTarget = target

								return callback.call(this, delegateEvent)
							}

							target = target.parentElement
						}
					},
					false
				)
			}
		}
	}
}
