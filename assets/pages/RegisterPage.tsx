import { useForm } from 'react-hook-form';
import { useNavigate, Link } from '@tanstack/react-router';
import { apiClient, ApiError } from '@/lib/apiClient';
import { applyViolations } from '@/lib/forms';
import { useAuth } from '@/features/auth/AuthProvider';
import { useToast } from '@/components/ui/toast';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import { Field } from '@/components/ui/label';
import { Input } from '@/components/ui/input';
import { Button } from '@/components/ui/button';

interface FormValues {
  name: string;
  email: string;
  password: string;
  isAgent: boolean;
}

export function RegisterPage() {
  const navigate = useNavigate();
  const { refresh } = useAuth();
  const toast = useToast();
  const {
    register,
    handleSubmit,
    setError,
    formState: { errors, isSubmitting },
  } = useForm<FormValues>({ defaultValues: { isAgent: false } });

  const onSubmit = async (values: FormValues) => {
    try {
      await apiClient.post('/api/auth/register', values);
      await refresh();
      toast.success('Account created! Please verify your email.');
      navigate({ to: '/' });
    } catch (err) {
      if (err instanceof ApiError && err.code === 'email_exists') {
        setError('email', { message: 'An account with this email already exists.' });
      } else if (!applyViolations(err, setError)) {
        toast.error(err instanceof ApiError ? err.message : 'Registration failed.');
      }
    }
  };

  return (
    <div className="container flex justify-center py-12">
      <Card className="w-full max-w-md">
        <CardHeader>
          <CardTitle className="text-2xl">Create your account</CardTitle>
          <p className="text-sm text-muted-foreground">Join PropertyMaster to save and list properties.</p>
        </CardHeader>
        <CardContent>
          <form onSubmit={handleSubmit(onSubmit)} className="space-y-4">
            <Field label="Full name" error={errors.name?.message} htmlFor="name">
              <Input id="name" {...register('name', { required: 'Name is required' })} />
            </Field>
            <Field label="Email" error={errors.email?.message} htmlFor="email">
              <Input id="email" type="email" autoComplete="email" {...register('email', { required: 'Email is required' })} />
            </Field>
            <Field label="Password" error={errors.password?.message} htmlFor="password">
              <Input
                id="password"
                type="password"
                autoComplete="new-password"
                {...register('password', { required: 'Password is required', minLength: { value: 6, message: 'At least 6 characters' } })}
              />
            </Field>
            <label className="flex items-center gap-2 text-sm">
              <input type="checkbox" className="h-4 w-4 rounded border-input" {...register('isAgent')} />
              I am a real-estate agent (I want to list properties)
            </label>
            <Button type="submit" className="w-full" loading={isSubmitting}>
              Create account
            </Button>
          </form>
          <p className="mt-6 text-center text-sm text-muted-foreground">
            Already have an account?{' '}
            <Link to="/login" className="font-medium text-primary hover:underline">
              Sign in
            </Link>
          </p>
        </CardContent>
      </Card>
    </div>
  );
}
