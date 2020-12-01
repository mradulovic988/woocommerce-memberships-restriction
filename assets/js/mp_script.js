"use strict";

/**
 * Redirection only once after user is log in to the /my-account page
 *
 * @author FixRunner dev: Marko R <marko@fixrunner.com>
 * @package membership-plan
 * @version 1.0.0
 * @since 1.0.0
 * @param {Element} logInBtn Log in button on login /my-account page
 * @param {Element} isPage Declaring /my-account page
 */
const logInBtn = document.querySelector('button.woocommerce-form-login__submit');
const isPage = window.document.href = 'https://lisbonplayers.wpengine.com/my-account/';

logInBtn ? logInBtn.addEventListener('click', () => localStorage.setItem('justLogin', 1)) : 0;

if (isPage == 'https://lisbonplayers.wpengine.com/my-account/' && localStorage.getItem('justLogin') && document.body.classList.contains('member-logged-in')) {
    localStorage.removeItem('justLogin');
    window.location.href = 'https://lisbonplayers.wpengine.com/members/';
}