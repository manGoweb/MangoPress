import React from 'react'
import createReactComponent from '../utils/createReactComponent'

interface Props {
	name?: string
	value?: string
	message?: string
}

interface State {
	count: number
}

const incrementCount = ({ count }: State) => ({ count: count + 1 })

class ExampleMetabox extends React.Component<Props, State> {
	static componentName = 'ExampleMetabox'

	state = {
		count: 1,
	}

	increment = () => {
		this.setState(incrementCount)
	}

	render() {
		const { name, value, message } = this.props

		return (
			<div>
				<div style={{ textAlign: 'right' }}>
					<button type="button" onClick={this.increment} className="button button-small">
						Click me {this.state.count}
					</button>
				</div>
				{!!Object.keys(this.props).length && (
					<pre style={{ whiteSpace: 'pre-wrap', fontSize: 10, lineHeight: 1.1 }}>
						{JSON.stringify(this.props, null, 2)}
					</pre>
				)}
				{message && <p>{message}</p>}
				{name && (
					<div>
						<label>
							Stored in <code>{name}</code> meta field
						</label>
						<br />
						<input
							style={{ width: '100%' }}
							type="text"
							name={`ac-meta[${name}]`}
							defaultValue={value}
						/>
						<hr />
						<label>
							This is callback field. Try writing <code>test</code>. It calls{' '}
							<code>ac_customCallback</code>.
						</label>
						<br />
						<input style={{ width: '100%' }} type="text" name={`ac-callback[ac_customCallback]`} />
					</div>
				)}
			</div>
		)
	}
}

export default createReactComponent(ExampleMetabox)
