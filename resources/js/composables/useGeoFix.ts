import { readonly, ref } from 'vue';

export type GeoFix = {
    latitude: number;
    longitude: number;
    accuracy: number;
};

export type GeoState = 'idle' | 'locating' | 'ready' | 'denied' | 'unavailable';

/**
 * A one-shot, high-accuracy position request. The punch flow needs an explicit
 * "get me a fix right now" call rather than a standing watch, so the user sees
 * the acquisition happen and understands what is being sent.
 */
export function useGeoFix() {
    const state = ref<GeoState>('idle');
    const fix = ref<GeoFix | null>(null);
    const error = ref<string | null>(null);

    async function acquire(timeout = 12000): Promise<GeoFix | null> {
        if (!('geolocation' in navigator)) {
            state.value = 'unavailable';
            error.value = 'This device cannot report its location.';

            return null;
        }

        state.value = 'locating';
        error.value = null;

        return new Promise((resolve) => {
            navigator.geolocation.getCurrentPosition(
                (position) => {
                    fix.value = {
                        latitude: position.coords.latitude,
                        longitude: position.coords.longitude,
                        accuracy: Math.round(position.coords.accuracy),
                    };
                    state.value = 'ready';
                    resolve(fix.value);
                },
                (failure) => {
                    if (failure.code === failure.PERMISSION_DENIED) {
                        state.value = 'denied';
                        error.value =
                            'Location access is blocked. Allow it in your browser settings to clock in.';
                    } else if (failure.code === failure.TIMEOUT) {
                        state.value = 'unavailable';
                        error.value =
                            'Finding your location took too long. Move somewhere with a clearer sky view.';
                    } else {
                        state.value = 'unavailable';
                        error.value = 'Your location could not be determined.';
                    }

                    resolve(null);
                },
                { enableHighAccuracy: true, timeout, maximumAge: 0 },
            );
        });
    }

    function reset() {
        state.value = 'idle';
        fix.value = null;
        error.value = null;
    }

    return {
        state: readonly(state),
        fix: readonly(fix),
        error: readonly(error),
        acquire,
        reset,
    };
}
