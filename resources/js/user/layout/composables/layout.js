import { computed, reactive } from 'vue';

const THEME_KEY = 'theme';

const getStoredTheme = () => {
    try {
        return localStorage.getItem(THEME_KEY);
    } catch (error) {
        return null;
    }
};

const setStoredTheme = (value) => {
    try {
        localStorage.setItem(THEME_KEY, value);
    } catch (error) {
        // ignore storage errors
    }
};

const applyThemeClass = (isDark) => {
    if (typeof document === 'undefined') return;
    document.documentElement.classList.toggle('app-dark', isDark);
};

const storedTheme = getStoredTheme();
const initialDark = storedTheme === 'dark';

const layoutConfig = reactive({
    darkTheme: initialDark,
    menuMode: 'static'
});

applyThemeClass(initialDark);

const layoutState = reactive({
    staticMenuInactive: false,
    overlayMenuActive: false,
    mobileMenuActive: false,
    menuHoverActive: false,
    activePath: null
});

export function useLayout() {
    const toggleDarkMode = () => {
        layoutConfig.darkTheme = !layoutConfig.darkTheme;
        applyThemeClass(layoutConfig.darkTheme);
        setStoredTheme(layoutConfig.darkTheme ? 'dark' : 'light');
    };

    const toggleMenu = () => {
        if (isDesktop()) {
            if (layoutConfig.menuMode === 'static') {
                layoutState.staticMenuInactive = !layoutState.staticMenuInactive;
            }

            if (layoutConfig.menuMode === 'overlay') {
                layoutState.overlayMenuActive = !layoutState.overlayMenuActive;
            }
        } else {
            layoutState.mobileMenuActive = !layoutState.mobileMenuActive;
        }
    };

    const hideMobileMenu = () => {
        layoutState.mobileMenuActive = false;
    };

    const isDesktop = () => window.innerWidth > 991;

    const isDarkTheme = computed(() => layoutConfig.darkTheme);
    const hasOpenOverlay = computed(() => layoutState.overlayMenuActive || layoutState.mobileMenuActive);

    return {
        layoutConfig,
        layoutState,
        isDarkTheme,
        toggleDarkMode,
        toggleMenu,
        hideMobileMenu,
        isDesktop,
        hasOpenOverlay
    };
}
