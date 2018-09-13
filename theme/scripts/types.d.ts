type DelegateEvent<E extends keyof HTMLElementEventMap> = HTMLElementEventMap[E] & {
	delegateTarget: HTMLElement
}

type DelegateEventListenerSpec<E extends keyof HTMLElementEventMap> = [
	E,
	string,
	(event: DelegateEvent<E>) => void
]

type EventListenerSpec<E extends keyof HTMLElementEventMap> = [
	E,
	(event: HTMLElementEventMap[E]) => void
]

type EventListeners = Array<
	{
		[E in keyof HTMLElementEventMap]: DelegateEventListenerSpec<E> | EventListenerSpec<E>
	}[keyof HTMLElementEventMap]
>

type Constructor<T> = new (...args: any[]) => T

interface Element {
	msMatchesSelector(selectors: string): boolean
}
