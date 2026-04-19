let recaptchaWidgetId = null;

/**
 * Renders the reCAPTCHA widget into a specific HTML container.
 * * @param {string} containerId - The ID of the HTML element (e.g., 'recaptcha-container')
 * @param {function} onVerify - Callback function triggered when the user passes the captcha. Receives the token.
 */
export function renderRecaptcha(containerId, onVerify = null) {
    const siteKey = import.meta.env.VITE_RECAPTCHA_SITE_KEY;
    
    const grecaptcha = window.grecaptcha?.enterprise || window.grecaptcha;

    if (!document.getElementById(containerId)) {
        return;
    }
    
    if (!siteKey || !grecaptcha) {
        console.error('reCAPTCHA script not loaded or site key missing.');
        return;
    }

    grecaptcha.ready(() => {
        // If it's already rendered, just reset it to avoid "already rendered" errors
        if (recaptchaWidgetId !== null) {
            resetRecaptcha();
            return;
        }

        try {
            recaptchaWidgetId = grecaptcha.render(containerId, {
                'sitekey': siteKey,
                'callback': onVerify, // Optional: handle the token immediately upon completion
                'theme': localStorage.getItem('theme'),     // Optional: 'light' or 'dark'
            });
        } catch (error) {
            console.error('Failed to render reCAPTCHA:', error);
        }
    });
}

/**
 * Resets the reCAPTCHA widget. 
 * Use this after a form submission or if the token expires.
 */
export function resetRecaptcha() {
    const grecaptcha = window.grecaptcha?.enterprise || window.grecaptcha;

    if (grecaptcha && recaptchaWidgetId !== null) {
        grecaptcha.reset(recaptchaWidgetId);
    }
}

/**
 * Retrieves the current token from the rendered widget.
 * * @returns {string} The reCAPTCHA token, or an empty string if not completed.
 */
export function getRecaptchaToken() {
    const grecaptcha = window.grecaptcha?.enterprise || window.grecaptcha;

    if (!grecaptcha || recaptchaWidgetId === null) {
        return '';
    }

    // getResponse grabs the token from the specific widget we rendered
    return grecaptcha.getResponse(recaptchaWidgetId);
}

/**
 * Clears the internal widget ID. 
 * Use this when the Vue component unmounts to prevent "element removed" errors.
 */
export function clearRecaptcha() {
    recaptchaWidgetId = null;
}