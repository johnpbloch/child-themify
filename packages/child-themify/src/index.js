import React from 'react';
import ReactDOM from 'react-dom';
import _ from 'lodash';
import App from './App';
import registerServiceWorker from './registerServiceWorker';

const settings = _.assign({}, window.ChildThemify, {
    themes: [],
});

ReactDOM.render(<App {...settings}/>, document.getElementById('ctfAppRoot'));
registerServiceWorker();
