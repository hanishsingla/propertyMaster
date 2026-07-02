export interface EnumOption {
  value: string;
  label: string;
}

export interface Enums {
  gender: EnumOption[];
  listingType: EnumOption[];
  propertyType: EnumOption[];
  propertyCategory: EnumOption[];
  propertyStatus: EnumOption[];
  areaUnit: EnumOption[];
  direction: EnumOption[];
}

export interface UserSelf {
  id: number;
  email: string;
  name: string;
  roles: string[];
  isAgent: boolean;
  isVerified: boolean;
  gender: string | null;
  avatarUrl: string | null;
  phone: string | null;
  mobile: string | null;
  country: string | null;
  address: string | null;
  address2: string | null;
  city: string | null;
  state: string | null;
  zip: string | null;
  createdAt: string;
}

export interface Agent {
  id: number;
  name: string;
  isAgent: boolean;
  avatarUrl: string | null;
  phone: string | null;
  mobile: string | null;
  city: string | null;
}

export interface PropertyCard {
  id: number;
  title: string;
  slug: string;
  listingType: string;
  category: string;
  price: number;
  priceFormatted: string;
  currency: string;
  area: number;
  areaUnit: string;
  bedRooms: number | null;
  bathRooms: number | null;
  city: string | null;
  isFeatured: boolean;
  coverImageUrl: string | null;
  isFavourited: boolean;
}

export interface PropertyImage {
  id: number;
  url: string;
  sortOrder: number;
  isCover: boolean;
}

export interface PropertyDetail extends PropertyCard {
  description: string;
  type: string;
  status: string;
  rooms: number | null;
  direction: string | null;
  state: string | null;
  country: string | null;
  latitude: number | null;
  longitude: number | null;
  images: PropertyImage[];
  agent: Agent;
  createdAt: string;
}

export interface PaginationMeta {
  page: number;
  perPage: number;
  total: number;
  totalPages: number;
}

export interface Paginated<T> {
  data: T[];
  meta: PaginationMeta;
}

export interface Violation {
  field: string;
  message: string;
}
