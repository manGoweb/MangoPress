export default (element: Element, selector: string): boolean => {
	return (element.matches || element.msMatchesSelector).call(element, selector)
}
