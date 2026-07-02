import { type ClassValue, clsx } from 'clsx';
import { twMerge } from 'tailwind-merge';

export function cn(...inputs: ClassValue[]) {
  return twMerge(clsx(inputs));
}

export function formatArea(area: number, unit: string): string {
  return `${new Intl.NumberFormat('en-IN').format(area)} ${unit}`;
}
