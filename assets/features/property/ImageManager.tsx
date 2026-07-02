import { useRef, useState } from 'react';
import { useQueryClient } from '@tanstack/react-query';
import { Upload, Star, Trash2, ArrowUp, ArrowDown } from 'lucide-react';
import { uploadImages, updateImages, deleteImage } from './api';
import { useToast } from '@/components/ui/toast';
import { Button } from '@/components/ui/button';
import { Spinner } from '@/components/ui/spinner';
import { cn } from '@/lib/utils';
import type { PropertyImage } from '@/lib/types';

export function ImageManager({ propertyId, images }: { propertyId: number; images: PropertyImage[] }) {
  const toast = useToast();
  const queryClient = useQueryClient();
  const fileRef = useRef<HTMLInputElement>(null);
  const [busy, setBusy] = useState(false);

  const sorted = [...images].sort((a, b) => a.sortOrder - b.sortOrder);

  const invalidate = () => {
    queryClient.invalidateQueries({ queryKey: ['my-properties'] });
    queryClient.invalidateQueries({ queryKey: ['property'] });
  };

  const onUpload = async (files: FileList | null) => {
    if (!files || files.length === 0) return;
    setBusy(true);
    try {
      await uploadImages(propertyId, files);
      invalidate();
      toast.success('Images uploaded');
    } catch {
      toast.error('Upload failed');
    } finally {
      setBusy(false);
      if (fileRef.current) fileRef.current.value = '';
    }
  };

  const setCover = async (imageId: number) => {
    setBusy(true);
    try {
      await updateImages(propertyId, [{ imageId, isCover: true }]);
      invalidate();
    } catch {
      toast.error('Could not set cover');
    } finally {
      setBusy(false);
    }
  };

  const move = async (index: number, dir: -1 | 1) => {
    const target = index + dir;
    if (target < 0 || target >= sorted.length) return;
    const a = sorted[index];
    const b = sorted[target];
    setBusy(true);
    try {
      await updateImages(propertyId, [
        { imageId: a.id, sortOrder: b.sortOrder },
        { imageId: b.id, sortOrder: a.sortOrder },
      ]);
      invalidate();
    } catch {
      toast.error('Could not reorder');
    } finally {
      setBusy(false);
    }
  };

  const remove = async (imageId: number) => {
    setBusy(true);
    try {
      await deleteImage(propertyId, imageId);
      invalidate();
    } catch {
      toast.error('Could not delete image');
    } finally {
      setBusy(false);
    }
  };

  return (
    <div className="space-y-4">
      <div className="flex items-center justify-between">
        <div className="flex items-center gap-2">
          <h3 className="font-semibold">Images</h3>
          {busy && <Spinner className="h-4 w-4" />}
        </div>
        <input
          ref={fileRef}
          type="file"
          accept="image/*"
          multiple
          className="hidden"
          onChange={(e) => onUpload(e.target.files)}
        />
        <Button type="button" variant="outline" size="sm" onClick={() => fileRef.current?.click()} disabled={busy}>
          <Upload className="h-4 w-4" /> Upload
        </Button>
      </div>

      {sorted.length === 0 ? (
        <p className="rounded-lg border border-dashed py-8 text-center text-sm text-muted-foreground">
          No images yet. Upload some to showcase this property.
        </p>
      ) : (
        <div className="grid grid-cols-2 gap-3 sm:grid-cols-3 md:grid-cols-4">
          {sorted.map((img, i) => (
            <div key={img.id} className={cn('group relative overflow-hidden rounded-lg border', img.isCover && 'ring-2 ring-primary')}>
              <img src={img.url} alt="" className="aspect-square w-full object-cover" />
              {img.isCover && (
                <span className="absolute left-1 top-1 rounded bg-primary px-1.5 py-0.5 text-[10px] font-semibold text-primary-foreground">
                  Cover
                </span>
              )}
              <div className="absolute inset-x-0 bottom-0 flex justify-center gap-1 bg-black/50 p-1 opacity-0 transition-opacity group-hover:opacity-100">
                <IconBtn label="Set as cover" onClick={() => setCover(img.id)} disabled={busy || img.isCover}>
                  <Star className="h-3.5 w-3.5" />
                </IconBtn>
                <IconBtn label="Move up" onClick={() => move(i, -1)} disabled={busy || i === 0}>
                  <ArrowUp className="h-3.5 w-3.5" />
                </IconBtn>
                <IconBtn label="Move down" onClick={() => move(i, 1)} disabled={busy || i === sorted.length - 1}>
                  <ArrowDown className="h-3.5 w-3.5" />
                </IconBtn>
                <IconBtn label="Delete" onClick={() => remove(img.id)} disabled={busy}>
                  <Trash2 className="h-3.5 w-3.5" />
                </IconBtn>
              </div>
            </div>
          ))}
        </div>
      )}
    </div>
  );
}

function IconBtn({
  children,
  label,
  onClick,
  disabled,
}: {
  children: React.ReactNode;
  label: string;
  onClick: () => void;
  disabled?: boolean;
}) {
  return (
    <button
      type="button"
      aria-label={label}
      title={label}
      onClick={onClick}
      disabled={disabled}
      className="flex h-7 w-7 items-center justify-center rounded bg-white text-foreground hover:bg-secondary disabled:opacity-40"
    >
      {children}
    </button>
  );
}
