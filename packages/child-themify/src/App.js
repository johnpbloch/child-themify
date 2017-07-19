import React, {Component} from 'react';
import PropTypes from 'prop-types';
import Select from 'react-select';
import ReactLoading from 'react-loading';
import 'react-select/dist/react-select.min.css';
import {i18n, Data} from './Utils';
import './App.css';

class App extends Component {
    constructor(props) {
        super(props);

        this.state = {
            theme: undefined,
            childName: '',
            filesLoading: false,
            themeFiles: [],
        };
    }

    selectTheme = (selected) => {
        this.setState({
            theme: selected ? selected.value : undefined,
            childName: '',
            filesLoading: true,
            themeFiles: [],
        });
        if (selected) {
            Data.themeFiles(selected.value)
                .then(data => {
                    this.themeFiles = Object.keys(data.data.files);
                    this.setState({filesLoading: false});
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

    updateThemeName = (event) => {
        const childName = event.target.value;
        const childSlug = App.formatSlug(childName);

        this.setState({childName, childSlug});
    };

    renderThemeSelector() {
        return (<div className="ctf-form-field">
            <label>{i18n.theme_select_label}</label>
            <Select
                name="form-field-name"
                options={this.props.themes}
                onChange={this.selectTheme}
                placeholder={i18n.theme_placeholder}
                value={this.state.theme}/>
        </div>)
    }

    renderNameField() {
        if (!this.state.theme) {
            return null;
        }
        return (<div className="ctf-form-field">
            <label>{i18n.name_label}</label>
            <input
                className="widefat"
                name="theme-name"
                onChange={this.updateThemeName}
                type="text"
                value={this.state.childName}/>
        </div>)
    }

    renderExtraFilesField() {
        if (!this.state.theme) {
            return null;
        }
        return (<div className="ctf-form-field">
            <label>{i18n.files_label}</label>
            {this.state.filesLoading
                ? <ReactLoading type="bubbles" color="#333" delay="0"/>
                : (<div>
                    <p>{i18n.files_description}</p>
                    <div className="ctf-extra-files">
                        {this.themeFiles.map(file => {
                            const isChecked = -1 !== this.state.themeFiles.indexOf(file);
                            const updateState = (event) => {
                                if (event.target.checked) {
                                    this.setState({themeFiles: [...this.state.themeFiles, event.target.value]});
                                } else {
                                    this.setState({themeFiles: this.state.filter(i => i !== event.target.value)});
                                }
                            };
                            return (<p><label>
                                <input checked={isChecked} onChange={updateState} type="checkbox" value={file}/>
                                {file}
                            </label></p>);
                        })}
                    </div>
                </div>)}
        </div>);
    }

    render() {
        return (
            <div className="App wrap">
                <h1>{i18n.header}</h1>
                {this.renderThemeSelector()}
                {this.renderNameField()}
                {this.renderExtraFilesField()}
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
