import { useQuery } from '@tanstack/react-query';
import { useParams, Link } from '@tanstack/react-router';
import {
  BedDouble,
  Bath,
  Maximize,
  MapPin,
  Compass,
  DoorOpen,
  Phone,
  Smartphone,
  Building2,
} from 'lucide-react';
import { fetchProperty } from '@/features/property/api';
import { FullSpinner } from '@/components/ui/spinner';
import { Badge } from '@/components/ui/badge';
import { Card, CardContent } from '@/components/ui/card';
import { Carousel } from '@/components/Carousel';
import { PropertyMap } from '@/components/PropertyMap';
import { FavouriteButton } from '@/components/FavouriteButton';
import { EmptyState } from '@/components/EmptyState';
import { useEnums, labelFor } from '@/lib/enums';
import { formatArea } from '@/lib/utils';

export function PropertyDetailPage() {
  const { slug } = useParams({ strict: false }) as { slug: string };
  const enums = useEnums();
  const { data, isLoading, isError } = useQuery({
    queryKey: ['property', slug],
    queryFn: ({ signal }) => fetchProperty(slug, signal),
  });

  if (isLoading) return <FullSpinner />;
  if (isError || !data) {
    return (
      <div className="container py-12">
        <EmptyState
          icon={Building2}
          title="Property not found"
          description="This listing may have been removed."
          action={
            <Link to="/properties" className="text-sm font-medium text-primary hover:underline">
              Browse all properties
            </Link>
          }
        />
      </div>
    );
  }

  const p = data;
  const facts = [
    p.bedRooms != null && { icon: BedDouble, label: 'Bedrooms', value: p.bedRooms },
    p.bathRooms != null && { icon: Bath, label: 'Bathrooms', value: p.bathRooms },
    { icon: Maximize, label: 'Area', value: formatArea(p.area, p.areaUnit) },
    p.rooms != null && { icon: DoorOpen, label: 'Rooms', value: p.rooms },
    p.direction && { icon: Compass, label: 'Facing', value: labelFor(enums.direction, p.direction) },
  ].filter(Boolean) as { icon: typeof BedDouble; label: string; value: string | number }[];

  const hasCoords = p.latitude != null && p.longitude != null;

  return (
    <div className="container py-8">
      <nav className="mb-4 text-sm text-muted-foreground">
        <Link to="/properties" className="hover:text-foreground">Properties</Link>
        <span className="mx-2">/</span>
        <span className="text-foreground">{p.title}</span>
      </nav>

      <div className="grid gap-8 lg:grid-cols-3">
        <div className="space-y-6 lg:col-span-2">
          <Carousel images={p.images} title={p.title} />

          <div>
            <div className="flex flex-wrap items-start justify-between gap-4">
              <div>
                <div className="flex items-center gap-2">
                  <Badge variant={p.listingType === 'rent' ? 'accent' : 'default'} className="capitalize">
                    For {p.listingType}
                  </Badge>
                  <Badge variant="muted">{labelFor(enums.propertyType, p.type)}</Badge>
                  <Badge variant="muted">{labelFor(enums.propertyStatus, p.status)}</Badge>
                </div>
                <h1 className="mt-3 text-3xl font-bold">{p.title}</h1>
                {p.city && (
                  <p className="mt-1 flex items-center gap-1 text-muted-foreground">
                    <MapPin className="h-4 w-4" />
                    {[p.city, p.state, p.country].filter(Boolean).join(', ')}
                  </p>
                )}
              </div>
              <div className="text-right">
                <div className="text-3xl font-bold text-primary">{p.priceFormatted}</div>
                <FavouriteButton
                  propertyId={p.id}
                  favourited={p.isFavourited}
                  variant="inline"
                  className="mt-2"
                />
              </div>
            </div>
          </div>

          <Card>
            <CardContent className="grid grid-cols-2 gap-4 p-6 sm:grid-cols-3 md:grid-cols-5">
              {facts.map((f) => (
                <div key={f.label} className="flex flex-col items-center rounded-lg bg-secondary/50 p-4 text-center">
                  <f.icon className="h-5 w-5 text-primary" />
                  <span className="mt-2 font-semibold">{f.value}</span>
                  <span className="text-xs text-muted-foreground">{f.label}</span>
                </div>
              ))}
            </CardContent>
          </Card>

          <div>
            <h2 className="mb-2 text-xl font-semibold">Description</h2>
            <p className="whitespace-pre-line leading-relaxed text-muted-foreground">{p.description}</p>
          </div>

          <div>
            <h2 className="mb-3 text-xl font-semibold">Location</h2>
            {hasCoords ? (
              <div className="h-[360px] overflow-hidden rounded-lg border">
                <PropertyMap
                  points={[{ id: p.id, lat: p.latitude!, lng: p.longitude!, label: p.title }]}
                  center={[p.latitude!, p.longitude!]}
                  zoom={15}
                />
              </div>
            ) : (
              <p className="text-sm text-muted-foreground">
                Map coordinates not provided
                {p.latitude != null && p.longitude != null
                  ? ` (${p.latitude}, ${p.longitude})`
                  : '.'}
              </p>
            )}
          </div>
        </div>

        {/* Agent sidebar */}
        <aside>
          <Card className="sticky top-20">
            <CardContent className="p-6">
              <h3 className="mb-4 text-sm font-semibold uppercase tracking-wide text-muted-foreground">
                Listed by
              </h3>
              <Link to="/agents/$id" params={{ id: String(p.agent.id) }} className="flex items-center gap-3">
                {p.agent.avatarUrl ? (
                  <img src={p.agent.avatarUrl} alt="" className="h-14 w-14 rounded-full object-cover" />
                ) : (
                  <span className="flex h-14 w-14 items-center justify-center rounded-full bg-primary text-xl font-semibold text-primary-foreground">
                    {p.agent.name.charAt(0).toUpperCase()}
                  </span>
                )}
                <div>
                  <div className="font-semibold">{p.agent.name}</div>
                  {p.agent.city && <div className="text-sm text-muted-foreground">{p.agent.city}</div>}
                </div>
              </Link>
              <div className="mt-4 space-y-2 text-sm">
                {p.agent.phone && (
                  <a href={`tel:${p.agent.phone}`} className="flex items-center gap-2 text-muted-foreground hover:text-foreground">
                    <Phone className="h-4 w-4" /> {p.agent.phone}
                  </a>
                )}
                {p.agent.mobile && (
                  <a href={`tel:${p.agent.mobile}`} className="flex items-center gap-2 text-muted-foreground hover:text-foreground">
                    <Smartphone className="h-4 w-4" /> {p.agent.mobile}
                  </a>
                )}
              </div>
            </CardContent>
          </Card>
        </aside>
      </div>
    </div>
  );
}
