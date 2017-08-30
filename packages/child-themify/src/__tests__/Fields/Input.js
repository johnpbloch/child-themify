import React from 'react';
import {mount} from 'enzyme';
import {Input} from '../../Fields/Input';

describe('<Input/> component tests', () => {
    it('Defaults to a no-op', () => {
        const wrapper = mount(<Input label="Test" value="test"/>);
        wrapper.find('.widefat').simulate('change', {target: {value: 'new value'}});
        expect(wrapper.prop('value')).toEqual('test');
    });

    it('Updates from parent', () => {
        const wrapper = mount(<Input label="Test" value="test"/>);
        wrapper.setProps({onChange: value => wrapper.setProps({value})});
        wrapper.find('.widefat').simulate('change', {target: {value: 'new value'}});
        expect(wrapper.prop('value')).toEqual('new value');
    });
});
