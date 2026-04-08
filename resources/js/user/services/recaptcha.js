export function getRecaptchaToken(action) {
    const siteKey = import.meta.env.VITE_RECAPTCHA_KEY;
    const grecaptcha = window.grecaptcha?.enterprise;

    if (!siteKey || !grecaptcha) {
        return Promise.resolve('');
    }

    return new Promise((resolve) => {
        grecaptcha.ready(async () => {
            try {
                const token = await grecaptcha.execute(siteKey, { action });
                resolve(token);
            } catch (error) {
                resolve('');
            }
        });
    });
}
