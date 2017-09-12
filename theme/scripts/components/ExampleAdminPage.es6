import createReactComponent from "../utils/createReactComponent"
import React, { Component } from "react"

import { map, pickBy, merge } from "lodash"

const styleWorkarounds = options => {
	let rules = []

	if (options.noPadding) {
		rules.push("#wpcontent { padding-left: 0 }")
	}

	return <style>{rules.join(" ")}</style>
}

class Tr extends Component {
	clearClick = e => {
		this.props.onChange({ [this.props.name]: null })
	}

	undoClick = e => {
		this.props.onChange({ [this.props.name]: false })
	}

	setThisClick = e => {
		this.props.onChange({ [this.props.name]: this.props.post_id })
	}

	render() {
		const { post_id, page_id, name, originalRoles, title } = this.props
		return (
			<tr>
				<th style={{ whiteSpace: "nowrap", fontSize: 12 }}>{name}</th>
				{page_id && (
					<td style={{ whiteSpace: "nowrap", fontSize: 12 }}>
						{page_id === post_id ? (
							<strong>this page</strong>
						) : (
							<a href={`?post=${page_id}&action=edit`} title={title}>
								#{page_id}
							</a>
						)}
					</td>
				)}
				<td
					style={{ textAlign: "right", whiteSpace: "nowrap" }}
					colSpan={page_id ? 1 : 2}
				>
					{!page_id && (
						<button
							type="button"
							className="button button-small"
							onClick={this.setThisClick}
						>
							set this page
						</button>
					)}
					{page_id && (
						<button
							type="button"
							className="button button-small"
							onClick={this.clearClick}
						>
							×
						</button>
					)}
					{originalRoles[name] &&
						page_id !== originalRoles[name] &&
						post_id !== originalRoles[name] && (
							<button
								type="button"
								className="button button-small"
								onClick={this.undoClick}
							>
								undo
							</button>
						)}
				</td>
			</tr>
		)
	}
}

const incrementCount = ({ count }) => ({ count: count + 1 })

class ExampleAdminPage extends Component {
	state = {
		count: 1,
	}

	increment = () => {
		this.setState(incrementCount)
	}

	rand = Math.random()

	render() {
		const { message, argFromConfig } = this.props

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
				<style
				>{`.admin-page-example pre { white-space: pre-wrap; font-size: 10px }`}</style>
			</div>
		)
	}
}

export default createReactComponent(ExampleAdminPage)
