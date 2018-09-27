import createReactComponent from '../utils/createReactComponent'
import React from 'react'

type Values = {
	[key: string]: any
}

interface TrProps {
	onChange: (value: Values) => void
	name: string
	post_id: string
	page_id: string
	originalRoles: Values
	title: string
}

class Tr extends React.Component<TrProps> {
	clearClick = (e: React.SyntheticEvent) => {
		this.props.onChange({ [this.props.name]: null })
	}

	undoClick = (e: React.SyntheticEvent) => {
		this.props.onChange({ [this.props.name]: false })
	}

	setThisClick = (e: React.SyntheticEvent) => {
		this.props.onChange({ [this.props.name]: this.props.post_id })
	}

	render() {
		const { post_id, page_id, name, originalRoles, title } = this.props
		return (
			<tr>
				<th style={{ whiteSpace: 'nowrap', fontSize: 12 }}>{name}</th>
				{page_id && (
					<td style={{ whiteSpace: 'nowrap', fontSize: 12 }}>
						{page_id === post_id ? (
							<strong>this page</strong>
						) : (
							<a href={`?post=${page_id}&action=edit`} title={title}>
								#{page_id}
							</a>
						)}
					</td>
				)}
				<td style={{ textAlign: 'right', whiteSpace: 'nowrap' }} colSpan={page_id ? 1 : 2}>
					{!page_id && (
						<button type="button" className="button button-small" onClick={this.setThisClick}>
							set this page
						</button>
					)}
					{page_id && (
						<button type="button" className="button button-small" onClick={this.clearClick}>
							×
						</button>
					)}
					{originalRoles[name] &&
						page_id !== originalRoles[name] &&
						post_id !== originalRoles[name] && (
							<button type="button" className="button button-small" onClick={this.undoClick}>
								undo
							</button>
						)}
				</td>
			</tr>
		)
	}
}

interface Props {
	message: string
}

interface State {
	count: number
}

const incrementCount = ({ count }: State) => ({ count: count + 1 })

class ExampleAdminPage extends React.Component<Props, State> {
	static componentName = 'ExampleAdminPage'

	state = {
		count: 1,
	}

	increment = () => {
		this.setState(incrementCount)
	}

	rand = Math.random()

	render() {
		const { message } = this.props

		return (
			<div className="wrap admin-page-example">
				<h1>ExampleAdminPage React component</h1>
				<p>
					With <code>{message}</code>
				</p>
				<p>
					<button type="button" onClick={this.increment} className="button">
						Click me: {this.state.count}
					</button>
				</p>
				<pre>{JSON.stringify(this.props, null, 2)}</pre>
				<style>{`.admin-page-example pre { white-space: pre-wrap; font-size: 10px }`}</style>
			</div>
		)
	}
}

export default createReactComponent(ExampleAdminPage)
