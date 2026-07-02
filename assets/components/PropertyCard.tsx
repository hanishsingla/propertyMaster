import { Link } from '@tanstack/react-router';
import { BedDouble, Bath, Maximize, MapPin } from 'lucide-react';
import type { PropertyCard as PropertyCardType } from '@/lib/types';
import { Badge } from '@/components/ui/badge';
import { FavouriteButton } from './FavouriteButton';
import { formatArea } from '@/lib/utils';

export function PropertyCard({ property }: { property: PropertyCardType }) {
  return (
    <Link
      to="/properties/$slug"
      params={{ slug: property.slug }}
      className="group flex flex-col overflow-hidden rounded-lg border bg-card shadow-sm transition-shadow hover:shadow-md focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring"
    >
      <div className="relative aspect-[4/3] overflow-hidden bg-muted">
        {property.coverImageUrl ? (
          <img
            src={property.coverImageUrl}
            alt={property.title}
            loading="lazy"
            className="h-full w-full object-cover transition-transform duration-300 group-hover:scale-105"
          />
        ) : (
          <div className="flex h-full w-full items-center justify-center text-muted-foreground">
            <MapPin className="h-10 w-10 opacity-30" />
          </div>
        )}
        <div className="absolute left-3 top-3 flex gap-2">
          <Badge variant={property.listingType === 'rent' ? 'accent' : 'default'} className="capitalize">
            For {property.listingType}
          </Badge>
          {property.isFeatured && <Badge variant="secondary">Featured</Badge>}
        </div>
        <FavouriteButton
          propertyId={property.id}
          favourited={property.isFavourited}
          className="absolute right-3 top-3"
        />
      </div>

      <div className="flex flex-1 flex-col p-4">
        <div className="text-xl font-bold text-primary">{property.priceFormatted}</div>
        <h3 className="mt-1 line-clamp-1 font-semibold text-foreground">{property.title}</h3>
        {property.city && (
          <p className="mt-1 flex items-center gap-1 text-sm text-muted-foreground">
            <MapPin className="h-3.5 w-3.5" />
            {property.city}
          </p>
        )}
        <div className="mt-auto flex items-center gap-4 pt-3 text-sm text-muted-foreground">
          {property.bedRooms != null && (
            <span className="flex items-center gap-1">
              <BedDouble className="h-4 w-4" /> {property.bedRooms}
            </span>
          )}
          {property.bathRooms != null && (
            <span className="flex items-center gap-1">
              <Bath className="h-4 w-4" /> {property.bathRooms}
            </span>
          )}
          <span className="flex items-center gap-1">
            <Maximize className="h-4 w-4" /> {formatArea(property.area, property.areaUnit)}
          </span>
        </div>
      </div>
    </Link>
  );
}
