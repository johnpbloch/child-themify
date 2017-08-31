import axios from 'axios';
import AxiosMockAdapter from 'axios-mock-adapter';
import React from 'react';
import {shallow, mount} from 'enzyme';
import state from '../../__mocks__/state.json';
import App from '../App';
import twentyseventeen from '../../__mocks__/api/theme-data/twentyseventeen.json';

describe('<App/> component tests', () => {
    const axiosMock = new AxiosMockAdapter(axios);

    test('Name field appears when theme selected', () => {
        const component = shallow(<App themes={state.themes}/>);

        expect(component.find('.name-field-wrapper').exists()).toBe(false);

        axiosMock.reset();
        axiosMock
            .onGet('http://ctf.dev/wp-json/child-themify/v1/theme-data/twentyseventeen')
            .reply(200, twentyseventeen);

        component.instance().selectTheme({value: 'twentyseventeen'});

        expect(component.find('.name-field-wrapper').exists()).toBe(true);
    });

    test('Name converts to predictable slug', () => {
        const component = shallow(<App themes={state.themes}/>);
        const instance = component.instance();
        instance.checkChildSlug = instance.realCheckChildSlug;
        axiosMock.reset();
        axiosMock
            .onGet('http://ctf.dev/wp-json/child-themify/v1/theme-data/twentyseventeen')
            .reply(200, twentyseventeen)
            .onGet()
            .reply(404, {});
        instance.selectTheme({value: 'twentyseventeen'});

        instance.updateThemeName('Basic Name');
        expect(component.state('childSlug')).toBe('basic-name');

        instance.updateThemeName('Name&with!symbols*#($*%');
        expect(component.state('childSlug')).toBe('namewithsymbols');

        instance.updateThemeName('Preserves-hyphens_underscores, not~other/punctuation.');
        expect(component.state('childSlug')).toBe('preserves-hyphens_underscores-nototherpunctuation');

        instance.updateThemeName(`handles\twhite\nspace well`);
        expect(component.state('childSlug')).toBe('handles-white-space-well');

        instance.updateThemeName(`compresses
    redundant               whitespace
    \t of all kinds`);
        expect(component.state('childSlug')).toBe('compresses-redundant-whitespace-of-all-kinds');

        instance.updateThemeName('compresses----hyphens_______underscores___---___separately');
        expect(component.state('childSlug')).toBe('compresses-hyphens_underscores_-_separately');
    });

    test('Advanced field toggle works', () => {
        const component = mount(<App themes={state.themes}/>);

        axiosMock.reset();
        axiosMock
            .onGet('http://ctf.dev/wp-json/child-themify/v1/theme-data/twentyseventeen')
            .reply(200, twentyseventeen);

        component.instance().selectTheme({value: 'twentyseventeen'});

        expect(component.find('ExtraFiles').exists()).toBe(false);

        component.find('.advancedToggle').simulate('click');

        expect(component.find('ExtraFiles').exists()).toBe(true);
    });

    test('Reset data on theme clear', () => {
        const component = mount(<App themes={state.themes}/>);
        component.setState({
            theme: 'twentyseventeen',
        });

        component.instance().selectTheme();

        expect(component.state('theme')).toBe('');
    });
});
