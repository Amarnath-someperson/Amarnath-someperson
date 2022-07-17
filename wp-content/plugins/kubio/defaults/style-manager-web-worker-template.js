// defaults and polyfills
// eslint-disable-next-line no-undef
window = self;
// eslint-disable-next-line no-undef
top = self;
const kubioNoop = function () {};

document = {
	createElement() {
		return {
			style: [],
			setAttribute: kubioNoop,
			attachEvent: kubioNoop,
		};
	},
	attachEvent: kubioNoop,
	addEventListener: kubioNoop,
	querySelectorAll() {
		return [];
	},
};

// wp imported scripts need to load kubio-style-manager
// {{{importScriptsPlaceholder}}}

const fonts = {};

// eslint-disable-next-line no-undef
wp.hooks.addAction(
	'kubio.google-fonts.load',
	'kubio.google-fonts.load',
	function (nextFonts) {
		nextFonts.forEach(function (font) {
			fonts[font.family] = fonts[font.family] || [];
			// eslint-disable-next-line no-undef
			fonts[font.family] = lodash.uniq(
				fonts[font.family].concat(font.variants)
			);
		});
	}
);

const renderStyle = function (payload) {
	const { data, parentDetails, canUseHtml, document = null, hash } = payload;
	// eslint-disable-next-line no-undef
	const dynamicStyle = lodash.cloneDeep(lodash.get(data, 'dynamicStyle', {}));
	// eslint-disable-next-line no-undef
	const renderer = new kubio.styleManager.BlockStyleRender(
		// eslint-disable-next-line no-undef
		lodash.omit(data, 'dynamicStyle'),
		parentDetails,
		canUseHtml,
		document
	);

	const styleRef = renderer.model?.styleRef;
	const localId = renderer.model?.id;

	return {
		css: renderer.export(),
		dynamicRules: renderer.exportDynamicStyle(dynamicStyle),
		styleRef,
		localId,
		responseHash: hash,
		fonts: Object.keys(fonts).map((family) => ({
			family,
			variants: fonts[family],
		})),
	};
};

// actual web worker runner
// eslint-disable-next-line no-undef
self.addEventListener('message', (event) => {
	const { action, hash, payload } = event.data;

	let response = null;

	switch (action) {
		case 'EXPORT_CSS':
			response = renderStyle(payload);
			break;
		case 'RENDER_CSS':
			// eslint-disable-next-line no-undef
			response = kubio.styleManager.cssobj(payload).css;
			break;
		case 'TEST':
			response = 'test';
			break;
	}

	// eslint-disable-next-line no-undef
	self.postMessage({
		hash,
		payload: response,
	});
});

// eslint-disable-next-line no-undef
self.postMessage('WORKER_LOADED');
