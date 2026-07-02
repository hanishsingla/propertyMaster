import { useState } from 'react';
import { useQuery, useQueryClient } from '@tanstack/react-query';
import { useParams, useNavigate, Link } from '@tanstack/react-router';
import { ArrowLeft } from 'lucide-react';
import { ApiError } from '@/lib/apiClient';
import { createProperty, updateProperty, fetchProperty, type PropertyPayload } from '@/features/property/api';
import type { PropertyDetail } from '@/lib/types';
import { RequireAuth } from '@/components/RequireAuth';
import { PropertyForm } from '@/features/property/PropertyForm';
import { ImageManager } from '@/features/property/ImageManager';
import { FullSpinner } from '@/components/ui/spinner';
import { Card, CardContent } from '@/components/ui/card';
import { useToast } from '@/components/ui/toast';
import { useAuth } from '@/features/auth/AuthProvider';
import { EmptyState } from '@/components/EmptyState';

function CreateForm() {
  const navigate = useNavigate();
  const toast = useToast();
  const queryClient = useQueryClient();
  const { user } = useAuth();
  const [submitting, setSubmitting] = useState(false);

  if (user && !user.isVerified) {
    return (
      <EmptyState
        title="Verify your email first"
        description="You must verify your email address before you can create listings. Use the banner at the top of the page to resend the verification email."
      />
    );
  }

  const onSubmit = async (payload: PropertyPayload) => {
    setSubmitting(true);
    try {
      const created = await createProperty(payload);
      queryClient.invalidateQueries({ queryKey: ['my-properties'] });
      toast.success('Property created. You can now add images.');
      navigate({ to: '/my/properties/$id/edit', params: { id: String(created.id) } });
    } catch (err) {
      if (err instanceof ApiError && err.status === 403) {
        toast.error('Your email must be verified to create listings.');
      } else {
        toast.error(err instanceof ApiError ? err.message : 'Could not create property');
      }
    } finally {
      setSubmitting(false);
    }
  };

  return <PropertyForm submitting={submitting} onSubmit={onSubmit} submitLabel="Create property" />;
}

function EditForm({ id }: { id: string }) {
  const toast = useToast();
  const queryClient = useQueryClient();
  const [submitting, setSubmitting] = useState(false);

  const { data, isLoading, isError } = useQuery({
    queryKey: ['property', id],
    queryFn: ({ signal }) => fetchProperty(id, signal),
  });

  if (isLoading) return <FullSpinner />;
  if (isError || !data) {
    return <EmptyState title="Property not found" />;
  }

  const onSubmit = async (payload: PropertyPayload) => {
    setSubmitting(true);
    try {
      await updateProperty(Number(id), payload);
      queryClient.invalidateQueries({ queryKey: ['property', id] });
      queryClient.invalidateQueries({ queryKey: ['my-properties'] });
      toast.success('Property updated');
    } catch (err) {
      toast.error(err instanceof ApiError ? err.message : 'Could not update property');
    } finally {
      setSubmitting(false);
    }
  };

  // Re-read latest images from cache after mutations.
  const current = queryClient.getQueryData<PropertyDetail>(['property', id]) ?? data;

  return (
    <div className="space-y-8">
      <PropertyForm initial={data} submitting={submitting} onSubmit={onSubmit} submitLabel="Save changes" />
      <Card>
        <CardContent className="p-6">
          <ImageManager propertyId={Number(id)} images={current.images} />
        </CardContent>
      </Card>
    </div>
  );
}

function FormPageInner({ mode }: { mode: 'new' | 'edit' }) {
  const params = useParams({ strict: false }) as { id?: string };

  return (
    <div className="container max-w-3xl py-8">
      <Link to="/my/properties" className="mb-4 inline-flex items-center gap-1 text-sm text-muted-foreground hover:text-foreground">
        <ArrowLeft className="h-4 w-4" /> Back to my properties
      </Link>
      <h1 className="mb-6 text-3xl font-bold">{mode === 'new' ? 'New listing' : 'Edit listing'}</h1>
      {mode === 'new' ? <CreateForm /> : <EditForm id={params.id!} />}
    </div>
  );
}

export function PropertyFormPage({ mode }: { mode: 'new' | 'edit' }) {
  return (
    <RequireAuth requireAgent>
      <FormPageInner mode={mode} />
    </RequireAuth>
  );
}
