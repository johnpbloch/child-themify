import React, {Component} from 'react';
import PropTypes from 'prop-types';
import Select from 'react-select';
import 'react-select/dist/react-select.min.css';
import './App.css';

class App extends Component {
    constructor(props) {
        super(props);

        this.state = {};
    }

    selectTheme = (selected) => {
        this.setState({theme: selected ? selected.value : undefined});
    };

    static formatSlug(name) {
        let slug = name;
        slug = slug.toLowerCase();
        slug = slug.replace(/[^\w\s-]/g, '');
        slug = slug.replace(/\s+/g, '-');
        slug = slug.replace(/([_-])\1+/g, '$1');

        return slug;
    }

    updateThemeName = (event) => {
        const childName = event.target.value;
        const childSlug = App.formatSlug(childName);

        this.setState({childName, childSlug});
    };

    renderThemeSelector() {
        return (<div className="ctf-form-field">
            <label>{this.props.i18n.theme_select_label}</label>
            <Select
                name="form-field-name"
                options={this.props.themes}
                onChange={this.selectTheme}
                value={this.state.theme}/>
        </div>)
    }

    renderNameField() {
        if (!this.state.theme) {
            return null;
        }
        return (<div className="ctf-form-field">
            <label>{this.props.i18n.name_label}</label>
            <input
                className="widefat"
                name="theme-name"
                onChange={this.updateThemeName}
                type="text"
                value={this.state.childName}/>
        </div>)
    }

    render() {
        return (
            <div className="App wrap">
                <h1>{this.props.i18n.header}</h1>
                {this.renderThemeSelector()}
                {this.renderNameField()}
            </div>
        );
    }
}

App.propTypes = {
    themes: PropTypes.arrayOf(PropTypes.shape({
        value: PropTypes.string.isRequired,
        label: PropTypes.string.isRequired,
    })),
};

export default App;
