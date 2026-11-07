import clsx from 'clsx';
import type { ButtonHTMLAttributes } from 'react';
import { buttonClass, type ButtonVariant } from '@/components/ui/styles';

type ButtonProps = ButtonHTMLAttributes<HTMLButtonElement> & {
    variant?: ButtonVariant;
};

export default function Button({ variant = 'primary', className, type = 'button', ...props }: ButtonProps) {
    return <button type={type} className={clsx(buttonClass(variant), className)} {...props} />;
}
