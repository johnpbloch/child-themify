import React, {Component} from 'react';
import PropTypes from 'prop-types';

class Input extends Component {

    updateValue = (event) => {
        this.props.onChange(event.target.value);
    };

    render() {
        return (<div className="ctf-form-field">
            <label>{this.props.label}</label>
            <input
                className="widefat"
                onChange={this.updateValue}
                type={this.props.type}
                value={this.props.value}/>
        </div>)
    }
}

Input.defaultProps = {
    onChange: () => {
    },
    type: 'text',
    value: '',
};

Input.propTypes = {
    label: PropTypes.string.isRequired,
    onChange: PropTypes.func,
    type: PropTypes.string,
    value: PropTypes.string.isRequired,
};

export {Input};
