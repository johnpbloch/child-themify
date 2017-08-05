import React, {Component} from 'react';
import PropTypes from 'prop-types';
import 'react-select/dist/react-select.min.css';
import {i18n, Data} from './Utils';
import './App.css';
import {ExtraFiles, Input, ThemeSelector} from "./Fields";

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

        this.themeData = {};
    }

    static formatSlug(name) {
        let slug = name;
        slug = slug.toLowerCase();
        slug = slug.replace(/[^\w\s-]/g, '');
        slug = slug.replace(/\s+/g, '-');
        slug = slug.replace(/([_-])\1+/g, '$1');

        return slug;
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

    updateThemeName = (name) => {
        const childName = name;
        const childSlug = App.formatSlug(childName);

        this.setState({childName, childSlug});
    };

    toggleAdvanced = (event) => {
        event.preventDefault();
        this.setState({advanced: !this.state.advanced});
    };

    renderShowAdvancedFieldsToggle = () => {
        const text = this.state.advanced ? i18n.hide_advanced : i18n.show_advanced;
        const icon = `dashicons dashicons-arrow-${this.state.advanced ? 'up' : 'down'}`;
        return (<p><a className="advancedToggle" onClick={this.toggleAdvanced}>
            {text} <span className={icon}/>
        </a></p>);
    };

    updateField(field, value) {
        this.setState({[field]: value});
    }

    ifTheme(renderer) {
        if (!this.state.theme) {
            return null;
        }
        return renderer();
    }

    ifAdvanced(renderer) {
        if (!this.state.advanced) {
            return null;
        }

        return this.ifTheme(renderer);
    }

    render() {
        return (
            <div className="App wrap">
                <h1>{i18n.header}</h1>
                <ThemeSelector onChange={this.selectTheme} theme={this.state.theme} themes={this.props.themes}/>
                {this.ifTheme(() => <Input
                    label={i18n.name_label}
                    onChange={this.updateThemeName}
                    value={this.state.childName}/>)}
                {this.ifTheme(this.renderShowAdvancedFieldsToggle)}
                {this.ifAdvanced(() => <ExtraFiles
                    dataLoading={this.state.dataLoading}
                    onChange={data => this.updateField('themeFiles', data)}
                    selectedFiles={this.state.themeFiles}
                    themeFiles={this.themeData.files || []}/>)}
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
