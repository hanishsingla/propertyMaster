import { useForm } from 'react-hook-form';
import { Mail, MapPin, Phone } from 'lucide-react';
import { apiClient, ApiError } from '@/lib/apiClient';
import { applyViolations } from '@/lib/forms';
import { useToast } from '@/components/ui/toast';
import { Card, CardContent } from '@/components/ui/card';
import { Field } from '@/components/ui/label';
import { Input } from '@/components/ui/input';
import { Textarea } from '@/components/ui/textarea';
import { Button } from '@/components/ui/button';

interface FormValues {
  name: string;
  email: string;
  message: string;
  website: string; // honeypot
}

export function ContactPage() {
  const toast = useToast();
  const {
    register,
    handleSubmit,
    reset,
    setError,
    formState: { errors, isSubmitting },
  } = useForm<FormValues>({ defaultValues: { website: '' } });

  const onSubmit = async (values: FormValues) => {
    try {
      await apiClient.post('/api/contact', values);
      toast.success('Thanks! Your message has been sent.');
      reset({ name: '', email: '', message: '', website: '' });
    } catch (err) {
      if (!applyViolations(err, setError)) {
        toast.error(err instanceof ApiError ? err.message : 'Could not send message.');
      }
    }
  };

  return (
    <div className="container grid gap-8 py-12 md:grid-cols-2">
      <div>
        <h1 className="text-3xl font-bold">Get in touch</h1>
        <p className="mt-2 text-muted-foreground">
          Have a question about a property or your account? Send us a message and we'll get back to you.
        </p>
        <div className="mt-8 space-y-4 text-sm">
          <div className="flex items-center gap-3">
            <span className="flex h-10 w-10 items-center justify-center rounded-lg bg-primary/10 text-primary">
              <Mail className="h-5 w-5" />
            </span>
            <span>hello@propertymaster.example</span>
          </div>
          <div className="flex items-center gap-3">
            <span className="flex h-10 w-10 items-center justify-center rounded-lg bg-primary/10 text-primary">
              <Phone className="h-5 w-5" />
            </span>
            <span>+91 00000 00000</span>
          </div>
          <div className="flex items-center gap-3">
            <span className="flex h-10 w-10 items-center justify-center rounded-lg bg-primary/10 text-primary">
              <MapPin className="h-5 w-5" />
            </span>
            <span>PropertyMaster HQ, India</span>
          </div>
        </div>
      </div>

      <Card>
        <CardContent className="p-6">
          <form onSubmit={handleSubmit(onSubmit)} className="space-y-4">
            <Field label="Name" error={errors.name?.message} htmlFor="name">
              <Input id="name" {...register('name', { required: 'Name is required' })} />
            </Field>
            <Field label="Email" error={errors.email?.message} htmlFor="email">
              <Input id="email" type="email" {...register('email', { required: 'Email is required' })} />
            </Field>
            <Field label="Message" error={errors.message?.message} htmlFor="message">
              <Textarea id="message" rows={5} {...register('message', { required: 'Message is required' })} />
            </Field>
            {/* Honeypot: hidden from users, must remain empty */}
            <div className="hidden" aria-hidden>
              <label>
                Website
                <input type="text" tabIndex={-1} autoComplete="off" {...register('website')} />
              </label>
            </div>
            <Button type="submit" className="w-full" loading={isSubmitting}>
              Send message
            </Button>
          </form>
        </CardContent>
      </Card>
    </div>
  );
}
