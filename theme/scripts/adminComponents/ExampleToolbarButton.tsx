import React from 'react'
import fromReactComponent from '../utils/fromReactComponent'

interface State {
	count: number
	serverTime?: string
	isWorking: boolean
}

const incrementCount = ({ count }: State) => ({ count: count + 1 })

class ExampleToolbarButton extends React.Component<{}, State> {
	state = {
		count: 0,
		serverTime: undefined,
		isWorking: false,
	}

	increment = () => {
		this.setState(incrementCount)
	}

	fetchServerTime() {
		if (!this.state.isWorking) {
			this.setState({ isWorking: true }, () => {
				fetch('/api/server-time')
					.then((res) => res.json())
					.then((res) => {
						this.setState({ isWorking: false, serverTime: res['server-time'] })
					})
			})
		}
	}

	render() {
		const { count, isWorking, serverTime } = this.state
		return (
			<button
				type="button"
				onClick={() => {
					this.increment()
					this.fetchServerTime()
				}}
				className={`smallButton ${isWorking ? 'is-working' : ''}`}
			>
				{count ? `Clicked ${count} times` : 'Click me'}{' '}
				{serverTime ? <small>Server time: {serverTime}</small> : null}
			</button>
		)
	}
}

export default fromReactComponent(ExampleToolbarButton, 'ExampleToolbarButton')
