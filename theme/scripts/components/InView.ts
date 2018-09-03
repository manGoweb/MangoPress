import Component from './Component'

interface InViewData {
	targets: string
	threshold: number
	detectOnce: boolean
	strictTop: boolean
	afterUpdate: Function
}

export default class InView extends Component<InViewData> {
	static componentName = 'InView'
	targets: NodeListOf<Element> | HTMLElement[]
	threshold: number
	detectOnce: boolean
	strictTop: boolean

	private readonly CLASSES = Object.freeze({
		topThreshold: 'view-topThreshold',
		bottomThreshold: 'view-bottomThreshold',
	})

	constructor(el: HTMLElement, data: InViewData) {
		super(el, data)

		this.targets = data.targets ? this.el.querySelectorAll(data.targets) : [this.el]
		this.threshold = data.threshold || 0
		this.detectOnce = data.hasOwnProperty('detectOnce') ? data.detectOnce : true
		this.strictTop = data.strictTop || false

		if ('IntersectionObserver' in window) {
			this._observerInit()
		} else {
			this._onScrollInit()
		}
	}

	_observerInit() {
		const observer = new IntersectionObserver(
			(entries) => {
				//callback
				entries.forEach((entry) => {
					const target = entry.target
					const intersectionRatio = entry.intersectionRatio
					this._updateState(
						target,
						intersectionRatio,
						entry.boundingClientRect.top < entry.rootBounds.height / 2
					)
				})
			},
			{
				//options
				threshold: this.threshold === 0 ? [0] : [0, this.threshold],
			}
		)

		for (let i = 0, length = this.targets.length; i < length; i++) {
			observer.observe(this.targets[i])
		}
	}

	_onScrollInit() {
		window.addEventListener('scroll', () => {
			window.requestAnimationFrame(() => {
				for (let i = 0, length = this.targets.length; i < length; i++) {
					const target = this.targets[i]
					const targetRect = target.getBoundingClientRect()
					const targetArea = targetRect.width * targetRect.height
					const intersectionArea =
						Math.max(
							0,
							Math.min(targetRect.left + targetRect.width, window.innerWidth) -
								Math.max(targetRect.left, 0)
						) * // width
						Math.max(
							0,
							Math.min(targetRect.top + targetRect.height, window.innerHeight) -
								Math.max(targetRect.top, 0)
						) // height
					const intersectionRatio = intersectionArea / targetArea
					this._updateState(target, intersectionRatio, targetRect.top < window.innerHeight / 2)
				}
			})
		})
		window.scrollTo(window.scrollX, window.scrollY)
	}

	_updateState(target: any, intersectionRatio: number, targetTopAboveViewCenter: boolean) {
		const hasTopClassThreshold = target.classList.contains(this.CLASSES.topThreshold)
		const hasBottomClassThreshold = target.classList.contains(this.CLASSES.bottomThreshold)
		const strictTop =
			this.data.hasOwnProperty('detectOnce') && this.data.strictTop ? this.threshold : 0
		const topThreshold = hasTopClassThreshold ? strictTop : this.threshold
		const isTop =
			(this.detectOnce && hasTopClassThreshold) ||
			(intersectionRatio > topThreshold || targetTopAboveViewCenter)
		const isBottom =
			(this.detectOnce && hasBottomClassThreshold) ||
			(intersectionRatio <= this.threshold && targetTopAboveViewCenter)
		target.classList.toggle(this.CLASSES.topThreshold, isTop)
		target.classList.toggle(this.CLASSES.bottomThreshold, isBottom)
		if (this.data.afterUpdate) {
			this.data.afterUpdate(isTop, isBottom)
		}
	}
}
