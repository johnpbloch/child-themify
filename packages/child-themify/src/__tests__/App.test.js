import axios from 'axios';
import AxiosMockAdapter from 'axios-mock-adapter';
import React from 'react';
import {shallow} from 'enzyme';
import state from '../../__mocks__/state.json';
import App from '../App';

test('Name field appears when theme selected', () => {
    const axiosMock = new AxiosMockAdapter(axios);

    const component = shallow(<App themes={state.themes}/>);

    expect(component.find('.name-field-wrapper').exists()).toBe(false);

    axiosMock.reset();
    axiosMock
        .onGet('http://ctf.dev/wp-json/child-themify/v1/theme-data/twentyseventeen')
        .reply(200, import('../../__mocks__/api/theme-data/twentyseventeen.json'));

    component.instance().selectTheme({value: 'twentyseventeen'});

    expect(component.find('.name-field-wrapper').exists()).toBe(true);
});
