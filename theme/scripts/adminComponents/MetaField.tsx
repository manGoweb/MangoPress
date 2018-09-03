import isFunction from 'lodash-es/isFunction'
import React from 'react'

interface MetaFieldProps {
	name: string
	callback?: boolean
	json?: boolean
	defaultValue?: any
	visible?: boolean
	children: (options: { value: any; setValue: (newValue: any) => void }) => JSX.Element
}

const noop = () => {}

export default class MetaField extends React.Component<MetaFieldProps, { changedValue: any }> {
	state = {
		changedValue: null,
	}

	getValue() {
		return this.state.changedValue || this.props.defaultValue || null
	}

	setValue = (value: any) => {
		this.setState(
			isFunction(value)
				? (oldState) => ({
						changedValue: value(oldState.changedValue || this.props.defaultValue || null),
				  })
				: { changedValue: value }
		)
	}

	render() {
		const { name, callback, json, visible } = this.props
		const value = this.getValue()
		return (
			<React.Fragment>
				{this.props.children({ value, setValue: this.setValue })}
				<input
					type={visible ? 'text' : 'hidden'}
					name={`ac-${callback ? 'callback' : 'meta'}${json ? '-json' : ''}[${name}]`}
					value={(json ? JSON.stringify(value) : value) || ''}
					onChange={noop}
				/>
			</React.Fragment>
		)
	}
}
