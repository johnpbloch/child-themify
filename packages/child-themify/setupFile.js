// import {JSDOM} from 'jsdom';
import state from './__mocks__/state.json';

// const dom = new JSDOM('<!doctype html><html><body><div id="ctfAppRoot"></div></body></html>');
// global.window = dom.window;
// global.document = dom.window.document;
global.window.ChildThemify = state;
