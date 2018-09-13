type NonBubblingEventType =
	| 'abort'
	| 'blur'
	| 'error'
	| 'focus'
	| 'load'
	| 'loadend'
	| 'loadstart'
	| 'progress'
	| 'scroll'

type BubblingEventType = Exclude<keyof HTMLElementEventMap, NonBubblingEventType>

type DelegateEvent<E extends BubblingEventType> = HTMLElementEventMap[E] & {
	delegateTarget: HTMLElement
}

type DelegateEventListenerSpec<E extends BubblingEventType> = [
	E,
	string,
	(event: DelegateEvent<E>) => void
]

type EventListenerSpec<E extends keyof HTMLElementEventMap> = [
	E,
	(event: HTMLElementEventMap[E]) => void
]

type EventListeners = Array<
	| { [E in keyof HTMLElementEventMap]: EventListenerSpec<E> }[keyof HTMLElementEventMap]
	| { [E in BubblingEventType]: DelegateEventListenerSpec<E> }[BubblingEventType]
>

type Constructor<T> = new (...args: any[]) => T

interface Element {
	msMatchesSelector(selectors: string): boolean
}

interface HTMLElementEventMap {
	focusin: FocusEvent
	focusout: FocusEvent
}
