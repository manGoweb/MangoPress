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
		this.getChild('.example-child', HTMLElement).innerText += ` ${this.data.name}!`
	}

	handleDelegateClick(e: DelegateEvent<'click'>): void {
		console.log(e.delegateTarget)
		alert(
			`Hello, ${this.data.name}! The number of the day is ${this.data.numberOfTheDay.toFixed(0)}.`
		)
	}

	handleClick(e: MouseEvent): void {
		e.preventDefault()
		console.log('Example component clicked')
	}
}
