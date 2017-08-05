window.ChildThemify = window.ChildThemify || {};

const rest_url = window.ChildThemify.rest || '';
const rest_nonce = window.ChildThemify.nonce || '';
const theme_list = window.ChildThemify.themes || [];
const current_user = window.ChildThemify.current_user || '';

const settings = {current_user, rest_url, rest_nonce, theme_list};

export {settings};
