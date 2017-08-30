import React from 'react';
import {mount} from 'enzyme';
import {ExtraFiles} from '../../../Fields/ExtraFiles';
import ReactLoading from 'react-loading';

describe('<ExtraFiles/> component tests', () => {
    it('Toggles the loader when loading', () => {
        const wrapper = mount(<ExtraFiles/>);

        expect(wrapper.find(ReactLoading).exists()).toBe(false);

        wrapper.setProps({dataLoading: true});

        expect(wrapper.find(ReactLoading).exists()).toBe(true);
    });

    it('Defaults to no-op', () => {
        const files = [
            'Foo',
            'Bar',
            'Baz',
            'Bat',
        ];
        const wrapper = mount(<ExtraFiles themeFiles={files}/>);

        wrapper.find('.select-all').simulate('click');

        expect(wrapper.prop('selectedFiles')).toEqual([]);
    });

    it('Selects All', () => {
        const files = [
            'Foo',
            'Bar',
            'Baz',
            'Bat',
        ];
        const wrapper = mount(<ExtraFiles themeFiles={files}/>);
        wrapper.setProps({
            onChange: selectedFiles => {
                wrapper.setProps({selectedFiles});
            }
        });

        expect(wrapper.prop('selectedFiles')).toEqual([]);

        wrapper.find('.select-all').simulate('click');

        expect(wrapper.prop('selectedFiles')).toEqual(files);
    });

    it('Selects None', () => {
        const files = [
            'Foo',
            'Bar',
            'Baz',
            'Bat',
        ];
        const wrapper = mount(<ExtraFiles themeFiles={files} selectedFiles={files}/>);
        wrapper.setProps({
            onChange: selectedFiles => {
                wrapper.setProps({selectedFiles});
            }
        });

        wrapper.find('.select-none').simulate('click');

        expect(wrapper.prop('selectedFiles')).toEqual([]);
    });

    it('Checks and unchecks individual files', () => {
        const files = ['Foo', 'Bar', 'Baz', 'Bat'];
        const selectedFiles = ['Foo', 'Baz'];
        const wrapper = mount(<ExtraFiles themeFiles={files} selectedFiles={selectedFiles}/>);
        wrapper.setProps({
            onChange: selectedFiles => {
                wrapper.setProps({selectedFiles});
            }
        });

        files.forEach(file => {
            wrapper.find(`input[value="${file}"]`).simulate('change', {
                target: {
                    value: file,
                    checked: -1 === selectedFiles.indexOf(file),
                }
            });
        });

        expect(wrapper.prop('selectedFiles')).toEqual(['Bar', 'Bat']);
    });
});
