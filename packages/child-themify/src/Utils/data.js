import axios from 'axios';
import {settings} from "./settings"

const {rest_url, rest_nonce} = settings;

export class Data {

    static themeData(theme) {
        return axios.get(`${rest_url}/theme-data/${theme}`, {
            headers: {'X-WP-Nonce': rest_nonce}
        });
    }

}
