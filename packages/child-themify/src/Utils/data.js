import axios from 'axios';
import {settings} from "./settings"

const {rest_url, rest_nonce} = settings;

export class Data {

    static themeFiles(theme) {
        return axios.get(`${rest_url}/theme-files/${theme}`, {
            headers: {'X-WP-Nonce': rest_nonce}
        });
    }

}
