import React, {Component} from 'react';
import PropTypes from 'prop-types';
import ReactLoading from 'react-loading';
import {i18n} from '../../Utils';
import {ExtraFile} from './ExtraFile';

class ExtraFiles extends Component {

    updateFile = (file, checked) => {
        if (checked) {
            this.props.onChange([...this.props.selectedFiles, file]);
        } else {
            this.props.onChange(this.props.selectedFiles.filter(i => i !== file));
        }
    };

    renderExtraFileField = (file) => {
        const isChecked = -1 !== this.props.selectedFiles.indexOf(file);
        return <ExtraFile checked={isChecked} key={file} name={file} onChange={this.updateFile}/>;
    };

    selectAll = event => {
        event.preventDefault();
        this.props.onChange(this.props.themeFiles);
    };

    selectNone = event => {
        event.preventDefault();
        this.props.onChange([]);
    };

    renderSelectAllNone() {
        return (
            <p>
                <a href="" onClick={this.selectAll}>{i18n.select_all}</a> |&nbsp;
                <a href="" onClick={this.selectNone}>{i18n.select_none}</a>
            </p>
        );
    }

    render() {
        return (<div className="ctf-form-field">
            <label>{i18n.files_label}</label>
            {this.props.dataLoading
                ? <ReactLoading type="bubbles" color="#333" delay="0"/>
                : (<div>
                    <p>{i18n.files_description}</p>
                    {this.renderSelectAllNone()}
                    <div className="ctf-extra-files">
                        {this.props.themeFiles.map(this.renderExtraFileField)}
                    </div>
                </div>)}
        </div>);
    }

}

ExtraFiles.defaultProps = {
    dataLoading: false,
    onChange: () => {
    },
    selectedFiles: [],
    themeFiles: [],
};

ExtraFiles.propTypes = {
    dataLoading: PropTypes.bool,
    onChange: PropTypes.func,
    selectedFiles: PropTypes.array,
    themeFiles: PropTypes.array,
};

export {ExtraFiles};
