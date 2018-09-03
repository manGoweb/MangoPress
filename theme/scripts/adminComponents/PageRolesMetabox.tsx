import createReactComponent from '../utils/createReactComponent'
import React, { SyntheticEvent } from 'react'

import { map, pickBy } from 'lodash-es'

interface Roles {
	[name: string]: null | false | PostId
}

interface TrProps {
	label: string
	name: string
	onChange: (roles: Roles) => void
	originalRoles: Roles
	page_id: PageId
	post_id: PostId
	title: string
}

class Tr extends React.Component<TrProps> {
	clearClick = (e: SyntheticEvent) => {
		this.props.onChange({ [this.props.name]: null })
	}

	undoClick = (e: SyntheticEvent) => {
		this.props.onChange({ [this.props.name]: false })
	}

	setThisClick = (e: SyntheticEvent) => {
		this.props.onChange({ [this.props.name]: this.props.post_id })
	}

	render() {
		const { post_id, page_id, name, originalRoles, title, label } = this.props
		return (
			<tr>
				<th style={{ whiteSpace: 'nowrap', fontSize: 10 }} title={name}>
					{label || name}
				</th>
				<td
					style={{
						whiteSpace: 'nowrap',
						fontSize: 10,
						overflow: 'hidden',
						textOverflow: 'ellipsis',
						position: 'relative',
						lineHeight: 1,
						width: '75%',
					}}
				>
					{page_id === post_id ? (
						<strong>this page</strong>
					) : (
						page_id && (
							<a href={`?post=${page_id}&action=edit`} title={title}>
								#{page_id} {title}
							</a>
						)
					)}

					<div
						style={{
							position: 'absolute',
							top: '50%',
							right: 5,
							transform: 'translateY(-50%)',
						}}
					>
						{!page_id && (
							<button type="button" className="button button-small" onClick={this.setThisClick}>
								set this page
							</button>
						)}{' '}
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
					</div>
				</td>
			</tr>
		)
	}
}

interface PageRolesProps {
	post_id: PostId
	roles: Roles
	labels: string[]
	titles: string[]
}

interface PageRolesState {
	roles: Roles
}

class PageRolesMetabox extends React.Component<PageRolesProps, PageRolesState> {
	static componentName = 'PageRolesMetabox'

	state = {
		roles: {},
	}

	onRowChange = (update: Roles) => {
		this.setState({
			roles: pickBy({ ...this.state.roles, ...update }, (val) => val !== false) as Roles,
		})
	}

	render() {
		const roles = { ...this.props.roles, ...this.state.roles }
		const originalRoles = this.props.roles
		const titles = this.props.titles
		const labels = this.props.labels
		const post_id = +this.props.post_id

		return (
			<div>
				<table className="wp-list-table widefat fixed striped posts">
					<tbody>
						{map(roles, (page_id, key: number) => (
							<Tr
								key={key}
								page_id={page_id}
								post_id={post_id}
								name={key.toString()}
								originalRoles={originalRoles}
								onChange={this.onRowChange}
								title={titles[page_id]}
								label={labels[key]}
							/>
						))}
					</tbody>
				</table>
				<input
					type="hidden"
					name={`ac-callback-json[ac_savePageRoles]`}
					value={JSON.stringify(roles)}
				/>
			</div>
		)
	}
}

export default createReactComponent(PageRolesMetabox)
