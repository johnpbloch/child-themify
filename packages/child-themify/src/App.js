import React, {Component} from 'react';
import PropTypes from 'prop-types';
import ReactLoading from 'react-loading';
import 'react-select/dist/react-select.min.css';
import {i18n, Data} from './Utils';
import './App.css';
import {Name, ThemeSelector} from "./Fields";

class App extends Component {
    constructor(props) {
        super(props);

        this.state = {
            theme: '',
            childName: '',
            advanced: false,
            dataLoading: false,
            themeFiles: [],
        };
    }

    selectTheme = (selected) => {
        this.setState({
            theme: selected ? selected.value : '',
            childName: '',
            dataLoading: true,
            themeFiles: [],
        });
        if (selected) {
            Data.themeData(selected.value)
                .then(data => {
                    this.themeData = {
                        files: Object.keys(data.data.files),
                    };
                    this.setState({dataLoading: false});
                });
        }
    };

    static formatSlug(name) {
        let slug = name;
        slug = slug.toLowerCase();
        slug = slug.replace(/[^\w\s-]/g, '');
        slug = slug.replace(/\s+/g, '-');
        slug = slug.replace(/([_-])\1+/g, '$1');

        return slug;
    }

    updateThemeName = (name) => {
        const childName = name;
        const childSlug = App.formatSlug(childName);

        this.setState({childName, childSlug});
    };

    updateExtraFileField = (event) => {
        if (event.target.checked) {
            this.setState({themeFiles: [...this.state.themeFiles, event.target.value]});
        } else {
            this.setState({themeFiles: this.state.filter(i => i !== event.target.value)});
        }
    };

    renderExtraFileField = (file) => {
        const isChecked = -1 !== this.state.themeFiles.indexOf(file);
        return (<p><label>
            <input checked={isChecked} onChange={this.updateExtraFileField} type="checkbox" value={file}/>
            {file}
        </label></p>);
    };

    renderExtraFilesField() {
        if (!this.state.theme) {
            return null;
        }
        return (<div className="ctf-form-field">
            <label>{i18n.files_label}</label>
            {this.state.dataLoading
                ? <ReactLoading type="bubbles" color="#333" delay="0"/>
                : (<div>
                    <p>{i18n.files_description}</p>
                    <div className="ctf-extra-files">
                        {this.themeData.files.map(this.renderExtraFileField)}
                    </div>
                </div>)}
        </div>);
    }

    toggleAdvanced = (event) => {
        event.preventDefault();
        this.setState({advanced: !this.state.advanced});
    };

    renderShowAdvancedFieldsToggle() {
        if (!this.state.theme) {
            return null;
        }
        const text = this.state.advanced ? i18n.hide_advanced : i18n.show_advanced;
        const icon = `dashicons dashicons-arrow-${this.state.advanced ? 'up' : 'down'}`;
        return (<p><a className="advancedToggle" onClick={this.toggleAdvanced}>
            {text} <span className={icon}/>
        </a></p>);
    }

    ifTheme(component) {
        if(!this.state.theme){
            return null;
        }
        return component;
    }

    render() {
        return (
            <div className="App wrap">
                <h1>{i18n.header}</h1>
                <ThemeSelector onChange={this.selectTheme} theme={this.state.theme} themes={this.props.themes}/>
                {this.ifTheme(<Name onChange={this.updateThemeName} value={this.state.childName} />)}
                {this.renderShowAdvancedFieldsToggle()}
                {this.state.advanced ? this.renderExtraFilesField() : null}
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
