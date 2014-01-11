(function (window, l10n) {
	var $ = window.jQuery,
			themes = window.wp.themes,
			themesView,
			themeViews = {};

	function injectLinks(into, model) {
		var className = '.theme-actions .' + (model.get('active') ? '' : 'in') + 'active-theme',
				links = into.find(className);
		if (links.length) {
			links.first().append('<a href="#" class="button button-secondary">' +
					l10n.createAChildTheme +
					'</a>');
		}
	}

	function onExpand() {
		window.setTimeout(function () {
			injectLinks(themesView.overlay.$el, themesView.model);
		}, 30);
	}

	function onLoad() {
		themesView = themes.Run.view.view;
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

	$(onLoad);
}(window, window.childThemify));
