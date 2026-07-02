import { MapContainer, TileLayer, Marker, Popup } from 'react-leaflet';
import L from 'leaflet';
import 'leaflet/dist/leaflet.css';

// Fix default marker icons (bundlers break the default relative paths).
const icon = L.icon({
  iconUrl: 'https://unpkg.com/leaflet@1.9.4/dist/images/marker-icon.png',
  iconRetinaUrl: 'https://unpkg.com/leaflet@1.9.4/dist/images/marker-icon-2x.png',
  shadowUrl: 'https://unpkg.com/leaflet@1.9.4/dist/images/marker-shadow.png',
  iconSize: [25, 41],
  iconAnchor: [12, 41],
  popupAnchor: [1, -34],
  shadowSize: [41, 41],
});

export interface MapPoint {
  id: number;
  lat: number;
  lng: number;
  label: string;
  href?: string;
}

export function PropertyMap({
  points,
  center,
  zoom = 13,
  className = 'h-full w-full',
}: {
  points: MapPoint[];
  center?: [number, number];
  zoom?: number;
  className?: string;
}) {
  const valid = points.filter((p) => typeof p.lat === 'number' && typeof p.lng === 'number');
  const fallbackCenter: [number, number] = center ?? (valid[0] ? [valid[0].lat, valid[0].lng] : [20.5937, 78.9629]);

  if (valid.length === 0 && !center) {
    return (
      <div className="flex h-full min-h-[200px] w-full items-center justify-center rounded-lg border bg-muted text-sm text-muted-foreground">
        Location not available
      </div>
    );
  }

  return (
    <MapContainer center={fallbackCenter} zoom={zoom} className={className} scrollWheelZoom={false}>
      <TileLayer
        attribution='&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a>'
        url="https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png"
      />
      {valid.map((p) => (
        <Marker key={p.id} position={[p.lat, p.lng]} icon={icon}>
          <Popup>
            {p.href ? (
              <a href={p.href} className="font-medium text-primary hover:underline">
                {p.label}
              </a>
            ) : (
              <span className="font-medium">{p.label}</span>
            )}
          </Popup>
        </Marker>
      ))}
    </MapContainer>
  );
}
