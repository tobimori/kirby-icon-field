import { reactive } from "vue"

const svgs = reactive(new Map())
const requested = new Map()
let queue = new Map()

export function iconSvg(field, value) {
	const endpoint = field?.endpoints?.field

	if (!endpoint) {
		return null
	}

	const key = `${endpoint}:${field.revision}`
	const markup = svgs.get(key)?.get(value)

	if (markup !== undefined) {
		return markup || null
	}

	if (requested.has(key) === false) {
		requested.set(key, new Set())
	}

	if (requested.get(key).has(value) === false) {
		requested.get(key).add(value)

		if (queue.size === 0) {
			setTimeout(flush)
		}

		if (queue.has(key) === false) {
			// the revision belongs to the content language of the view that renders the field
			queue.set(key, { endpoint, language: window.panel.language.code, values: [] })
		}

		queue.get(key).values.push(value)
	}

	return null
}

function flush() {
	const batches = queue
	queue = new Map()

	for (const [key, batch] of batches) {
		load(key, batch)
	}
}

async function load(key, { endpoint, language, values }) {
	try {
		const icons = await window.panel.api.post(
			`${endpoint}/svgs`,
			{ values },
			{ headers: language ? { "x-language": language } : {}, silent: true }
		)

		if (svgs.has(key) === false) {
			svgs.set(key, new Map())
		}

		const cache = svgs.get(key)

		for (const icon of icons) {
			cache.set(icon.value, icon.svg)
		}

		for (const value of values) {
			if (cache.has(value) === false) {
				cache.set(value, "")
			}
		}
	} catch (error) {
		// allow a later render to request the icons again, the Panel shows the error
		for (const value of values) {
			requested.get(key).delete(value)
		}

		throw error
	}
}
