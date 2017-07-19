import React from 'react';
import ReactDOM from 'react-dom';
import _ from 'lodash';
import App from './App';

const settings = _.assign({}, {
    themes: [],
    i18n: {
        header: '',
        theme_select_label: '',
        name_label: '',
    },
}, window.ChildThemify);

ReactDOM.render(<App {...settings}/>, document.getElementById('ctfAppRoot'));
