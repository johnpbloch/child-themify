import React from 'react';
import ReactDOM from 'react-dom';
import _ from 'lodash';
import App from './App';

const settings = _.assign({}, window.ChildThemify, {
    themes: [],
    i18n: {
        header: '',
        theme_select_label: '',
    },
});

ReactDOM.render(<App {...settings}/>, document.getElementById('ctfAppRoot'));
