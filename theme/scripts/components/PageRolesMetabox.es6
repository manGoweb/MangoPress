import createReactComponent from './createReactComponent'
import React, { Component } from 'react'

import { map, pickBy, merge } from 'lodash'

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
		return (<tr>
			<th style={{whiteSpace:"nowrap",fontSize:12}}>
				{name}
			</th>
			{page_id && <td style={{whiteSpace:"nowrap",fontSize:12}}>
				{(page_id === post_id) ? <strong>this page</strong> : <a href={`?post=${page_id}&action=edit`} title={title}>#{page_id}</a>}
			</td>}
			<td style={{textAlign:"right",whiteSpace:"nowrap"}} colSpan={page_id ? 1 : 2}>
				{!page_id && <button type="button" className="button button-small" onClick={this.setThisClick}>set this page</button>}
				{page_id && <button type="button" className="button button-small" onClick={this.clearClick}>×</button>}
				{originalRoles[name] && (page_id !== originalRoles[name]) && (post_id !== originalRoles[name]) && <button type="button" className="button button-small" onClick={this.undoClick}>undo</button>}
			</td>
		</tr>)
	}
}

class PageRolesMetabox extends Component {

	state = {
		roles: {}
	}

	onRowChange = (update) => {
		this.setState(state => {
			state.roles = {...state.roles, ...update}
			state.roles = pickBy(state.roles, val => (val !== false))
			return state
		})
	}

	render() {
		const roles = {...this.props.roles, ...this.state.roles}
		const originalRoles = this.props.roles
		const titles = this.props.titles
		const post_id = +this.props.post_id

		return <div>
			<table className="wp-list-table widefat fixed striped posts">
				<tbody>
					{map(roles, (page_id, key) => (<Tr key={key} page_id={page_id} post_id={post_id} name={key} originalRoles={originalRoles} onChange={this.onRowChange} title={titles[key]} />))}
				</tbody>
			</table>
			<input type="hidden" name={`adminComponentCallback[savePageRoles]`} value={JSON.stringify(roles)} />
		</div>
	}

}

module.exports = createReactComponent(PageRolesMetabox)
