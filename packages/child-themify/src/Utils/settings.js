window.ChildThemify = window.ChildThemify || /* istanbul ignore next */ {};

const rest_url = window.ChildThemify.rest || /* istanbul ignore next */ '';
const rest_nonce = window.ChildThemify.nonce || /* istanbul ignore next */ '';
const theme_list = window.ChildThemify.themes || /* istanbul ignore next */ [];
const current_user = window.ChildThemify.current_user || /* istanbul ignore next */ '';
const credentials = window.ChildThemify.creds || /* istanbul ignore next */ {};

const settings = {current_user, rest_url, rest_nonce, theme_list, credentials};

export {settings};
