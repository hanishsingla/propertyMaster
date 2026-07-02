import type { UseFormSetError, FieldValues, Path } from 'react-hook-form';
import { ApiError } from './apiClient';

/**
 * Maps API `violations` onto react-hook-form field errors. Returns true when the
 * error was a validation error that got mapped, false otherwise (caller can toast).
 */
export function applyViolations<T extends FieldValues>(err: unknown, setError: UseFormSetError<T>): boolean {
  if (err instanceof ApiError && err.violations && err.violations.length > 0) {
    for (const v of err.violations) {
      setError(v.field as Path<T>, { message: v.message });
    }
    return true;
  }
  return false;
}
