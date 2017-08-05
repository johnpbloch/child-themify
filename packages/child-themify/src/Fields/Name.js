import React, {Component} from 'react';
import PropTypes from 'prop-types';
import {i18n} from '../Utils';

class Name extends Component {

    updateThemeName = (event) => {
        this.props.onChange(event.target.value);
    };

    render() {
        return (<div className="ctf-form-field">
            <label>{i18n.name_label}</label>
            <input
                className="widefat"
                name="theme-name"
                onChange={this.updateThemeName}
                type="text"
                value={this.props.value}/>
        </div>)
    }
}

Name.defaultProps = {
    onChange: () => {
    },
    value: '',
};

Name.propTypes = {
    onChange: PropTypes.func,
    value: PropTypes.string.isRequired,
};

export {Name};
