import React, {Component} from 'react';
import PropTypes from 'prop-types';
import ReactLoading from 'react-loading';
import {debounce} from 'lodash';
import {sprintf} from 'sprintf-js';
import 'react-select/dist/react-select.min.css';
import {i18n, Data, settings} from './Utils';
import './App.css';
import {ExtraFiles, Input, ThemeSelector} from './Fields';

class App extends Component {

    constructor(props) {
        super(props);

        this.state = {
            advanced: false,
            author: settings.current_user,
            checkingSlug: false,
            childName: '',
            creatingTheme: false,
            dataLoading: false,
            theme: '',
            themeFiles: [],
            validSlug: null,
        };

        this.themeData = {};

        this.selectTheme = this.selectTheme.bind(this);
        this.updateThemeName = this.updateThemeName.bind(this);
        this.toggleAdvanced = this.toggleAdvanced.bind(this);
        this.renderShowAdvancedFieldsToggle = this.renderShowAdvancedFieldsToggle.bind(this);
        this.renderNameField = this.renderNameField.bind(this);
        this.createTheme = this.createTheme.bind(this);
        this.checkChildSlug = debounce(this.realCheckChildSlug.bind(this), 1500);
    }

    static formatSlug(name) {
        let slug = name;
        slug = slug.toLowerCase();
        slug = slug.replace(/[^\w\s-]/g, '');
        slug = slug.replace(/\s+/g, '-');
        slug = slug.replace(/([_-])\1+/g, '$1');

        return slug;
    }

    selectTheme(selected) {
        this.setState({
            theme: selected ? selected.value : '',
            childName: '',
            dataLoading: true,
            themeFiles: [],
        });
        /* istanbul ignore else */
        if (selected) {
            Data.themeData(selected.value)
                .then(data => {
                    this.themeData = {
                        files: Object.keys(data.data.files),
                    };
                    this.setState({dataLoading: false});
                }).catch(() => {});
        }
    };

    updateThemeName(name) {
        const childName = name;
        const childSlug = App.formatSlug(childName);
        const checkingSlug = !!childSlug;
        this.setState({childName, childSlug, checkingSlug, validSlug: null});
        /* istanbul ignore else */
        if (checkingSlug) {
            this.checkChildSlug();
        }
    };

    toggleAdvanced(event) {
        event.preventDefault();
        this.setState({advanced: !this.state.advanced});
    };

    renderShowAdvancedFieldsToggle() {
        const text = this.state.advanced ? i18n.hide_advanced : i18n.show_advanced;
        const icon = `dashicons dashicons-arrow-${this.state.advanced ? 'up' : 'down'}`;
        return (<p><a className="advancedToggle" onClick={this.toggleAdvanced}>
            {text} <span className={icon}/>
        </a></p>);
    };

    realCheckChildSlug() {
        Data.themeData(this.state.childSlug)
            .then(() => {
                this.setState({checkingSlug: false, validSlug: false});
            }, (error) => {
                this.setState({
                    checkingSlug: false,
                    validSlug: error.response && error.response.status === 404
                });
            }).catch(() => {});
    }

    getErrorIndicatorIcon() {
        /* istanbul ignore else */
        if (this.state.validSlug === true) {
            return <span className="dashicons dashicons-yes"/>;
        }
        /* istanbul ignore else */
        if (this.state.validSlug === false) {
            return <span className="dashicons dashicons-no"/>;
        }
        return null;
    }

    renderNameField() {
        const error = this.state.validSlug === false;
        return (
            <div className="name-field-wrapper">
                <Input label={i18n.name_label} onChange={this.updateThemeName} value={this.state.childName}/>
                <div className="name-field-status-indicator">
                    {this.state.checkingSlug ?
                        <ReactLoading color="#333" delay={0} height="20px" type="spin" width="20px"/> :
                        this.getErrorIndicatorIcon()}
                </div>
                {error ? sprintf(i18n.invalid_theme, `"${this.state.childSlug}"`) : null}
            </div>
        );
    };

    /* istanbul ignore next */
    checkChildSlug() {
    }

    createTheme() {
    }

    updateField(field, value) {
        this.setState({[field]: value});
    }

    ifTheme(renderer) {
        /* istanbul ignore else */
        if (!this.state.theme) {
            return null;
        }
        return renderer();
    }

    ifAdvanced(renderer) {
        /* istanbul ignore else */
        if (!this.state.advanced) {
            return null;
        }

        return this.ifTheme(renderer);
    }

    isSubmitDisabled() {
        return ((
            !this.state.theme ||
            !this.state.childSlug ||
            !this.state.validSlug
        ) || this.state.creatingTheme);
    }

    render() {
        const {ready, working} = i18n.create_button;
        return (
            <div className="App wrap">
                <h1>{i18n.header}</h1>
                <ThemeSelector onChange={this.selectTheme} theme={this.state.theme} themes={this.props.themes}/>
                {this.ifTheme(this.renderNameField)}
                {this.ifTheme(this.renderShowAdvancedFieldsToggle)}
                {this.ifAdvanced(() => <Input
                    label={i18n.author_label}
                    onChange={data => this.updateField('author', data)}
                    value={this.state.author}/>)}
                {this.ifAdvanced(() => <ExtraFiles
                    dataLoading={this.state.dataLoading}
                    onChange={data => this.updateField('themeFiles', data)}
                    selectedFiles={this.state.themeFiles}
                    themeFiles={this.themeData.files || []}/>)}
                <p className="submit">
                    <input
                        className="button button-primary button-large"
                        disabled={this.isSubmitDisabled()}
                        onClick={this.createTheme}
                        type="submit"
                        value={this.state.creatingTheme ? working : ready}/>
                </p>

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
