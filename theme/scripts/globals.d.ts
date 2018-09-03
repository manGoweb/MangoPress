declare const DEBUG: boolean
declare module 'nette-forms'

interface ComponentDefinition {
	name: string
	place?: keyof HTMLElementTagNameMap | HTMLElement
	data?: any
}

type ComponentInitializer =
	| Array<ComponentDefinition>
	| {
			push: (definition: ComponentDefinition) => void
	  }

interface Window {
	initComponents: ComponentInitializer

	initAdminComponents: ComponentInitializer

	initStyleguideComponents: ComponentInitializer

	tinymce: any
}

declare namespace NodeJS {
	interface Global {}
}

type ComponentInitializerName = { [I in keyof Window]: I }[
	| 'initComponents'
	| 'initAdminComponents'
	| 'initStyleguideComponents']
