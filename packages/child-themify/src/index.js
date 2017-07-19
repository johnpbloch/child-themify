import React from 'react';
import ReactDOM from 'react-dom';
import _ from 'lodash';
import App from './App';

const settings = _.assign({}, {
    wp: window.wp,
    rest: '',
    themes: [],
}, window.ChildThemify);

ReactDOM.render(<App {...settings}/>, document.getElementById('ctfAppRoot'));
