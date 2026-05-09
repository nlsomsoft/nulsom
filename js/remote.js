/**
 * Material floating button
 * By: Nobita
 * Repo and docs: https://github.com/nobitagit/material-floating-button
 *
 * License: MIT
 */
(function(window, document, undefined) {

    'use strict';

    /**
     * Some defaults
     */
    var clickOpt = 'click',
        hoverOpt = 'hover',
        toggleMethod = 'data-mfb-toggle',
        menuState = 'data-mfb-state',
        isOpen = 'open',
        isClosed = 'closed',
        mainButtonClass = 'mfb-component__button--main';

    /**
     * Internal references
     */
    var elemsToClick,
        elemsToHover,
        mainButton,
        target,
        currentState;

    /**
     * For every menu we need to get the main button and attach the appropriate evt.
     */

    function attachEvt(elems, evt) {
        for (var i = 0, len = elems.length; i < len; i++) {
            mainButton = elems[i].querySelector('.' + mainButtonClass);
            mainButton.addEventListener(evt, toggleButton, false);
        }
        var mainBtn = document.getElementsByClassName(mainButtonClass)[0];
        if(typeof(mainBtn) !== 'undefined') {
            mainBtn.style.visibility="visible";
        }
    }

    /**
     * Remove the hover option, set a click toggle and a default,
     * initial state of 'closed' to menu that's been targeted.
     */

    function replaceAttrs(elems) {
        for (var i = 0, len = elems.length; i < len; i++) {
            elems[i].setAttribute(toggleMethod, clickOpt);
            elems[i].setAttribute(menuState, isClosed);
        }
    }

    function getElemsByToggleMethod(selector) {
        return document.querySelectorAll('[' + toggleMethod + '="' + selector + '"]');
    }

    /**
     * The open/close action is performed by toggling an attribute
     * on the menu main element.
     *
     * First, check if the target is the menu itself. If it's a child
     * keep walking up the tree until we found the main element
     * where we can toggle the state.
     */

    function toggleButton(evt) {

        target = evt.target;
        while (target && !target.getAttribute(toggleMethod)) {
            target = target.parentNode;
            if (!target) {
                return;
            }
        }

        currentState = target.getAttribute(menuState) === isOpen ? isClosed : isOpen;

        target.setAttribute(menuState, currentState);

    }

    /**
     * On touch enabled devices we assume that no hover state is possible.
     * So, we get the menu with hover action configured and we set it up
     * in order to make it usable with tap/click.
     **/
    // if (window.Modernizr && Modernizr.touch) {
    //     elemsToHover = getElemsByToggleMethod(hoverOpt);
    //     replaceAttrs(elemsToHover);
    // }

    elemsToClick = getElemsByToggleMethod(clickOpt);

    attachEvt(elemsToClick, 'click');

})(window, document);

$(document).ready(function() {
    // Show or hide the sticky footer button
    $(window).scroll(function() {
        if ($(this).scrollTop() > 200) {
            $('.go-top, #goTop').fadeIn(200);
        } else {
            $('.go-top, #goTop').fadeOut(200);
        }
/*
        if(typeof $("#bo_vc").offset() != 'undefined') {
            if ($(this).scrollTop() > ($("#bo_vc").offset().top - screen.height)) {
                $('.go-comment').fadeOut(200);
            } else {
                $('.go-comment').fadeIn(200);
            }
        }
*/
    });
	// Animate the scroll to top
    $('.go-top, #goTop').click(function(event) {
        event.preventDefault();

        $('html, body').animate({scrollTop: 0}, 300);
    })
/*
    $('.go-comment, #goComment').click(function(event) {
        event.preventDefault();

        $('html, body').animate({scrollTop: $("#bo_vc").offset().top}, 300);
    })
*/
});