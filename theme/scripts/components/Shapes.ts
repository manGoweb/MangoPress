import Component from './Component'

interface ShapesData {
	url: string
}

/**
 * Shapes component class
 *
 * - injects SVG sprite into body
 */
export default class Shapes extends Component<ShapesData> {
	static componentName = 'Shapes'

	init() {
		document.implementation.hasFeature(
			'http://www.w3.org/TR/SVG11/feature#BasicStructure',
			'1.1'
		) && this.injectSprite()
	}

	injectSprite(): void {
		fetch(this.data.url)
			.then((response: Response) => {
				if (!response.ok) {
					throw new Error(response.statusText)
				}
				return response.text()
			})
			.then((shapes: string) => {
				const wrapper = document.createElement('div')
				const body = document.body

				wrapper.innerHTML = shapes

				const el = wrapper.children.item(0)
				if (el) {
					body.insertBefore(el, body.firstChild)
				}
			})
			.catch(() => {
				setTimeout(() => this.injectSprite(), 1e4)
			})
	}
}
