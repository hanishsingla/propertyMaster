import { useForm } from 'react-hook-form';
import { z } from 'zod';
import type { PropertyDetail } from '@/lib/types';
import type { PropertyPayload } from './api';
import { useEnums } from '@/lib/enums';
import { Field } from '@/components/ui/label';
import { Input } from '@/components/ui/input';
import { Select } from '@/components/ui/select';
import { Textarea } from '@/components/ui/textarea';
import { Button } from '@/components/ui/button';

const emptyToUndef = (v: unknown) => (v === '' || v === null ? undefined : v);
const optionalInt = z.preprocess(emptyToUndef, z.coerce.number().int().nonnegative('Must be zero or more').optional());
const optionalFloat = z.preprocess(emptyToUndef, z.coerce.number().optional());

const schema = z.object({
  title: z.string().min(3, 'Title must be at least 3 characters'),
  description: z.string().min(10, 'Description must be at least 10 characters'),
  listingType: z.string().min(1, 'Required'),
  category: z.string().min(1, 'Required'),
  type: z.string().min(1, 'Required'),
  status: z.string().optional(),
  price: z.coerce.number().positive('Price must be greater than 0'),
  area: z.coerce.number().positive('Area must be greater than 0'),
  areaUnit: z.string().optional(),
  bedRooms: optionalInt,
  bathRooms: optionalInt,
  rooms: optionalInt,
  direction: z.string().optional(),
  city: z.string().min(1, 'City is required'),
  state: z.string().optional(),
  country: z.string().optional(),
  latitude: optionalFloat,
  longitude: optionalFloat,
  isFeatured: z.boolean().optional(),
});

type FormValues = {
  title: string;
  description: string;
  listingType: string;
  category: string;
  type: string;
  status: string;
  price: string;
  area: string;
  areaUnit: string;
  bedRooms: string;
  bathRooms: string;
  rooms: string;
  direction: string;
  city: string;
  state: string;
  country: string;
  latitude: string;
  longitude: string;
  isFeatured: boolean;
};

function toNum(v?: number): number | null {
  return v === undefined || Number.isNaN(v) ? null : v;
}

export interface PropertyFormHandle {
  submit: (payload: PropertyPayload) => Promise<void>;
}

export function PropertyForm({
  initial,
  submitting,
  onSubmit,
  submitLabel = 'Save property',
}: {
  initial?: PropertyDetail;
  submitting: boolean;
  onSubmit: (payload: PropertyPayload) => Promise<void>;
  submitLabel?: string;
}) {
  const enums = useEnums();
  const {
    register,
    handleSubmit,
    setError,
    formState: { errors },
  } = useForm<FormValues>({
    defaultValues: {
      title: initial?.title ?? '',
      description: initial?.description ?? '',
      listingType: initial?.listingType ?? '',
      category: initial?.category ?? '',
      type: initial?.type ?? '',
      status: initial?.status ?? '',
      // price comes back in paise; convert to rupees for editing
      price: initial ? String(Math.round(initial.price / 100)) : '',
      area: initial ? String(initial.area) : '',
      areaUnit: initial?.areaUnit ?? '',
      bedRooms: initial?.bedRooms != null ? String(initial.bedRooms) : '',
      bathRooms: initial?.bathRooms != null ? String(initial.bathRooms) : '',
      rooms: initial?.rooms != null ? String(initial.rooms) : '',
      direction: initial?.direction ?? '',
      city: initial?.city ?? '',
      state: initial?.state ?? '',
      country: initial?.country ?? '',
      latitude: initial?.latitude != null ? String(initial.latitude) : '',
      longitude: initial?.longitude != null ? String(initial.longitude) : '',
      isFeatured: initial?.isFeatured ?? false,
    },
  });

  const submit = handleSubmit((values) => {
    const parsed = schema.safeParse(values);
    if (!parsed.success) {
      for (const issue of parsed.error.issues) {
        const key = issue.path[0] as keyof FormValues;
        setError(key, { message: issue.message });
      }
      return;
    }
    const d = parsed.data;
    const payload: PropertyPayload = {
      title: d.title,
      description: d.description,
      listingType: d.listingType,
      category: d.category,
      type: d.type,
      status: d.status || undefined,
      price: d.price,
      area: d.area,
      areaUnit: d.areaUnit || undefined,
      bedRooms: toNum(d.bedRooms),
      bathRooms: toNum(d.bathRooms),
      rooms: toNum(d.rooms),
      direction: d.direction || null,
      city: d.city,
      state: d.state || null,
      country: d.country || null,
      latitude: toNum(d.latitude),
      longitude: toNum(d.longitude),
      isFeatured: d.isFeatured,
    };
    return onSubmit(payload);
  });

  return (
    <form onSubmit={submit} className="space-y-8">
      <Section title="Basic details">
        <Field label="Title" error={errors.title?.message} className="sm:col-span-2">
          <Input {...register('title')} />
        </Field>
        <Field label="Description" error={errors.description?.message} className="sm:col-span-2">
          <Textarea rows={5} {...register('description')} />
        </Field>
        <Field label="Listing type" error={errors.listingType?.message}>
          <Select {...register('listingType')}>
            <option value="">Select…</option>
            {enums.listingType.map((o) => <option key={o.value} value={o.value}>{o.label}</option>)}
          </Select>
        </Field>
        <Field label="Category" error={errors.category?.message}>
          <Select {...register('category')}>
            <option value="">Select…</option>
            {enums.propertyCategory.map((o) => <option key={o.value} value={o.value}>{o.label}</option>)}
          </Select>
        </Field>
        <Field label="Type" error={errors.type?.message}>
          <Select {...register('type')}>
            <option value="">Select…</option>
            {enums.propertyType.map((o) => <option key={o.value} value={o.value}>{o.label}</option>)}
          </Select>
        </Field>
        <Field label="Status" error={errors.status?.message}>
          <Select {...register('status')}>
            <option value="">Default</option>
            {enums.propertyStatus.map((o) => <option key={o.value} value={o.value}>{o.label}</option>)}
          </Select>
        </Field>
      </Section>

      <Section title="Pricing & size">
        <Field label="Price (₹)" error={errors.price?.message}>
          <Input type="number" min="0" {...register('price')} />
        </Field>
        <Field label="Area" error={errors.area?.message}>
          <Input type="number" min="0" {...register('area')} />
        </Field>
        <Field label="Area unit" error={errors.areaUnit?.message}>
          <Select {...register('areaUnit')}>
            <option value="">Default</option>
            {enums.areaUnit.map((o) => <option key={o.value} value={o.value}>{o.label}</option>)}
          </Select>
        </Field>
        <Field label="Direction" error={errors.direction?.message}>
          <Select {...register('direction')}>
            <option value="">Not specified</option>
            {enums.direction.map((o) => <option key={o.value} value={o.value}>{o.label}</option>)}
          </Select>
        </Field>
        <Field label="Bedrooms" error={errors.bedRooms?.message}>
          <Input type="number" min="0" {...register('bedRooms')} />
        </Field>
        <Field label="Bathrooms" error={errors.bathRooms?.message}>
          <Input type="number" min="0" {...register('bathRooms')} />
        </Field>
        <Field label="Total rooms" error={errors.rooms?.message}>
          <Input type="number" min="0" {...register('rooms')} />
        </Field>
      </Section>

      <Section title="Location">
        <Field label="City" error={errors.city?.message}>
          <Input {...register('city')} />
        </Field>
        <Field label="State" error={errors.state?.message}>
          <Input {...register('state')} />
        </Field>
        <Field label="Country" error={errors.country?.message}>
          <Input {...register('country')} />
        </Field>
        <Field label="Latitude" error={errors.latitude?.message}>
          <Input type="number" step="any" {...register('latitude')} />
        </Field>
        <Field label="Longitude" error={errors.longitude?.message}>
          <Input type="number" step="any" {...register('longitude')} />
        </Field>
      </Section>

      <label className="flex items-center gap-2 text-sm">
        <input type="checkbox" className="h-4 w-4 rounded border-input" {...register('isFeatured')} />
        Mark as featured
      </label>

      <Button type="submit" loading={submitting} size="lg">
        {submitLabel}
      </Button>
    </form>
  );
}

function Section({ title, children }: { title: string; children: React.ReactNode }) {
  return (
    <fieldset>
      <legend className="mb-4 text-lg font-semibold">{title}</legend>
      <div className="grid gap-4 sm:grid-cols-2">{children}</div>
    </fieldset>
  );
}
