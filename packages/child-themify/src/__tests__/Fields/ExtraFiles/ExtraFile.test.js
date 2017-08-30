import React from 'react';
import {mount} from 'enzyme';
import {ExtraFile} from '../../../Fields/ExtraFiles/ExtraFile';

describe('<ExtraFile> checkbox component', () => {
    it('no-ops by default', () => {
        const wrapper = mount(<ExtraFile name="Foobar"/>);

        expect(wrapper.prop('checked')).toBe(false);

        wrapper.find('input').simulate('change');

        expect(wrapper.prop('checked')).toBe(false);
    });

    it('Updates using onClick', () => {
        const wrapper = mount(<ExtraFile name="Bazbat"/>);

        wrapper.setProps({
            onChange: (val, checked) => {
                wrapper.setProps({checked});
            }
        });

        wrapper.find('input').simulate('change', {target:{
            value: 'Bazbat',
            checked: true,
        }});

        expect(wrapper.prop('checked')).toBe(true);
    });
});
