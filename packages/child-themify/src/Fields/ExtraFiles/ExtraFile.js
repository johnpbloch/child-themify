import React, {Component} from 'react';
import PropTypes from 'prop-types';

class ExtraFile extends Component {

    handleChange = (event) => {
        this.props.onChange(event.target.value, event.target.checked);
    };

    render() {
        return (<p><label>
            <input
                checked={this.props.checked}
                onChange={this.handleChange}
                type="checkbox"
                value={this.props.name}/>
            {this.props.name}
        </label></p>);
    }

}

ExtraFile.defaultProps = {
    checked: false,
    onChange: () => {
    },
};

ExtraFile.propTypes = {
    checked: PropTypes.bool,
    name: PropTypes.string.isRequired,
    onChange: PropTypes.func,
};

export {ExtraFile};
