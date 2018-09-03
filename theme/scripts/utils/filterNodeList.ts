import matchesSelector from './matchesSelector'

export default <E extends HTMLElement>(list: NodeListOf<E>, selector: string): Array<E> =>
	Array.prototype.filter.call(list, (item: E) => matchesSelector(item, selector))
