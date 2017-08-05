import React, {Component} from 'react';
import {PropTypes} from 'prop-types';
import Select from 'react-select';
import {i18n} from "../../Utils/i18n";

class ThemeSelector extends Component {

    render() {
        return (<div className="ctf-form-field">
            <label>{i18n.theme_select_label}</label>
            <Select
                name="form-field-name"
                options={this.props.themes}
                onChange={this.props.onChange}
                placeholder={i18n.theme_placeholder}
                value={this.props.theme}/>
        </div>)
    }

}

ThemeSelector.propTypes = {
    onChange: PropTypes.func,
    themes: PropTypes.array.isRequired,
    theme: PropTypes.string,
};

export {ThemeSelector};
