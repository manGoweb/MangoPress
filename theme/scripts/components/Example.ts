import { Component, EventListeners, DelegateEvent } from '@mangoweb/scripts-base'

interface ExampleProps {
	name: string
	numberOfTheDay: number
}

export class Example extends Component<ExampleProps> {
	static componentName = 'Example'

	protected getListeners = (): EventListeners => [
		['click', this.handleClick],
		['click', '.example-child', this.handleDelegateClick],
	]

	public init() {
		this.getChild('.example-child', HTMLElement).innerText += ` ${this.props.name}!`
	}

	private handleDelegateClick(e: DelegateEvent<'click'>): void {
		console.log(e.delegateTarget)
		alert(`Hello, ${this.props.name}! The number of the day is ${this.props.numberOfTheDay.toFixed(0)}.`)
	}

	private handleClick(e: MouseEvent): void {
		e.preventDefault()
		console.log('Example component clicked')
	}
}
