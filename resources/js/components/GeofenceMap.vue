<script setup lang="ts">
import 'leaflet/dist/leaflet.css';
import L from 'leaflet';
import { onBeforeUnmount, onMounted, ref, watch } from 'vue';

/**
 * Drag the pin or click the map to move the clock-in point; the circle is the
 * live geofence staff are measured against.
 */
const props = defineProps<{
    latitude: number | null;
    longitude: number | null;
    radius: number;
}>();

const emit = defineEmits<{
    move: [payload: { latitude: number; longitude: number }];
}>();

const container = ref<HTMLElement | null>(null);

let map: L.Map | null = null;
let marker: L.Marker | null = null;
let circle: L.Circle | null = null;

const DEFAULT_CENTER: L.LatLngExpression = [6.4281, 3.4219]; // Victoria Island, Lagos

// A CSS pin instead of Leaflet's bundled PNG, so it inherits the palette.
const pinIcon = L.divIcon({
    className: '',
    iconSize: [26, 26],
    iconAnchor: [13, 13],
    html: `
        <span style="
            display:grid;place-items:center;width:26px;height:26px;
            border-radius:50%;
            background:var(--beacon);
            border:3px solid var(--panel);
            box-shadow:0 2px 10px rgba(0,0,0,.45);
            cursor:grab;
        ">
            <span style="width:7px;height:7px;border-radius:50%;background:var(--panel)"></span>
        </span>`,
});

function currentCenter(): L.LatLngExpression {
    return props.latitude !== null && props.longitude !== null
        ? [props.latitude, props.longitude]
        : DEFAULT_CENTER;
}

function place(lat: number, lng: number, emitChange = true) {
    marker?.setLatLng([lat, lng]);
    circle?.setLatLng([lat, lng]);

    if (emitChange) {
        emit('move', {
            latitude: Number(lat.toFixed(7)),
            longitude: Number(lng.toFixed(7)),
        });
    }
}

/** Fit the viewport so the whole fence is comfortably visible. */
function frame() {
    if (!map || !circle) {
        return;
    }

    map.fitBounds(circle.getBounds(), { padding: [40, 40], maxZoom: 18 });
}

/**
 * Leaflet measures its container on mount, so a map revealed inside a modal
 * has to be told to re-measure once the panel has finished animating.
 */
function refresh() {
    map?.invalidateSize();
}

defineExpose({ frame, refresh });

onMounted(() => {
    if (!container.value) {
        return;
    }

    map = L.map(container.value, {
        center: currentCenter(),
        zoom: 17,
        zoomControl: true,
        scrollWheelZoom: false,
        attributionControl: true,
    });

    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        maxZoom: 19,
        attribution: '&copy; OpenStreetMap',
    }).addTo(map);

    circle = L.circle(currentCenter(), {
        radius: props.radius,
        color: 'var(--beacon)',
        weight: 2,
        fillColor: 'var(--beacon)',
        fillOpacity: 0.12,
    }).addTo(map);

    marker = L.marker(currentCenter(), {
        icon: pinIcon,
        draggable: true,
        keyboard: true,
        title: 'Clock-in point',
    }).addTo(map);

    marker.on('drag', (event) => {
        const { lat, lng } = (event.target as L.Marker).getLatLng();
        circle?.setLatLng([lat, lng]);
    });

    marker.on('dragend', (event) => {
        const { lat, lng } = (event.target as L.Marker).getLatLng();
        place(lat, lng);
    });

    map.on('click', (event: L.LeafletMouseEvent) => {
        place(event.latlng.lat, event.latlng.lng);
    });

    // Scroll zoom only once the map has focus, so the page still scrolls.
    map.on('focus', () => map?.scrollWheelZoom.enable());
    map.on('blur', () => map?.scrollWheelZoom.disable());

    if (props.latitude === null) {
        place(DEFAULT_CENTER[0] as number, DEFAULT_CENTER[1] as number);
    }
});

onBeforeUnmount(() => {
    map?.remove();
    map = null;
});

watch(
    () => props.radius,
    (radius) => circle?.setRadius(radius),
);

watch(
    () => [props.latitude, props.longitude],
    ([lat, lng]) => {
        if (lat === null || lng === null) {
            return;
        }

        const current = marker?.getLatLng();

        // Only re-centre when the change came from outside the map itself.
        if (
            current &&
            Math.abs(current.lat - lat) < 1e-7 &&
            Math.abs(current.lng - lng) < 1e-7
        ) {
            return;
        }

        place(lat, lng, false);
        map?.panTo([lat, lng]);
    },
);
</script>

<template>
    <div
        ref="container"
        class="h-[380px] w-full rounded-xl border border-line bg-sunken"
        tabindex="0"
        aria-label="Clock-in location map. Click to move the pin."
    />
</template>
