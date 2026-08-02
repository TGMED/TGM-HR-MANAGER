import { computed, unref } from 'vue';
import type { ComputedRef, Ref } from 'vue';

export const PASSWORD_MIN_LENGTH = 8;

export type PasswordRule = {
    key: string;
    label: string;
    test: (value: string) => boolean;
};

/**
 * These mirror `Password::defaults()` in AppServiceProvider one for one — same
 * order, same Unicode classes Laravel's validator uses. If the server rules
 * change, change these with them or the checklist starts lying.
 *
 * The production-only `uncompromised()` breach check is deliberately absent:
 * it needs a network call, so it stays a server-side error.
 */
export const passwordRules: PasswordRule[] = [
    {
        key: 'length',
        label: `At least ${PASSWORD_MIN_LENGTH} characters`,
        // Count code points, not UTF-16 units, to match PHP's mb_strlen.
        test: (value) => [...value].length >= PASSWORD_MIN_LENGTH,
    },
    {
        key: 'mixedCase',
        label: 'An uppercase and a lowercase letter',
        test: (value) => /(\p{Ll}+.*\p{Lu})|(\p{Lu}+.*\p{Ll})/u.test(value),
    },
    {
        key: 'numbers',
        label: 'A number',
        test: (value) => /\p{N}/u.test(value),
    },
    {
        key: 'symbols',
        label: 'A symbol, such as ! or @',
        test: (value) => /\p{Z}|\p{S}|\p{P}/u.test(value),
    },
];

export type PasswordRuleState = PasswordRule & { met: boolean };

export function usePasswordRules(password: Ref<string> | (() => string)): {
    rules: ComputedRef<PasswordRuleState[]>;
    satisfied: ComputedRef<boolean>;
} {
    const value = computed(() =>
        typeof password === 'function' ? password() : unref(password),
    );

    const rules = computed<PasswordRuleState[]>(() =>
        passwordRules.map((rule) => ({ ...rule, met: rule.test(value.value) })),
    );

    const satisfied = computed(() => rules.value.every((rule) => rule.met));

    return { rules, satisfied };
}
