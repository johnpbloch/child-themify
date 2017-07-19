import React from 'react';
import ReactDOM from 'react-dom';
import App from './App';
import {settings} from './Utils';

ReactDOM.render(<App themes={settings.theme_list}/>, document.getElementById('ctfAppRoot'));
