import Component from './Component'

interface ExampleData {
	name: string
	numberOfTheDay: number
}

export default class Example extends Component<ExampleData> {
	static componentName = 'Example'

	getListeners = (): EventListeners => [
		['click', this.handleClick],
		['click', '.example-child', this.handleDelegateClick],
	]

	init() {
		// Initialize your component.
	}

	handleDelegateClick(e: DelegateEvent<'click'>): void {
		console.log(e.delegateTarget)
	}

	handleClick(e: MouseEvent): void {
		e.preventDefault()
		alert(
			`Hello, ${this.data.name}! The number of the day is ${this.data.numberOfTheDay.toFixed(0)}`
		)
	}
}
