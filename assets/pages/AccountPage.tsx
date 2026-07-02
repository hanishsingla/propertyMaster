import { useRef, useState } from 'react';
import { useForm } from 'react-hook-form';
import { useQuery, useQueryClient } from '@tanstack/react-query';
import { useNavigate } from '@tanstack/react-router';
import { Camera, Trash2 } from 'lucide-react';
import { apiClient, ApiError } from '@/lib/apiClient';
import { applyViolations } from '@/lib/forms';
import { useEnums } from '@/lib/enums';
import { useAuth } from '@/features/auth/AuthProvider';
import { useToast } from '@/components/ui/toast';
import { RequireAuth } from '@/components/RequireAuth';
import { FullSpinner } from '@/components/ui/spinner';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import { Field } from '@/components/ui/label';
import { Input } from '@/components/ui/input';
import { Select } from '@/components/ui/select';
import { Button } from '@/components/ui/button';
import { Dialog } from '@/components/ui/dialog';
import type { UserSelf } from '@/lib/types';

interface ProfileValues {
  name: string;
  phone: string;
  mobile: string;
  gender: string;
  address: string;
  address2: string;
  city: string;
  state: string;
  zip: string;
  country: string;
}

function AccountInner() {
  const enums = useEnums();
  const toast = useToast();
  const queryClient = useQueryClient();
  const { refresh } = useAuth();
  const fileRef = useRef<HTMLInputElement>(null);
  const [uploading, setUploading] = useState(false);

  const { data: account, isLoading } = useQuery({
    queryKey: ['account'],
    queryFn: ({ signal }) => apiClient.get<UserSelf>('/api/account', signal),
  });

  if (isLoading || !account) return <FullSpinner />;

  return (
    <div className="container max-w-3xl py-8">
      <h1 className="mb-6 text-3xl font-bold">Account settings</h1>

      <Card className="mb-6">
        <CardHeader>
          <CardTitle>Avatar</CardTitle>
        </CardHeader>
        <CardContent className="flex items-center gap-4">
          {account.avatarUrl ? (
            <img src={account.avatarUrl} alt="" className="h-20 w-20 rounded-full object-cover" />
          ) : (
            <span className="flex h-20 w-20 items-center justify-center rounded-full bg-primary text-2xl font-semibold text-primary-foreground">
              {account.name.charAt(0).toUpperCase()}
            </span>
          )}
          <div>
            <input
              ref={fileRef}
              type="file"
              accept="image/*"
              className="hidden"
              onChange={async (e) => {
                const file = e.target.files?.[0];
                if (!file) return;
                setUploading(true);
                try {
                  const form = new FormData();
                  form.append('avatar', file);
                  await apiClient.postForm('/api/account/avatar', form);
                  await queryClient.invalidateQueries({ queryKey: ['account'] });
                  await refresh();
                  toast.success('Avatar updated');
                } catch {
                  toast.error('Could not upload avatar');
                } finally {
                  setUploading(false);
                  if (fileRef.current) fileRef.current.value = '';
                }
              }}
            />
            <Button variant="outline" onClick={() => fileRef.current?.click()} loading={uploading}>
              <Camera className="h-4 w-4" /> Change avatar
            </Button>
          </div>
        </CardContent>
      </Card>

      <ProfileForm account={account} genderOptions={enums.gender} />
      <ChangePasswordCard />
      <DeleteAccountCard />
    </div>
  );
}

function ProfileForm({
  account,
  genderOptions,
}: {
  account: UserSelf;
  genderOptions: { value: string; label: string }[];
}) {
  const toast = useToast();
  const queryClient = useQueryClient();
  const { refresh } = useAuth();
  const {
    register,
    handleSubmit,
    setError,
    formState: { errors, isSubmitting },
  } = useForm<ProfileValues>({
    defaultValues: {
      name: account.name ?? '',
      phone: account.phone ?? '',
      mobile: account.mobile ?? '',
      gender: account.gender ?? '',
      address: account.address ?? '',
      address2: account.address2 ?? '',
      city: account.city ?? '',
      state: account.state ?? '',
      zip: account.zip ?? '',
      country: account.country ?? '',
    },
  });

  const onSubmit = async (values: ProfileValues) => {
    try {
      await apiClient.patch('/api/account', values);
      await queryClient.invalidateQueries({ queryKey: ['account'] });
      await refresh();
      toast.success('Profile updated');
    } catch (err) {
      if (!applyViolations(err, setError)) {
        toast.error(err instanceof ApiError ? err.message : 'Could not update profile');
      }
    }
  };

  return (
    <Card className="mb-6">
      <CardHeader>
        <CardTitle>Profile</CardTitle>
      </CardHeader>
      <CardContent>
        <form onSubmit={handleSubmit(onSubmit)} className="grid gap-4 sm:grid-cols-2">
          <Field label="Name" error={errors.name?.message} htmlFor="name" className="sm:col-span-2">
            <Input id="name" {...register('name', { required: 'Name is required' })} />
          </Field>
          <Field label="Email">
            <Input value={account.email} disabled />
          </Field>
          <Field label="Gender">
            <Select {...register('gender')}>
              <option value="">Prefer not to say</option>
              {genderOptions.map((o) => (
                <option key={o.value} value={o.value}>{o.label}</option>
              ))}
            </Select>
          </Field>
          <Field label="Phone"><Input {...register('phone')} /></Field>
          <Field label="Mobile"><Input {...register('mobile')} /></Field>
          <Field label="Address" className="sm:col-span-2"><Input {...register('address')} /></Field>
          <Field label="Address line 2" className="sm:col-span-2"><Input {...register('address2')} /></Field>
          <Field label="City"><Input {...register('city')} /></Field>
          <Field label="State"><Input {...register('state')} /></Field>
          <Field label="ZIP / Postcode"><Input {...register('zip')} /></Field>
          <Field label="Country"><Input {...register('country')} /></Field>
          <div className="sm:col-span-2">
            <Button type="submit" loading={isSubmitting}>Save changes</Button>
          </div>
        </form>
      </CardContent>
    </Card>
  );
}

function ChangePasswordCard() {
  const toast = useToast();
  const {
    register,
    handleSubmit,
    reset,
    setError,
    formState: { errors, isSubmitting },
  } = useForm<{ currentPassword: string; newPassword: string }>();

  const onSubmit = async (values: { currentPassword: string; newPassword: string }) => {
    try {
      await apiClient.post('/api/account/change-password', values);
      toast.success('Password changed');
      reset();
    } catch (err) {
      if (!applyViolations(err, setError)) {
        toast.error('Could not change password');
      }
    }
  };

  return (
    <Card className="mb-6">
      <CardHeader>
        <CardTitle>Change password</CardTitle>
      </CardHeader>
      <CardContent>
        <form onSubmit={handleSubmit(onSubmit)} className="grid gap-4 sm:grid-cols-2">
          <Field label="Current password" error={errors.currentPassword?.message}>
            <Input type="password" autoComplete="current-password" {...register('currentPassword', { required: 'Required' })} />
          </Field>
          <Field label="New password" error={errors.newPassword?.message}>
            <Input type="password" autoComplete="new-password" {...register('newPassword', { required: 'Required', minLength: { value: 6, message: 'At least 6 characters' } })} />
          </Field>
          <div className="sm:col-span-2">
            <Button type="submit" loading={isSubmitting}>Update password</Button>
          </div>
        </form>
      </CardContent>
    </Card>
  );
}

function DeleteAccountCard() {
  const toast = useToast();
  const navigate = useNavigate();
  const { refresh } = useAuth();
  const [open, setOpen] = useState(false);
  const [password, setPassword] = useState('');
  const [busy, setBusy] = useState(false);

  const confirmDelete = async () => {
    setBusy(true);
    try {
      await apiClient.delete('/api/account', { password });
      await refresh();
      toast.success('Your account has been deleted');
      navigate({ to: '/' });
    } catch (err) {
      toast.error(err instanceof ApiError ? err.message : 'Could not delete account');
    } finally {
      setBusy(false);
      setOpen(false);
    }
  };

  return (
    <Card className="border-destructive/40">
      <CardHeader>
        <CardTitle className="text-destructive">Delete account</CardTitle>
      </CardHeader>
      <CardContent>
        <p className="mb-4 text-sm text-muted-foreground">
          Permanently delete your account and all associated data. This cannot be undone.
        </p>
        <Button variant="destructive" onClick={() => setOpen(true)}>
          <Trash2 className="h-4 w-4" /> Delete account
        </Button>

        <Dialog open={open} onClose={() => setOpen(false)} title="Confirm account deletion" description="Enter your password to confirm. This action is permanent.">
          <div className="space-y-4">
            <Input
              type="password"
              placeholder="Your password"
              value={password}
              onChange={(e) => setPassword(e.target.value)}
            />
            <div className="flex justify-end gap-2">
              <Button variant="outline" onClick={() => setOpen(false)}>Cancel</Button>
              <Button variant="destructive" onClick={confirmDelete} loading={busy} disabled={!password}>
                Delete permanently
              </Button>
            </div>
          </div>
        </Dialog>
      </CardContent>
    </Card>
  );
}

export function AccountPage() {
  return (
    <RequireAuth>
      <AccountInner />
    </RequireAuth>
  );
}
