import { useForm } from 'react-hook-form';
import { useParams, useNavigate, Link } from '@tanstack/react-router';
import { apiClient, ApiError } from '@/lib/apiClient';
import { useToast } from '@/components/ui/toast';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import { Field } from '@/components/ui/label';
import { Input } from '@/components/ui/input';
import { Button } from '@/components/ui/button';

interface FormValues {
  password: string;
  confirm: string;
}

export function ResetPasswordPage() {
  const { token } = useParams({ strict: false }) as { token: string };
  const navigate = useNavigate();
  const toast = useToast();
  const {
    register,
    handleSubmit,
    watch,
    setError,
    formState: { errors, isSubmitting },
  } = useForm<FormValues>();

  const onSubmit = async (values: FormValues) => {
    try {
      await apiClient.post('/api/auth/reset-password/reset', { token, password: values.password });
      toast.success('Password updated. You can now sign in.');
      navigate({ to: '/login' });
    } catch (err) {
      if (err instanceof ApiError && err.code === 'invalid_token') {
        setError('password', { message: 'This reset link is invalid or has expired.' });
      } else {
        toast.error('Could not reset password.');
      }
    }
  };

  return (
    <div className="container flex justify-center py-12">
      <Card className="w-full max-w-md">
        <CardHeader>
          <CardTitle className="text-2xl">Set a new password</CardTitle>
        </CardHeader>
        <CardContent>
          <form onSubmit={handleSubmit(onSubmit)} className="space-y-4">
            <Field label="New password" error={errors.password?.message} htmlFor="password">
              <Input
                id="password"
                type="password"
                autoComplete="new-password"
                {...register('password', { required: 'Password is required', minLength: { value: 6, message: 'At least 6 characters' } })}
              />
            </Field>
            <Field label="Confirm password" error={errors.confirm?.message} htmlFor="confirm">
              <Input
                id="confirm"
                type="password"
                autoComplete="new-password"
                {...register('confirm', {
                  required: 'Please confirm your password',
                  validate: (v) => v === watch('password') || 'Passwords do not match',
                })}
              />
            </Field>
            <Button type="submit" className="w-full" loading={isSubmitting}>
              Update password
            </Button>
          </form>
          <p className="mt-6 text-center text-sm text-muted-foreground">
            <Link to="/login" className="font-medium text-primary hover:underline">
              Back to login
            </Link>
          </p>
        </CardContent>
      </Card>
    </div>
  );
}
