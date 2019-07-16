async function initNetteForms() {
	const NetteForms = await import('nette-forms')
	NetteForms.initOnLoad()
}

if (document.querySelector('[data-nette-form="true"]')) {
	initNetteForms()
}
