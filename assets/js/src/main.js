(function (window, l10n, undefined) {
	var $ = window.jQuery,
			themes = window.wp.themes,
			themesView,
			themeViews = {};

	function createUrl(theme) {
		var link = window.location.href;
		if (link.indexOf('?')) {
			link = link.substr(0, link.indexOf('?'));
		}
		link += '?action=child-themify';
		link += '&theme=' + theme;
		link += '&_ctf_nonce=' + l10n.nonce;

		return link;
	}

	function injectLinks(into, model) {
		if (model.get('parent')) {
			return;
		}
		var className = '.theme-actions .' + (model.get('active') ? '' : 'in') + 'active-theme',
				links = into.find(className),
				link = createUrl(model.id);
		if (links.length) {
			links.first().append('<a href="' + link + '" class="button button-secondary">' +
					l10n.createAChildTheme +
					'</a>');
		}
	}

	function onExpand() {
		window.setTimeout(function () {
			injectLinks(themesView.overlay.$el, themesView.model);
		}, 30);
	}

	function initialize() {
		var index,
				obj,
				listeners = themesView._listeners,
				count = 0;
		for (index in listeners) {
			if (listeners.hasOwnProperty(index)) {
				obj = listeners[index];
				if (obj instanceof themes.view.Theme) {
					themeViews[obj.model.id] = obj;
					count += 1;
				}
			}
		}

		if (count === 1) {
			injectLinks(themesView.singleTheme.$el, themesView.model);
		} else if (count > 0) {
			for (index in themeViews) {
				if (themeViews.hasOwnProperty(index)) {
					themesView.listenTo(themeViews[index], 'theme:expand', onExpand);
				}
			}
		}
	}

	function onLoad() {
		themesView = themes.Run.view.view;

		initialize();

		if (undefined !== themes.data.settings.theme && '' !== themes.data.settings.theme) {
			injectLinks(themesView.overlay.$el, themesView.model);
		}

		themesView.listenTo(themesView.collection, 'update', initialize);
	}

	$(onLoad);
}(window, window.childThemify));
