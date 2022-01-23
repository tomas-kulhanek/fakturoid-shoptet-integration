import './styles/frontend.scss';

import naja from 'naja';

import LiveFormValidation from 'live-form-validation-es6';

window.LiveForm = LiveFormValidation.LiveForm;
window.Nette = LiveFormValidation.Nette;

document.addEventListener('DOMContentLoaded', naja.initialize.bind(naja));
