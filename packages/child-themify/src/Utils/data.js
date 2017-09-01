import axios from 'axios';
import {settings} from "./settings"

const {rest_url, rest_nonce} = settings;

export class Data {

    static themeData(theme) {
        return axios.get(`${rest_url}/theme-data/${theme}`, {
            headers: {'X-WP-Nonce': rest_nonce}
        });
    }

    static createTheme(
        slug,
        parent,
        name,
        author,
        extra_files,
        creds
    ) {
        return axios.post(`${rest_url}/create-theme`, {
            slug, parent, name, author, extra_files, creds
        }, {
            headers: {'X-WP-Nonce': rest_nonce}
        })
    }

}
