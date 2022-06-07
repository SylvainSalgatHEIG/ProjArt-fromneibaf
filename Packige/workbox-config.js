module.exports = {
	globDirectory: 'public/',
	globPatterns: [
		'**/*.{ico,webmanifest,json}'
	],
	swDest: 'public/sw.js',
	ignoreURLParametersMatching: [
		/^utm_/,
		/^fbclid$/
	]
};