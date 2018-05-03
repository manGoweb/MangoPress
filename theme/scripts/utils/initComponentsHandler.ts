export default (components, initComponentsPlaceholder = "initComponents") => {
	let componentsStartTime, colorLog

	if (DEBUG) {
		componentsStartTime = performance.now()
		colorLog = (message, color) => {
			console.log(`%c${message}`, `color: ${color}`)
		}
		colorLog("Initializing components...", "brown")
	}

	//
	// Lazy components initialization from initComponents queue
	//
	const instances = []

	// Init function
	const init = component => {
		let componentStartTime

		if (component.name in components) {
			if (DEBUG) {
				componentStartTime = performance.now()
			}

			const Component = components[component.name] // class
			const placement =
				typeof component.place === "string"
					? document.querySelector(component.place)
					: component.place // DOM element

			if (DEBUG) {
				if (component.place && !placement) {
					let appendText = ""

					if (typeof component.place === "string") {
						if (
							!component.place.startsWith(".") &&
							!component.place.startsWith("#")
						) {
							appendText = `Did you mean ".${component.place}" or "#${component.place}".`
						}
					}
					console.error(
						`Component "${component.name}" cannot find place element for "${component.place}". ` +
							appendText
					)
				}
			}

			const instance = new Component(placement, component.data || {}) // new instance

			instances.push(instance)

			if (DEBUG) {
				const componentEndTime = performance.now()
				colorLog(
					`\tcomponent: ${component.name}: ${Math.round(
						componentEndTime - componentStartTime
					)}ms`,
					"blue"
				)
			}
		} else if (DEBUG) {
			console.warn(`Component with name ${component.name} was not found!`)
		}
	}
	window[initComponentsPlaceholder] = window[initComponentsPlaceholder] || []

	// Instance only required components
	window[initComponentsPlaceholder].map(init)

	// Allow lazy init of components after page load
	window[initComponentsPlaceholder] = {
		push: init,
	}

	if (DEBUG) {
		const componentsEndTime = performance.now()
		colorLog(
			`Components initialization: ${Math.round(
				componentsEndTime - componentsStartTime
			)}ms`,
			"blue"
		)
		colorLog("Instances:", "brown")
		console.log(instances)
	}

	//
	// Print timing data on page load
	//
	if (DEBUG) {
		const printPerfStats = () => {
			const timing = window.performance.timing
			colorLog("Performance:", "brown")
			colorLog(
				`\tdns: \t\t ${timing.domainLookupEnd -
					timing.domainLookupStart} ms\n` +
					`\tconnect: \t ${timing.connectEnd - timing.connectStart} ms\n` +
					`\tttfb: \t\t ${timing.responseStart - timing.connectEnd} ms\n` +
					`\tbasePage: \t ${timing.responseEnd - timing.responseStart} ms\n` +
					`\tfrontEnd: \t ${timing.loadEventStart - timing.responseEnd} ms\n`,
				"blue"
			)
		}

		window.addEventListener("load", () => setTimeout(printPerfStats, 1000))
	}
}
