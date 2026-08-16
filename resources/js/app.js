import axios from 'axios';

window.axios = axios;

window.axios.defaults.headers.common[
    'X-Requested-With'
] = 'XMLHttpRequest';


/*
|--------------------------------------------------------------------------
| LiteraHub Global JavaScript
|--------------------------------------------------------------------------
*/


/*
|--------------------------------------------------------------------------
| Theme
|--------------------------------------------------------------------------
*/

const getPreferredTheme = () => {

    const saved =
        localStorage.getItem('literahub-theme');

    if (saved) {
        return saved;
    }

    return window.matchMedia(
        '(prefers-color-scheme: dark)'
    ).matches
        ? 'dark'
        : 'light';
};


const applyTheme = (theme) => {

    document.documentElement.dataset.theme =
        theme;

    localStorage.setItem(
        'literahub-theme',
        theme
    );
};


window.LiteraHub = {

    setTheme(theme) {
        applyTheme(theme);
    },

    toggleTheme() {

        const current =
            document.documentElement.dataset.theme ??
            getPreferredTheme();

        applyTheme(
            current === 'dark'
                ? 'light'
                : 'dark'
        );
    },

};


/*
|--------------------------------------------------------------------------
| Auto-dismiss Alerts
|--------------------------------------------------------------------------
*/

const setupAutoDismissAlerts = () => {

    document
        .querySelectorAll('[data-auto-dismiss]')
        .forEach((alert) => {

            const delay =
                parseInt(
                    alert.dataset.autoDismiss ?? '5000',
                    10
                );

            window.setTimeout(() => {

                alert.classList.add('is-dismissing');

                window.setTimeout(
                    () => alert.remove(),
                    200
                );

            }, delay);

        });

};


/*
|--------------------------------------------------------------------------
| Confirm Destructive Actions
|--------------------------------------------------------------------------
*/

const setupConfirmActions = () => {

    document
        .querySelectorAll('[data-confirm]')
        .forEach((element) => {

            element.addEventListener(
                'click',
                (event) => {

                    const message =
                        element.dataset.confirm ??
                        'Are you sure?';

                    if (!window.confirm(message)) {
                        event.preventDefault();
                    }

                }
            );

        });

};


/*
|--------------------------------------------------------------------------
| Mobile Navigation
|--------------------------------------------------------------------------
*/

const setupMobileNavigation = () => {

    const toggle =
        document.querySelector(
            '[data-nav-toggle]'
        );

    const close =
        document.querySelector(
            '[data-nav-close]'
        );

    const navigation =
        document.querySelector(
            '[data-mobile-nav]'
        );

    const backdrop =
        document.querySelector(
            '[data-nav-backdrop]'
        );

    if (
        !toggle ||
        !navigation ||
        !backdrop
    ) {
        return;
    }


    const openNavigation = () => {

        navigation.classList.add(
            'is-open'
        );

        backdrop.classList.add(
            'is-open'
        );

        toggle.setAttribute(
            'aria-expanded',
            'true'
        );

        navigation.setAttribute(
            'aria-hidden',
            'false'
        );

        document.body.classList.add(
            'navigation-open'
        );

    };


    const closeNavigation = () => {

        navigation.classList.remove(
            'is-open'
        );

        backdrop.classList.remove(
            'is-open'
        );

        toggle.setAttribute(
            'aria-expanded',
            'false'
        );

        navigation.setAttribute(
            'aria-hidden',
            'true'
        );

        document.body.classList.remove(
            'navigation-open'
        );

    };


    toggle.addEventListener(
        'click',
        () => {

            const isOpen =
                navigation.classList.contains(
                    'is-open'
                );

            if (isOpen) {
                closeNavigation();
            } else {
                openNavigation();
            }

        }
    );


    close?.addEventListener(
        'click',
        closeNavigation
    );


    backdrop.addEventListener(
        'click',
        closeNavigation
    );


    navigation
        .querySelectorAll('a')
        .forEach((link) => {

            link.addEventListener(
                'click',
                closeNavigation
            );

        });


    document.addEventListener(
        'keydown',
        (event) => {

            if (
                event.key === 'Escape' &&
                navigation.classList.contains(
                    'is-open'
                )
            ) {
                closeNavigation();
            }

        }
    );


    window.addEventListener(
        'resize',
        () => {

            if (
                window.innerWidth >= 1024
            ) {
                closeNavigation();
            }

        }
    );

};


/*
|--------------------------------------------------------------------------
| Global Bootstrap
|--------------------------------------------------------------------------
*/

const initializeLiteraHub = () => {

    applyTheme(
        getPreferredTheme()
    );

    setupAutoDismissAlerts();

    setupConfirmActions();

    setupMobileNavigation();

};


if (
    document.readyState === 'loading'
) {

    document.addEventListener(
        'DOMContentLoaded',
        initializeLiteraHub
    );

} else {

    initializeLiteraHub();

}