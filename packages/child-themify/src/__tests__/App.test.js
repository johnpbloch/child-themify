import axios from 'axios';
import AxiosMockAdapter from 'axios-mock-adapter';
import React from 'react';
import {shallow, mount} from 'enzyme';
import state from '../../__mocks__/state.json';
import App from '../App';
import {Input} from '../Fields/Input';
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

    test('Existing theme names cannot be used', () => {
        expect.assertions(4);
        const component = mount(<App themes={state.themes}/>);
        axiosMock.reset();
        axiosMock
            .onGet('http://ctf.dev/wp-json/child-themify/v1/theme-data/twentyseventeen')
            .reply(200, twentyseventeen);
        const instance = component.instance();
        instance.checkChildSlug = instance.realCheckChildSlug;
        instance.selectTheme({value: 'twentyseventeen'});

        axiosMock
            .onGet('http://ctf.dev/wp-json/child-themify/v1/theme-data/test-theme')
            .reply(200, {"files": {}})
            .onGet()
            .reply(404, {});

        return Promise.resolve(1)
            .then(() => {
                return new Promise(resolve => {
                    instance.updateThemeName('First Test Theme');
                    setTimeout(() => resolve(1));
                })
            })
            .then(() => {
                return new Promise(resolve => {
                    expect(component.find('.dashicons-yes').exists()).toBe(true);
                    expect(component.find('.dashicons-no').exists()).toBe(false);
                    resolve(1);
                });
            })
            .then(() => {
                return new Promise(resolve => {
                    instance.updateThemeName('Test Theme');
                    setTimeout(() => resolve(1));
                });
            })
            .then(() => {
                expect(component.find('.dashicons-yes').exists()).toBe(false);
                expect(component.find('.dashicons-no').exists()).toBe(true);
            });
    });

    test('Creating the theme works as expected', () => {
        expect.assertions(1);
        const component = mount(<App themes={state.themes}/>);
        axiosMock.reset();
        axiosMock
            .onGet('http://ctf.dev/wp-json/child-themify/v1/theme-data/twentyseventeen')
            .reply(200, twentyseventeen);
        const instance = component.instance();
        instance.checkChildSlug = instance.realCheckChildSlug;
        instance.selectTheme({value: 'twentyseventeen'});
        axiosMock
            .onPost('http://ctf.dev/wp-json/child-themify/v1/create-theme')
            .reply(201, {success: true});

        return Promise.resolve(1)
            .then(() => {
                return new Promise(resolve => {
                    instance.updateThemeName('Test Theme');
                    setTimeout(() => resolve(1));
                });
            })
            .then(() => {
                return new Promise(resolve => {
                    component.find('.button-primary').simulate('click');
                    setTimeout(() => resolve(1));
                });
            })
            .then(() => {
                expect(component.find('.notice-success').exists()).toBe(true);
            });
    });

    test('Error displays when create fails: 400 w/ message', () => {
        expect.assertions(2);
        const component = mount(<App themes={state.themes}/>);
        axiosMock.reset();
        axiosMock
            .onGet('http://ctf.dev/wp-json/child-themify/v1/theme-data/twentyseventeen')
            .reply(200, twentyseventeen);
        const instance = component.instance();
        instance.checkChildSlug = instance.realCheckChildSlug;
        instance.selectTheme({value: 'twentyseventeen'});
        axiosMock
            .onPost('http://ctf.dev/wp-json/child-themify/v1/create-theme')
            .reply(400, {
                code: 'ctf_no_fs',
                message: 'Could not write to server! Please check your credentials and try again.'
            });

        return Promise.resolve(1)
            .then(() => {
                return new Promise(resolve => {
                    instance.updateThemeName('Test Theme');
                    setTimeout(() => resolve(1));
                });
            })
            .then(() => {
                return new Promise(resolve => {
                    component.find('.button-primary').simulate('click');
                    setTimeout(() => resolve(1));
                });
            })
            .then(() => {
                expect(component.find('.notice-error').exists()).toBe(true);
                expect(component.find('.notice-error').text())
                    .toBe('Looks like something was wrong with the info you provided. Here\'s the message we got: Could not write to server! Please check your credentials and try again.');
            });
    });

    test('Error displays when create fails: 400 w/out message', () => {
        expect.assertions(2);
        const component = mount(<App themes={state.themes}/>);
        axiosMock.reset();
        axiosMock
            .onGet('http://ctf.dev/wp-json/child-themify/v1/theme-data/twentyseventeen')
            .reply(200, twentyseventeen);
        const instance = component.instance();
        instance.checkChildSlug = instance.realCheckChildSlug;
        instance.selectTheme({value: 'twentyseventeen'});
        axiosMock
            .onPost('http://ctf.dev/wp-json/child-themify/v1/create-theme')
            .reply(400, {});

        return Promise.resolve(1)
            .then(() => {
                return new Promise(resolve => {
                    instance.updateThemeName('Test Theme');
                    setTimeout(() => resolve(1));
                });
            })
            .then(() => {
                return new Promise(resolve => {
                    component.find('.button-primary').simulate('click');
                    setTimeout(() => resolve(1));
                });
            })
            .then(() => {
                expect(component.find('.notice-error').exists()).toBe(true);
                expect(component.find('.notice-error').text())
                    .toBe('Looks like something was wrong with the info you provided. Please make sure your info is correct and try again.');
            });
    });

    test('Error displays when create fails: 500 w/ message', () => {
        expect.assertions(2);
        const component = mount(<App themes={state.themes}/>);
        axiosMock.reset();
        axiosMock
            .onGet('http://ctf.dev/wp-json/child-themify/v1/theme-data/twentyseventeen')
            .reply(200, twentyseventeen);
        const instance = component.instance();
        instance.checkChildSlug = instance.realCheckChildSlug;
        instance.selectTheme({value: 'twentyseventeen'});
        axiosMock
            .onPost('http://ctf.dev/wp-json/child-themify/v1/create-theme')
            .reply(500, {
                code: 'ctf-no-create-dir',
                message: 'Could not create new theme directory!'
            });

        return Promise.resolve(1)
            .then(() => {
                return new Promise(resolve => {
                    instance.updateThemeName('Test Theme');
                    setTimeout(() => resolve(1));
                });
            })
            .then(() => {
                return new Promise(resolve => {
                    component.find('.button-primary').simulate('click');
                    setTimeout(() => resolve(1));
                });
            })
            .then(() => {
                expect(component.find('.notice-error').exists()).toBe(true);
                expect(component.find('.notice-error').text())
                    .toBe('Oops! Something went wrong! Here\'s the message we got: Could not create new theme directory!');
            });
    });

    test('Error displays when create fails: 500 w/out message', () => {
        expect.assertions(2);
        const component = mount(<App themes={state.themes}/>);
        axiosMock.reset();
        axiosMock
            .onGet('http://ctf.dev/wp-json/child-themify/v1/theme-data/twentyseventeen')
            .reply(200, twentyseventeen);
        const instance = component.instance();
        instance.checkChildSlug = instance.realCheckChildSlug;
        instance.selectTheme({value: 'twentyseventeen'});
        axiosMock
            .onPost('http://ctf.dev/wp-json/child-themify/v1/create-theme')
            .reply(500, {});

        return Promise.resolve(1)
            .then(() => {
                return new Promise(resolve => {
                    instance.updateThemeName('Test Theme');
                    setTimeout(() => resolve(1));
                });
            })
            .then(() => {
                return new Promise(resolve => {
                    component.find('.button-primary').simulate('click');
                    setTimeout(() => resolve(1));
                });
            })
            .then(() => {
                expect(component.find('.notice-error').exists()).toBe(true);
                expect(component.find('.notice-error').text())
                    .toBe('Oops! Something went wrong! Please try again later.');
            });
    });
});
