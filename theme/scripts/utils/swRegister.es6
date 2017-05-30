if ('serviceWorker' in navigator) {
	navigator.serviceWorker.register('serviceWorker.js').then((registration) => {
		console.log(registration)
	})
}
